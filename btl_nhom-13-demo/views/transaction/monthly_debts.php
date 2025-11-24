<?php
// Tệp view cho phần quản lý công nợ vé tháng
include_once '../../includes/header.php';
require_once '../../functions/transaction_functions.php';

// Lấy danh sách công nợ vé tháng
$monthlyDebts = getMonthlyTicketDebts();

// Tính tổng công nợ
$totalDebt = array_reduce($monthlyDebts, function($carry, $item) {
    return $carry + $item['amount'];
}, 0);
?>

<div class="container">
    <h2>Quản lý công nợ vé tháng</h2>

    <!-- Tổng quan công nợ -->
    <div class="debt-summary">
        <div class="summary-card">
            <h3>Tổng công nợ</h3>
            <p class="amount"><?php echo number_format($totalDebt, 0, ',', '.'); ?> VNĐ</p>
        </div>
        <div class="summary-card">
            <h3>Số khách hàng nợ</h3>
            <p class="amount"><?php echo count($monthlyDebts); ?></p>
        </div>
    </div>

    <!-- Danh sách công nợ -->
    <div class="debts-list">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Khách hàng</th>
                    <th>Biển số xe</th>
                    <th>Loại vé</th>
                    <th>Số tiền</th>
                    <th>Hạn thanh toán</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($monthlyDebts as $debt): ?>
                <tr class="<?php echo strtotime($debt['due_date']) < time() ? 'overdue' : ''; ?>">
                    <td><?php echo $debt['id']; ?></td>
                    <td><?php echo $debt['customer_name']; ?></td>
                    <td><?php echo $debt['vehicle_plate']; ?></td>
                    <td><?php echo $debt['ticket_type']; ?></td>
                    <td><?php echo number_format($debt['amount'], 0, ',', '.'); ?> VNĐ</td>
                    <td>
                        <?php 
                        echo date('d/m/Y', strtotime($debt['due_date']));
                        if (strtotime($debt['due_date']) < time()) {
                            echo ' <span class="badge badge-danger">Quá hạn</span>';
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        $status_labels = [
                            'pending' => 'Chưa thanh toán',
                            'overdue' => 'Quá hạn'
                        ];
                        echo $status_labels[$debt['status']] ?? $debt['status'];
                        ?>
                    </td>
                    <td>
                        <button onclick="recordPayment(<?php echo $debt['id']; ?>)" 
                                class="btn btn-success btn-sm">
                            Ghi nhận thanh toán
                        </button>
                        <button onclick="sendReminder(<?php echo $debt['id']; ?>)" 
                                class="btn btn-info btn-sm">
                            Gửi nhắc nhở
                        </button>
                        <button onclick="extendDueDate(<?php echo $debt['id']; ?>)" 
                                class="btn btn-warning btn-sm">
                            Gia hạn
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal ghi nhận thanh toán -->
<div class="modal" id="paymentModal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Ghi nhận thanh toán</h3>
        <form id="paymentForm" method="post" action="../../handle/transaction_process.php">
            <input type="hidden" name="action" value="record_monthly_payment">
            <input type="hidden" name="transaction_id" id="payment_transaction_id">
            
            <div class="form-group">
                <label>Số tiền thanh toán:</label>
                <input type="number" name="amount" required>
            </div>
            
            <div class="form-group">
                <label>Phương thức thanh toán:</label>
                <select name="payment_method" required>
                    <option value="cash">Tiền mặt</option>
                    <option value="bank_transfer">Chuyển khoản</option>
                    <option value="qr_code">Quét mã QR</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Ghi chú:</label>
                <textarea name="notes" rows="3"></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Xác nhận thanh toán</button>
        </form>
    </div>
</div>

<!-- Modal gia hạn -->
<div class="modal" id="extendModal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Gia hạn thời gian thanh toán</h3>
        <form id="extendForm" method="post" action="../../handle/transaction_process.php">
            <input type="hidden" name="action" value="extend_due_date">
            <input type="hidden" name="transaction_id" id="extend_transaction_id">
            
            <div class="form-group">
                <label>Ngày hạn mới:</label>
                <input type="date" name="new_due_date" required>
            </div>
            
            <div class="form-group">
                <label>Lý do gia hạn:</label>
                <textarea name="reason" rows="3" required></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Xác nhận gia hạn</button>
        </form>
    </div>
</div>

<style>
.debt-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.summary-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    text-align: center;
}

.summary-card .amount {
    font-size: 1.5em;
    font-weight: bold;
    margin: 10px 0;
    color: #dc3545;
}

tr.overdue {
    background-color: #fff3f3;
}

.badge {
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 0.8em;
}

.badge-danger {
    background-color: #dc3545;
    color: white;
}

.modal {
    display: none;
}

/* Inherit existing modal styles */
</style>

<script>
// Ghi nhận thanh toán
function recordPayment(transactionId) {
    const modal = document.getElementById('paymentModal');
    document.getElementById('payment_transaction_id').value = transactionId;
    modal.style.display = 'block';
}

// Gửi nhắc nhở
async function sendReminder(transactionId) {
    if (confirm('Bạn có chắc muốn gửi nhắc nhở cho giao dịch này?')) {
        try {
            const response = await fetch('../../handle/transaction_process.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=send_payment_reminder&transaction_id=${transactionId}`
            });
            const result = await response.json();
            if (result.success) {
                alert('Đã gửi nhắc nhở thành công!');
            } else {
                alert('Có lỗi xảy ra khi gửi nhắc nhở');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi gửi nhắc nhở');
        }
    }
}

// Gia hạn
function extendDueDate(transactionId) {
    const modal = document.getElementById('extendModal');
    document.getElementById('extend_transaction_id').value = transactionId;
    modal.style.display = 'block';
}

// Đóng modal
document.querySelectorAll('.close').forEach(closeBtn => {
    closeBtn.onclick = function() {
        this.closest('.modal').style.display = 'none';
    }
});

// Đóng modal khi click ngoài
window.onclick = function(event) {
    if (event.target.className === 'modal') {
        event.target.style.display = 'none';
    }
}
</script>

<?php include_once '../../includes/footer.php'; ?>