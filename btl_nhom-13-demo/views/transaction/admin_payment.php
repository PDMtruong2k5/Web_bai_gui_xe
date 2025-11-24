<?php
// admin_payment.php - Giao diện quản lý thanh toán cho admin
$pageTitle = "Quản lý thanh toán";
include_once '../../includes/header.php';
include_once '../../functions/transaction_functions.php';
include_once '../../functions/ticket_functions.php';

// Kiểm tra quyền admin
if (!isset($currentUser) || $currentUser['role'] !== 'admin') {
    header("Location: /btl_nhom-13-demo/index.php");
    exit();
}

// Lấy danh sách giao dịch chưa thanh toán
$pendingTransactions = getPendingTransactions();

// Lấy danh sách giao dịch đã thanh toán (thành công)
// Giao dịch từ bảng transactions
$paidTransactions = getPaidTransactions();

// Ngoài ra lấy các vé (tickets) - coi vé đang active như các giao dịch đã thanh toán
$tickets = getAllTickets();
$ticketAsTransactions = [];
foreach ($tickets as $t) {
    // Bỏ qua vé bị hủy
    if (isset($t['status']) && $t['status'] === 'cancelled') continue;
    $ticketAsTransactions[] = [
        'id' => 'TICKET-' . $t['id'],
        'customer_name' => $t['customer_name'] ?? ('Customer ' . ($t['customer_id'] ?? '')),
        'amount' => isset($t['price']) ? $t['price'] : 0,
        'status' => 'paid'
    ];
}

// Gộp transactions đã thanh toán và vé thành một danh sách hiển thị
$paidTransactions = array_merge($paidTransactions, $ticketAsTransactions);

// Tính tổng số tiền chờ thanh toán
$totalPending = array_reduce($pendingTransactions, function($carry, $item) {
    return $carry + $item['amount'];
}, 0);

// Tính tổng số tiền đã thanh toán
$totalPaid = array_reduce($paidTransactions, function($carry, $item) { 
    return $carry + $item['amount']; 
}, 0);
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="payment-dashboard">
    <!-- Header -->
    <div class="dashboard-header">
        <div>
            <h1>Quản lý thanh toán</h1>
            <p>Theo dõi và xử lý các giao dịch của hệ thống</p>
        </div>
        <button class="btn-refresh" onclick="location.reload()">
            <i class="fas fa-sync-alt"></i>
            Làm mới
        </button>
    </div>

    <!-- Stats Overview -->
    <div class="stats-container">
        <div class="stat-box warning">
            <div class="stat-header">
                <i class="fas fa-clock"></i>
                <span>Chờ xử lý</span>
            </div>
            <div class="stat-value"><?php echo count($pendingTransactions); ?></div>
            <div class="stat-label">giao dịch</div>
        </div>

        <div class="stat-box info">
            <div class="stat-header">
                <i class="fas fa-money-bill-wave"></i>
                <span>Chờ thanh toán</span>
            </div>
            <div class="stat-value"><?php echo number_format($totalPending/1000, 0); ?>K</div>
            <div class="stat-label">VNĐ</div>
        </div>

        <div class="stat-box success">
            <div class="stat-header">
                <i class="fas fa-check-circle"></i>
                <span>Đã xử lý</span>
            </div>
            <div class="stat-value"><?php echo count($paidTransactions); ?></div>
            <div class="stat-label">giao dịch</div>
        </div>

        <div class="stat-box primary">
            <div class="stat-header">
                <i class="fas fa-wallet"></i>
                <span>Tổng thu</span>
            </div>
            <div class="stat-value"><?php echo number_format($totalPaid/1000, 0); ?>K</div>
            <div class="stat-label">VNĐ</div>
        </div>
    </div>

    <!-- Pending Transactions -->
    <div class="data-card">
        <div class="card-header">
            <div class="card-title">
                <i class="fas fa-hourglass-half"></i>
                <span>Giao dịch chờ xử lý</span>
                <span class="badge"><?php echo count($pendingTransactions); ?></span>
            </div>
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Tìm kiếm..." id="searchPending" onkeyup="searchTable('tablePending', 'searchPending')">
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($pendingTransactions)): ?>
                <div class="empty-box">
                    <i class="fas fa-check-circle"></i>
                    <p>Không có giao dịch chờ xử lý</p>
                </div>
            <?php else: ?>
                <table class="data-table" id="tablePending">
                    <thead>
                        <tr>
                            <th>Mã GD</th>
                            <th>Khách hàng</th>
                            <th>Số tiền</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendingTransactions as $trans): ?>
                        <tr>
                            <td><span class="trans-id">#<?php echo $trans['id']; ?></span></td>
                            <td>
                                <div class="user-cell">
                                    <div class="avatar"><?php echo strtoupper(substr($trans['customer_name'], 0, 1)); ?></div>
                                    <span><?php echo htmlspecialchars($trans['customer_name']); ?></span>
                                </div>
                            </td>
                            <td><strong><?php echo number_format($trans['amount'], 0, ',', '.'); ?> đ</strong></td>
                            <td><span class="status pending">Chờ xử lý</span></td>
                            <td>
                                <div class="btn-group">
                                    <form method="post" action="../../handle/transaction_process.php" style="display:inline-block;">
                                        <input type="hidden" name="transaction_id" value="<?php echo $trans['id']; ?>">
                                        <button type="submit" name="action" value="confirm_payment" class="btn-sm success">
                                            <i class="fas fa-check"></i> Xác nhận
                                        </button>
                                    </form>
                                    <button class="btn-sm info" onclick="viewDetails(<?php echo $trans['id']; ?>)">
                                        <i class="fas fa-eye"></i> Chi tiết
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Paid Transactions -->
    <div class="data-card">
        <div class="card-header">
            <div class="card-title">
                <i class="fas fa-check-double"></i>
                <span>Giao dịch đã thanh toán</span>
                <span class="badge success"><?php echo count($paidTransactions); ?></span>
            </div>
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Tìm kiếm..." id="searchPaid" onkeyup="searchTable('tablePaid', 'searchPaid')">
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($paidTransactions)): ?>
                <div class="empty-box">
                    <i class="fas fa-inbox"></i>
                    <p>Chưa có giao dịch nào</p>
                </div>
            <?php else: ?>
                <table class="data-table" id="tablePaid">
                    <thead>
                        <tr>
                            <th>Mã GD</th>
                            <th>Khách hàng</th>
                            <th>Số tiền</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($paidTransactions as $trans): ?>
                        <tr>
                            <td><span class="trans-id">#<?php echo $trans['id']; ?></span></td>
                            <td>
                                <div class="user-cell">
                                    <div class="avatar success"><?php echo strtoupper(substr($trans['customer_name'], 0, 1)); ?></div>
                                    <span><?php echo htmlspecialchars($trans['customer_name']); ?></span>
                                </div>
                            </td>
                            <td><strong><?php echo number_format($trans['amount'], 0, ',', '.'); ?> đ</strong></td>
                            <td><span class="status paid">Đã thanh toán</span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    background: #f5f7fa;
    color: #2d3748;
}

