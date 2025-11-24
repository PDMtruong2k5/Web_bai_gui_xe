<?php
require_once __DIR__ . '/../../functions/auth.php';
checkLogin(__DIR__ . '/../../index.php');
requireRole('admin', __DIR__ . '/../../index.php');

$pageTitle = "Thống kê doanh thu";
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../functions/db_connection.php';

// Lấy thông số thống kê
$period = $_GET['period'] ?? 'monthly';
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// Query payments table
$conn = getDbConnection();
$s = $conn->real_escape_string($start_date);
$e = $conn->real_escape_string($end_date);
$sql = "SELECT DATE(paid_at) AS day, COUNT(*) AS cnt, COALESCE(SUM(amount),0) AS total FROM payments WHERE status='paid' AND DATE(paid_at) BETWEEN '".$s."' AND '".$e."' GROUP BY DATE(paid_at) ORDER BY DATE(paid_at)";
$res = mysqli_query($conn, $sql);
$revenue_stats = [];
$total_revenue = 0;
if ($res) {
    while ($r = mysqli_fetch_assoc($res)) {
        $revenue_stats[] = [
            'time_period' => $r['day'],
            'total_transactions' => intval($r['cnt']),
            'total_revenue' => floatval($r['total']),
        ];
        $total_revenue += floatval($r['total']);
    }
}
mysqli_close($conn);

$totalTransactions = array_sum(array_column($revenue_stats, 'total_transactions'));
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="revenue-dashboard">
    <!-- Header -->
    <div class="dashboard-header">
        <div class="header-content">
            <div class="title-group">
                <h1>Thống kê doanh thu</h1>
                <p>Báo cáo tổng hợp doanh thu từ các khoản thanh toán</p>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-card">
        <form method="GET" class="filter-form">
            <div class="form-group">
                <label class="form-label">Khoảng thời gian</label>
                <select name="period" class="form-input" onchange="this.form.submit()">
                    <option value="daily" <?php echo $period == 'daily' ? 'selected' : ''; ?>>Theo ngày</option>
                    <option value="weekly" <?php echo $period == 'weekly' ? 'selected' : ''; ?>>Theo tuần</option>
                    <option value="monthly" <?php echo $period == 'monthly' ? 'selected' : ''; ?>>Theo tháng</option>
                    <option value="yearly" <?php echo $period == 'yearly' ? 'selected' : ''; ?>>Theo năm</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Từ ngày</label>
                <input type="date" name="start_date" value="<?php echo htmlspecialchars($start_date); ?>" class="form-input">
            </div>
            
            <div class="form-group">
                <label class="form-label">Đến ngày</label>
                <input type="date" name="end_date" value="<?php echo htmlspecialchars($end_date); ?>" class="form-input">
            </div>
            
            <button type="submit" class="btn-filter">
                <i class="fas fa-filter"></i>
                Lọc
            </button>
        </form>
    </div>

    <!-- Stats Overview -->
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon-wrapper blue">
                <i class="fas fa-coins"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value"><?php echo number_format($total_revenue); ?> đ</div>
                <div class="stat-label">Tổng doanh thu</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-wrapper purple">
                <i class="fas fa-exchange-alt"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value"><?php echo $totalTransactions; ?></div>
                <div class="stat-label">Tổng giao dịch</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-wrapper green">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value"><?php echo $totalTransactions > 0 ? number_format($total_revenue / $totalTransactions) : 0; ?> đ</div>
                <div class="stat-label">Giá trị trung bình</div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-grid">
        <!-- Line Chart -->
        <div class="content-card">
            <div class="card-header">
                <h2>Xu hướng doanh thu</h2>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="lineChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Bar Chart -->
        <div class="content-card">
            <div class="card-header">
                <h2>Doanh thu theo ngày</h2>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="barChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Chart -->
    <div class="content-card">
        <div class="card-header">
            <h2>Số lượng giao dịch</h2>
        </div>
        <div class="card-body">
            <div class="chart-container-single">
                <canvas id="transactionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Revenue Details Table -->
    <div class="content-card">
        <div class="card-header">
            <h2>Chi tiết doanh thu</h2>
            <span class="count-badge"><?php echo count($revenue_stats); ?></span>
        </div>
        <div class="card-body">
            <?php if (empty($revenue_stats)): ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3>Chưa có dữ liệu</h3>
                    <p>Không có giao dịch nào trong khoảng thời gian này</p>
                </div>
            <?php else: ?>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Ngày</th>
                                <th>Số giao dịch</th>
                                <th>Doanh thu</th>
                                <th>Tỉ lệ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($revenue_stats as $stat): 
                                $percentage = $total_revenue > 0 ? ($stat['total_revenue'] / $total_revenue) * 100 : 0;
                            ?>
                            <tr>
                                <td>
                                    <strong><?php echo date('d/m/Y', strtotime($stat['time_period'])); ?></strong>
                                </td>
                                <td>
                                    <span class="transaction-badge">
                                        <i class="fas fa-exchange-alt"></i>
                                        <?php echo $stat['total_transactions']; ?>
                                    </span>
                                </td>
                                <td>
                                    <strong class="revenue-value"><?php echo number_format($stat['total_revenue'], 0, ',', '.'); ?> đ</strong>
                                </td>
                                <td>
                                    <div class="percentage-bar">
                                        <div class="percentage-fill" style="width: <?php echo min($percentage, 100); ?>%"></div>
                                    </div>
                                    <span class="percentage-text"><?php echo round($percentage, 1); ?>%</span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
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

