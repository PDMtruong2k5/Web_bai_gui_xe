<?php
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '/../../functions/payment_gateway.php';
require_once __DIR__ . '/../../functions/ticket_functions.php';
checkLogin(__DIR__ . '/../../index.php');

$user = getCurrentUser();
if (($user['role'] ?? '') !== 'admin') {
    header('Location: ../../index.php'); exit();
}

$payments = load_all_payments();

// Tính toán thống kê
$total_revenue = 0;
$paid_count = 0;
$pending_count = 0;
$awaiting_count = 0;

$chart_data = [];
foreach ($payments as $p) {
    if ($p['status'] === 'paid') {
        $total_revenue += $p['amount'];
        $paid_count++;
    } elseif ($p['status'] === 'pending') {
        $pending_count++;
    } elseif ($p['status'] === 'awaiting_confirmation') {
        $awaiting_count++;
    }
    
    $date = date('Y-m-d', strtotime($p['created_at']));
    if (!isset($chart_data[$date])) {
        $chart_data[$date] = ['count' => 0, 'amount' => 0];
    }
    if ($p['status'] === 'paid') {
        $chart_data[$date]['count']++;
        $chart_data[$date]['amount'] += $p['amount'];
    }
}

ksort($chart_data);

require_once __DIR__ . '/../../includes/header.php';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="payments-dashboard">
    <!-- Header -->
    <div class="dashboard-header">
        <div class="header-content">
            <div class="title-group">
                <h1>Quản lý Doanh thu / Thanh toán</h1>
                <p>Danh sách giao dịch thanh toán khách hàng</p>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon-wrapper green">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value"><?php echo number_format($total_revenue); ?> đ</div>
                <div class="stat-label">Tổng thu được</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-wrapper blue">
                <i class="fas fa-exchange-alt"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value"><?php echo $paid_count; ?></div>
                <div class="stat-label">Đã nhận thanh toán</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-wrapper orange">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value"><?php echo $awaiting_count; ?></div>
                <div class="stat-label">Chờ duyệt</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-wrapper red">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value"><?php echo $pending_count; ?></div>
                <div class="stat-label">Chưa thanh toán</div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-grid">
        <!-- Revenue Chart -->
        <div class="content-card">
            <div class="card-header">
                <h2>Xu hướng doanh thu</h2>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Transaction Chart -->
        <div class="content-card">
            <div class="card-header">
                <h2>Số lượng giao dịch</h2>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="transactionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Distribution -->
    <div class="content-card">
        <div class="card-header">
            <h2>Phân bố trạng thái thanh toán</h2>
        </div>
        <div class="card-body">
            <div class="chart-container-pie">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="content-card">
        <div class="card-header">
            <h2>Chi tiết giao dịch</h2>
            <span class="count-badge"><?php echo count($payments); ?></span>
        </div>
        <div class="card-body">
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Vé</th>
                            <th>Khách hàng</th>
                            <th>Số tiền</th>
                            <th>Trạng thái</th>
                            <th>Thời gian</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $p):
                            $ticket = getTicketById($p['ticket_id']);
                            $customerName = $ticket['customer_name'] ?? ($ticket['name'] ?? 'N/A');
                            $ticketLabel = $ticket ? sprintf('#%d (%s)', $ticket['id'], $ticket['ticket_type']) : 'N/A';
                        ?>
                        <tr>
                            <td><strong>#<?php echo htmlspecialchars($p['id']); ?></strong></td>
                            <td><?php echo htmlspecialchars($ticketLabel); ?></td>
                            <td><?php echo htmlspecialchars($customerName); ?></td>
                            <td>
                                <strong class="revenue-value"><?php echo number_format($p['amount'],0,',','.'); ?> đ</strong>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $p['status']; ?>">
                                    <?php
                                        $st = $p['status'];
                                        if ($st === 'paid') echo '<i class="fas fa-check"></i> Đã nhận';
                                        elseif ($st === 'awaiting_confirmation') echo '<i class="fas fa-clock"></i> Chờ duyệt';
                                        elseif ($st === 'pending') echo '<i class="fas fa-hourglass"></i> Chưa thanh toán';
                                        else echo htmlspecialchars($st);
                                    ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($p['created_at'])); ?></td>
                            <td>
                                <?php if ($p['status'] !== 'paid'): ?>
                                    <button class="btn-action btn-success" data-id="<?php echo $p['id']; ?>" onclick="markPaid(this)">
                                        <i class="fas fa-check"></i> Xác nhận
                                    </button>
                                <?php else: ?>
                                    <span class="badge-done">✓ Đã xử lý</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