.payment-dashboard {
    max-width: 1320px;
    margin: 0 auto;
    padding: 32px 24px;
}

/* Header */
.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
}

.dashboard-header h1 {
    font-size: 28px;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 4px;
}

.dashboard-header p {
    font-size: 14px;
    color: #718096;
}

.btn-refresh {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    color: #4a5568;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-refresh:hover {
    border-color: #cbd5e0;
    background: #f7fafc;
}

/* Stats */
.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}

.stat-box {
    background: white;
    padding: 24px;
    border-radius: 12px;
    border-left: 4px solid;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.stat-box.warning { border-color: #f59e0b; }
.stat-box.info { border-color: #3b82f6; }
.stat-box.success { border-color: #10b981; }
.stat-box.primary { border-color: #8b5cf6; }

.stat-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
    color: #64748b;
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-header i {
    font-size: 16px;
}

.stat-value {
    font-size: 32px;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 4px;
}

.stat-label {
    font-size: 13px;
    color: #94a3b8;
}

/* Data Card */
.data-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    margin-bottom: 24px;
    overflow: hidden;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid #f1f5f9;
    background: #fafbfc;
}

.card-title {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 16px;
    font-weight: 600;
    color: #1a202c;
}

.card-title i {
    color: #64748b;
}

.badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 24px;
    height: 24px;
    padding: 0 8px;
    background: #e2e8f0;
    color: #475569;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.badge.success {
    background: #d1fae5;
    color: #065f46;
}

/* Search Box */
.search-box {
    position: relative;
}

.search-box i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 14px;
}

.search-box input {
    padding: 8px 12px 8px 36px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 14px;
    width: 240px;
    transition: all 0.2s;
}

.search-box input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Card Body */
.card-body {
    padding: 0;
}

/* Table */
.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table thead {
    background: #fafbfc;
}

.data-table th {
    padding: 16px 24px;
    text-align: left;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid #f1f5f9;
}

.data-table td {
    padding: 16px 24px;
    border-bottom: 1px solid #f1f5f9;
    font-size: 14px;
}

.data-table tbody tr:hover {
    background: #fafbfc;
}

/* Transaction ID */
.trans-id {
    font-family: 'Courier New', monospace;
    font-weight: 600;
    color: #6366f1;
}

/* User Cell */
.user-cell {
    display: flex;
    align-items: center;
    gap: 12px;
}

.avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
}

.avatar.success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

/* Status */
.status {
    display: inline-flex;
    align-items: center;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 500;
}

.status.pending {
    background: #fef3c7;
    color: #92400e;
}

.status.paid {
    background: #d1fae5;
    color: #065f46;
}

/* Button Group */
.btn-group {
    display: flex;
    gap: 8px;
}

.btn-sm {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 14px;
    border: none;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-sm.success {
    background: #10b981;
    color: white;
}

.btn-sm.success:hover {
    background: #059669;
}

.btn-sm.info {
    background: #3b82f6;
    color: white;
}

.btn-sm.info:hover {
    background: #2563eb;
}

/* Empty State */
.empty-box {
    text-align: center;
    padding: 60px 24px;
}

.empty-box i {
    font-size: 48px;
    color: #cbd5e1;
    margin-bottom: 16px;
}

.empty-box p {
    color: #64748b;
    font-size: 15px;
}

/* Responsive */
@media (max-width: 768px) {
    .dashboard-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }

    .stats-container {
        grid-template-columns: 1fr;
    }

    .card-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }

    .search-box input {
        width: 100%;
    }

    .data-table {
        font-size: 13px;
    }

    .data-table th,
    .data-table td {
        padding: 12px 16px;
    }

    .btn-group {
        flex-direction: column;
    }

    .btn-sm {
        width: 100%;
        justify-content: center;
    }
}
</style>

<script>
function viewDetails(transactionId) {
    alert('Chi tiết giao dịch #' + transactionId);
}

function searchTable(tableId, searchId) {
    const input = document.getElementById(searchId);
    const filter = input.value.toLowerCase();
    const table = document.getElementById(tableId);
    const rows = table.getElementsByTagName('tr');

    for (let i = 1; i < rows.length; i++) {
        const text = rows[i].textContent.toLowerCase();
        rows[i].style.display = text.includes(filter) ? '' : 'none';
    }
}
</script>

<?php include_once '../../includes/footer.php'; ?>