.revenue-dashboard {
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

/* Filter Card */
.filter-card {
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    margin-bottom: 32px;
    padding: 24px;
}

.filter-form {
    display: flex;
    align-items: flex-end;
    gap: 16px;
    flex-wrap: wrap;
}

.form-group {
    flex: 1;
    min-width: 180px;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-700);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.form-input {
    width: 100%;
    padding: 12px 14px;
    background: var(--white);
    border: 1px solid var(--gray-300);
    border-radius: var(--radius-sm);
    font-size: 14px;
    color: var(--gray-900);
    transition: all 0.2s;
}

.form-input:focus {
    outline: none;
    border-color: var(--gray-900);
    box-shadow: 0 0 0 3px rgba(33, 37, 41, 0.1);
}

.btn-filter {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: var(--gray-900);
    color: var(--white);
    border: none;
    border-radius: var(--radius-sm);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-filter:hover {
    background: var(--gray-800);
    transform: translateY(-1px);
    box-shadow: var(--shadow);
}

/* Stats */
.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
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
}

.stat-icon-wrapper.blue {
    background: rgba(76, 110, 245, 0.1);
    color: var(--blue);
}

.stat-icon-wrapper.purple {
    background: rgba(121, 80, 242, 0.1);
    color: var(--purple);
}

.stat-icon-wrapper.green {
    background: rgba(55, 178, 77, 0.1);
    color: var(--green);
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

.chart-container-single {
    position: relative;
    height: 350px;
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

.transaction-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: rgba(76, 110, 245, 0.1);
    color: var(--blue);
    border-radius: var(--radius-sm);
    font-size: 13px;
    font-weight: 600;
}

.revenue-value {
    color: var(--green);
    font-weight: 700;
}

.percentage-bar {
    width: 100px;
    height: 6px;
    background: var(--gray-200);
    border-radius: 3px;
    overflow: hidden;
    margin-bottom: 6px;
}

.percentage-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--blue), var(--purple));
    border-radius: 3px;
    transition: width 0.3s;
}

.percentage-text {
    font-size: 12px;
    color: var(--gray-600);
    font-weight: 600;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 80px 24px;
}

.empty-icon {
    font-size: 64px;
    color: var(--gray-300);
    margin-bottom: 20px;
}

.empty-state h3 {
    font-size: 20px;
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: 8px;
}

.empty-state p {
    font-size: 15px;
    color: var(--gray-600);
    margin-bottom: 24px;
}

/* Responsive */
@media (max-width: 968px) {
    .filter-form {
        flex-direction: column;
        align-items: stretch;
    }

    .form-group,
    .btn-filter {
        width: 100%;
    }

    .stats-container {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 640px) {
    .revenue-dashboard {
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
    'labels' => array_map(function($s) { return date('d/m', strtotime($s['time_period'])); }, $revenue_stats),
    'revenues' => array_map(function($s) { return $s['total_revenue']; }, $revenue_stats),
    'transactions' => array_map(function($s) { return $s['total_transactions']; }, $revenue_stats)
]); ?>;

// Line Chart - Xu hướng doanh thu
const lineCtx = document.getElementById('lineChart');
if (lineCtx && chartData.labels.length > 0) {
    new Chart(lineCtx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Doanh thu (đ)',
                data: chartData.revenues,
                borderColor: '#4c6ef5',
                backgroundColor: 'rgba(76, 110, 245, 0.05)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointBackgroundColor: '#4c6ef5',
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
                    labels: {
                        font: { size: 12, weight: '600' },
                        padding: 15,
                        usePointStyle: true
                    }
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
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 } }
                }
            }
        }
    });
}

// Bar Chart - Doanh thu theo ngày
const barCtx = document.getElementById('barChart');
if (barCtx && chartData.labels.length > 0) {
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Doanh thu',
                data: chartData.revenues,
                backgroundColor: [
                    '#4c6ef5',
                    '#7950f2',
                    '#37b24d',
                    '#fd7e14',
                    '#f03e3e'
                ],
                borderRadius: 6,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
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
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 } }
                }
            }
        }
    });
}

// Transaction Chart - Số lượng giao dịch
const transactionCtx = document.getElementById('transactionChart');
if (transactionCtx && chartData.labels.length > 0) {
    new Chart(transactionCtx, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: [{
                label: 'Số giao dịch',
                data: chartData.transactions,
                borderColor: '#7950f2',
                backgroundColor: 'rgba(121, 80, 242, 0.05)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 5,
                pointBackgroundColor: '#7950f2',
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
                    labels: {
                        font: { size: 12, weight: '600' },
                        padding: 15,
                        usePointStyle: true
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f3f5' },
                    ticks: {
                        font: { size: 11 },
                        stepSize: 1
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11 } }
                }
            }
        }
    });
}
</script>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