:root {
    --white: #ffffff;
    --off-white: #fafbfc;
    --gray-50: #f8f9fa;
    --gray-100: #f1f3f5;
    --gray-200: #e9ecef;
    --gray-300: #dee2e6;
    --gray-400: #ced4da;
    --gray-500: #adb5bd;
    --gray-600: #868e96;
    --gray-700: #495057;
    --gray-800: #343a40;
    --gray-900: #212529;
    --blue: #4c6ef5;
    --purple: #7950f2;
    --red: #f03e3e;
    --green: #37b24d;
    --orange: #fd7e14;
    --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.02);
    --shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.08);
    --radius-sm: 8px;
    --radius: 12px;
    --radius-lg: 16px;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', Roboto, sans-serif;
    background: var(--gray-50);
    color: var(--gray-900);
    line-height: 1.6;
}

.payments-dashboard {
    max-width: 1400px;
    margin: 0 auto;
    padding: 40px 24px;
}

/* Header */
.dashboard-header {
    margin-bottom: 32px;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 24px;
}

.title-group h1 {
    font-size: 32px;
    font-weight: 700;
    letter-spacing: -0.5px;
    color: var(--gray-900);
    margin-bottom: 4px;
}

.title-group p {
    font-size: 15px;
    color: var(--gray-600);
}

/* Stats */
.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}

.stat-card {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 24px;
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    transition: all 0.2s;
}

.stat-card:hover {
    box-shadow: var(--shadow);
    transform: translateY(-2px);
}

.stat-icon-wrapper {
    width: 56px;
    height: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: var(--radius);
    font-size: 24px;
    flex-shrink: 0;
}

.stat-icon-wrapper.blue {
    background: rgba(76, 110, 245, 0.1);
    color: var(--blue);
}

.stat-icon-wrapper.green {
    background: rgba(55, 178, 77, 0.1);
    color: var(--green);
}

.stat-icon-wrapper.orange {
    background: rgba(253, 126, 20, 0.1);
    color: var(--orange);
}

.stat-icon-wrapper.red {
    background: rgba(240, 62, 62, 0.1);
    color: var(--red);
}

.stat-details {
    flex: 1;
}

.stat-value {
    font-size: 28px;
    font-weight: 700;
    color: var(--gray-900);
    line-height: 1;
    margin-bottom: 4px;
    word-break: break-word;
}

.stat-label {
    font-size: 14px;
    color: var(--gray-600);
    font-weight: 500;
}

/* Charts Grid */
.charts-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(550px, 1fr));
    gap: 24px;
    margin-bottom: 24px;
}

@media (max-width: 1200px) {
    .charts-grid {
        grid-template-columns: 1fr;
    }
}

/* Content Card */
.content-card {
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    margin-bottom: 24px;
    overflow: hidden;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 24px;
    border-bottom: 1px solid var(--gray-100);
}

.card-header h2 {
    font-size: 18px;
    font-weight: 600;
    color: var(--gray-900);
}

.count-badge {
    padding: 4px 12px;
    background: var(--gray-100);
    color: var(--gray-700);
    border-radius: 12px;
    font-size: 13px;
    font-weight: 600;
}

.card-body {
    padding: 24px;
}

/* Chart Container */
.chart-container {
    position: relative;
    height: 300px;
}

.chart-container-pie {
    position: relative;
    height: 350px;
    max-width: 400px;
    margin: 0 auto;
}

