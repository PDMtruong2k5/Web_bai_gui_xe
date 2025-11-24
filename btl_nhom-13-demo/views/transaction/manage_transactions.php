<?php
// Tệp view cho phần quản lý giao dịch
include_once '../../includes/header.php';
require_once '../../functions/transaction_functions.php';

// Xử lý filter
$filters = [];
if (isset($_GET['start_date'])) $filters['start_date'] = $_GET['start_date'];
if (isset($_GET['end_date'])) $filters['end_date'] = $_GET['end_date'];
if (isset($_GET['payment_method'])) $filters['payment_method'] = $_GET['payment_method'];
if (isset($_GET['status'])) $filters['status'] = $_GET['status'];
if (isset($_GET['vehicle_type'])) $filters['vehicle_type'] = $_GET['vehicle_type'];
if (isset($_GET['area_id'])) $filters['area_id'] = $_GET['area_id'];

// Lấy danh sách giao dịch với bộ lọc
$transactions = getTransactionsWithFilters($filters);

// Tính tổng doanh thu của các giao dịch đã lọc
$totalRevenue = array_reduce($transactions, function($carry, $item) {
    return $carry + ($item['status'] == 'paid' ? $item['amount'] : 0);
}, 0);
?>

<div class="container">
    <h2>Quản lý giao dịch thanh toán</h2>
    
    <!-- Form lọc -->
    <div class="filter-section">
        <form method="GET" class="filter-form">
            <div class="form-group">
                <label>Từ ngày:</label>
                <input type="date" name="start_date" value="<?php echo $_GET['start_date'] ?? ''; ?>">
            </div>
            <div class="form-group">
                <label>Đến ngày:</label>
                <input type="date" name="end_date" value="<?php echo $_GET['end_date'] ?? ''; ?>">
            </div>
            <div class="form-group">
                <label>Hình thức thanh toán:</label>
                <select name="payment_method">
                    <option value="">Tất cả</option>
                    <option value="cash" <?php echo ($_GET['payment_method'] ?? '') == 'cash' ? 'selected' : ''; ?>>Tiền mặt</option>
                    <option value="bank_transfer" <?php echo ($_GET['payment_method'] ?? '') == 'bank_transfer' ? 'selected' : ''; ?>>Chuyển khoản</option>
                    <option value="qr_code" <?php echo ($_GET['payment_method'] ?? '') == 'qr_code' ? 'selected' : ''; ?>>Quét mã QR</option>
                    <option value="monthly_card" <?php echo ($_GET['payment_method'] ?? '') == 'monthly_card' ? 'selected' : ''; ?>>Thẻ tháng</option>
                    <option value="e_wallet" <?php echo ($_GET['payment_method'] ?? '') == 'e_wallet' ? 'selected' : ''; ?>>Ví điện tử</option>
                </select>
            </div>
            <div class="form-group">
                <label>Trạng thái:</label>
                <select name="status">
                    <option value="">Tất cả</option>
                    <option value="pending" <?php echo ($_GET['status'] ?? '') == 'pending' ? 'selected' : ''; ?>>Chờ xác nhận</option>
                    <option value="paid" <?php echo ($_GET['status'] ?? '') == 'paid' ? 'selected' : ''; ?>>Đã thanh toán</option>
                    <option value="refunded" <?php echo ($_GET['status'] ?? '') == 'refunded' ? 'selected' : ''; ?>>Đã hoàn tiền</option>
                    <option value="cancelled" <?php echo ($_GET['status'] ?? '') == 'cancelled' ? 'selected' : ''; ?>>Đã hủy</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Lọc</button>
            <a href="?clear=1" class="btn btn-secondary">Xóa bộ lọc</a>
        </form>
    </div>

    <!-- Hiển thị tổng doanh thu -->
    <div class="revenue-summary">
        <h3>Tổng doanh thu: <?php echo number_format($totalRevenue, 0, ',', '.'); ?> VNĐ</h3>
    </div>

    <!-- Bảng danh sách giao dịch -->
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Mã vé/xe</th>
                <th>Khách hàng</th>
                <th>Hình thức TT</th>
                <th>Số tiền</th>
                <th>Thời gian</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $transaction): ?>
            <tr>
                <td><?php echo $transaction['id']; ?></td>
                <td>
                    <?php echo $transaction['vehicle_plate']; ?><br>
                    <small><?php echo $transaction['ticket_type']; ?></small>
                </td>
                <td><?php echo $transaction['customer_name']; ?></td>
                <td><?php 
                    $payment_methods = [
                        'cash' => 'Tiền mặt',
                        'bank_transfer' => 'Chuyển khoản',
                        'qr_code' => 'Quét mã QR',
                        'monthly_card' => 'Thẻ tháng',
                        'e_wallet' => 'Ví điện tử'
                    ];
                    echo $payment_methods[$transaction['payment_method']] ?? $transaction['payment_method'];
                ?></td>
                <td><?php echo number_format($transaction['amount'], 0, ',', '.'); ?> VNĐ</td>
                <td><?php echo date('d/m/Y H:i', strtotime($transaction['payment_time'])); ?></td>
                <td>
                    <?php
                    $status_classes = [
                        'pending' => 'status-pending',
                        'paid' => 'status-success',
                        'refunded' => 'status-warning',
                        'cancelled' => 'status-danger'
                    ];
                    $status_labels = [
                        'pending' => 'Chờ xác nhận',
                        'paid' => 'Đã thanh toán',
                        'refunded' => 'Đã hoàn tiền',
                        'cancelled' => 'Đã hủy'
                    ];
                    ?>
                    <span class="status <?php echo $status_classes[$transaction['status']] ?? ''; ?>">
                        <?php echo $status_labels[$transaction['status']] ?? $transaction['status']; ?>
                    </span>
                </td>
                <td>
                    <?php if ($transaction['status'] == 'pending'): ?>
                    <form method="post" action="../../handle/transaction_process.php" style="display: inline;">
                        <input type="hidden" name="transaction_id" value="<?php echo $transaction['id']; ?>">
                        <button type="submit" name="action" value="confirm_payment" class="btn btn-success btn-sm">
                            Xác nhận
                        </button>
                    </form>
                    <?php endif; ?>
                    
                    <?php if (in_array($transaction['status'], ['paid', 'pending'])): ?>
                    <form method="post" action="../../handle/transaction_process.php" style="display: inline;">
                        <input type="hidden" name="transaction_id" value="<?php echo $transaction['id']; ?>">
                        <button type="submit" name="action" value="refund_payment" class="btn btn-warning btn-sm">
                            Hoàn tiền
                        </button>
                    </form>
                    <?php endif; ?>
                    
                    <button onclick="printReceipt(<?php echo $transaction['id']; ?>)" class="btn btn-info btn-sm">
                        In hóa đơn
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<style>
.filter-section {
    background: #f8f9fa;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 5px;
}

.filter-form {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.form-group {
    margin-bottom: 10px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
}

.status {
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 0.85em;
}

.status-pending { background: #ffd700; color: #000; }
.status-success { background: #28a745; color: #fff; }
.status-warning { background: #ffc107; color: #000; }
.status-danger { background: #dc3545; color: #fff; }

.revenue-summary {
    background: #e9ecef;
    padding: 15px;
    margin: 20px 0;
    border-radius: 5px;
}

.btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    margin: 2px;
}
</style>

<script>
function printReceipt(transactionId) {
    // Implement in-browser printing functionality
    window.open(`print_receipt.php?id=${transactionId}`, '_blank', 'width=800,height=600');
}
</script>

<?php include_once '../../includes/footer.php'; ?>