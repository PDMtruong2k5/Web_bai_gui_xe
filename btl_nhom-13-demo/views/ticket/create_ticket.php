<?php
require_once __DIR__ . '/../../functions/auth.php';
checkLogin(__DIR__ . '/../../index.php');
require_once __DIR__ . '/../../functions/ticket_functions.php';
require_once __DIR__ . '/../../functions/customer_functions.php';

$currentUser = getCurrentUser();
$pageTitle = 'Đăng ký vé mới';
include_once __DIR__ . '/../../includes/header.php';

// If the logged-in user is a non-admin customer, try to fetch their customer record
$customerForForm = null;
if ($currentUser && ($currentUser['role'] ?? '') !== 'admin') {
    $customerForForm = getCustomerByUserId(intval($currentUser['id'] ?? 0));
}

// If admin, keep the dropdown list
$customers = [];
if (($currentUser['role'] ?? '') === 'admin') {
    $customers = getAllCustomersForDropdown();
}
?>

<div class="page-card">
    <div class="card">
        <div class="card-body">
            <h3 class="card-title">Đăng ký vé mới</h3>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>

            <form action="../../handle/ticket_process.php" method="POST">
                <input type="hidden" name="action" value="create">

                <?php if ($customerForForm): ?>
                    <div class="mb-3">
                        <label class="form-label">Khách hàng</label>
                        <div class="form-control" style="background:#f8fafc;"><?= htmlspecialchars($customerForForm['name']) ?> — <?= htmlspecialchars($customerForForm['license_plate']) ?></div>
                        <input type="hidden" name="customer_id" value="<?= intval($customerForForm['id']) ?>">
                    </div>
                <?php else: ?>
                    <?php if (($currentUser['role'] ?? '') === 'admin'): ?>
                        <div class="mb-3">
                            <label for="customer_id" class="form-label">Khách hàng <span class="text-danger">*</span></label>
                            <select id="customer_id" name="customer_id" class="form-control" required>
                                <option value="">-- Chọn khách hàng --</option>
                                <?php foreach($customers as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?> — <?= htmlspecialchars($c['license_plate'] ?? '') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">Chưa có hồ sơ khách hàng liên kết với tài khoản của bạn. Vui lòng cập nhật <a href="../customer/edit_profile.php">hồ sơ</a> trước khi đặt vé.</div>
                    <?php endif; ?>
                <?php endif; ?>

                <div class="mb-3">
                    <label for="ticket_type" class="form-label">Loại vé <span class="text-danger">*</span></label>
                    <select id="ticket_type" name="ticket_type" class="form-control" required>
                        <option value="day">Vé ngày</option>
                        <option value="month">Vé tháng</option>
                        <option value="year">Vé năm</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="start_date" class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
                    <input type="date" id="start_date" name="start_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">Giá vé (VNĐ)</label>
                    <input type="number" id="price" name="price" class="form-control" min="0" step="1000" placeholder="Để trống để dùng giá mặc định">
                    <div class="form-text">Giá mặc định: Vé ngày: 10,000đ | Vé tháng: 200,000đ | Vé năm: 2,000,000đ</div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-plus me-1"></i> Đăng ký vé</button>
                    <a href="../ticket.php" class="btn btn-secondary">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Auto set price based on ticket type
    (function(){
        var ticketType = document.getElementById('ticket_type');
        var priceInput = document.getElementById('price');
        var defaultPrices = { 'day': 10000, 'month': 200000, 'year': 2000000 };
        if (ticketType && priceInput) {
            ticketType.addEventListener('change', function(){ priceInput.value = defaultPrices[this.value] || ''; });
            priceInput.value = defaultPrices[ticketType.value] || '';
        }
    })();
</script>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>