/* Table */
.table-wrapper {
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table thead {
    border-bottom: 1px solid var(--gray-200);
}

.data-table th {
    padding: 12px 16px;
    text-align: left;
    font-size: 12px;
    font-weight: 600;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.data-table td {
    padding: 16px;
    border-bottom: 1px solid var(--gray-100);
}

.data-table tbody tr:hover {
    background: var(--gray-50);
}

.revenue-value {
    color: var(--green);
    font-weight: 700;
}

/* Status Badges */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: var(--radius-sm);
    font-size: 13px;
    font-weight: 600;
}

.status-badge.status-paid {
    background: rgba(55, 178, 77, 0.1);
    color: var(--green);
}

.status-badge.status-awaiting_confirmation {
    background: rgba(253, 126, 20, 0.1);
    color: var(--orange);
}

.status-badge.status-pending {
    background: rgba(240, 62, 62, 0.1);
    color: var(--red);
}

/* Action Buttons */
.btn-action {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: var(--green);
    color: var(--white);
    border: none;
    border-radius: var(--radius-sm);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-action:hover {
    background: #2f9e4a;
    transform: translateY(-1px);
    box-shadow: var(--shadow);
}

.btn-success {
    background: var(--green);
}

.badge-done {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    background: rgba(55, 178, 77, 0.1);
    color: var(--green);
    border-radius: var(--radius-sm);
    font-size: 13px;
    font-weight: 600;
}

/* Responsive */
@media (max-width: 968px) {
    .stats-container {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 640px) {
    .payments-dashboard {
        padding: 24px 16px;
    }

    .stats-container {
        grid-template-columns: 1fr;
    }

    .stat-value {
        font-size: 24px;
    }

    .charts-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
// Chart Data
const chartData = <?php echo json_encode([
    'labels' => array_keys($chart_data),
    'revenues' => array_map(function($d) { return $d['amount']; }, $chart_data),
    'transactions' => array_map(function($d) { return $d['count']; }, $chart_data)
]); ?>;

// Revenue Chart
const revenueCtx = document.getElementById('revenueChart');
if (revenueCtx && chartData.labels.length > 0) {
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Doanh thu (đ)',
                data: chartData.revenues,
                borderColor: '#37b24d',
                backgroundColor: 'rgba(55, 178, 77, 0.05)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointBackgroundColor: '#37b24d',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    labels: { font: { size: 12, weight: '600' }, padding: 15 }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f3f5' },
                    ticks: {
                        font: { size: 11 },
                        callback: function(value) {
                            return new Intl.NumberFormat('vi-VN', {
                                style: 'currency',
                                currency: 'VND',
                                minimumFractionDigits: 0
                            }).format(value);
                        }
                    }
                },
                x: { grid: { display: false }, ticks: { font: { size: 11 } } }
            }
        }
    });
}

// Transaction Chart
const transactionCtx = document.getElementById('transactionChart');
if (transactionCtx && chartData.labels.length > 0) {
    new Chart(transactionCtx, {
        type: 'bar',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Số giao dịch',
                data: chartData.transactions,
                backgroundColor: '#4c6ef5',
                borderRadius: 6,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f3f5' },
                    ticks: { font: { size: 11 }, stepSize: 1 }
                },
                x: { grid: { display: false }, ticks: { font: { size: 11 } } }
            }
        }
    });
}

// Status Distribution Chart
const statusCtx = document.getElementById('statusChart');
if (statusCtx) {
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Đã nhận', 'Chờ duyệt', 'Chưa thanh toán'],
            datasets: [{
                data: [<?php echo $paid_count; ?>, <?php echo $awaiting_count; ?>, <?php echo $pending_count; ?>],
                backgroundColor: ['#37b24d', '#fd7e14', '#f03e3e'],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: { font: { size: 12 }, padding: 15 }
                }
            }
        }
    });
}
</script>

<script>
function markPaid(btn){
    var id = btn.getAttribute('data-id');
    if (!confirm('Xác nhận đã nhận tiền cho giao dịch #' + id + ' ?')) return;
    var fd = new FormData(); fd.append('action','admin_mark_paid'); fd.append('payment_id', id);
    var xhr = new XMLHttpRequest(); xhr.open('POST','/btl_nhom-13-demo/handle/payment_action.php',true);
    xhr.setRequestHeader('X-Requested-With','XMLHttpRequest');
    xhr.onload = function(){ try{ var r = JSON.parse(xhr.responseText); }catch(e){ r=null; }
        if (xhr.status===200 && r && r.success) { alert('Đã xác nhận'); window.location.reload(); }
        else alert((r && r.message) ? r.message : 'Không thể xác nhận');
    };
    xhr.onerror = function(){ alert('Lỗi kết nối'); };
    xhr.send(fd);
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
