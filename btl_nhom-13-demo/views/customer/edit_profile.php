a<?php
require_once __DIR__ . '/../../functions/auth.php';
checkLogin(__DIR__ . '/../../index.php');

// Yêu cầu role customer
if (getCurrentUser()['role'] !== 'customer') {
    header('Location: ../../index.php');
    exit();
}

// Support AJAX fragment mode — when loaded via the header SPA we only return the inner container
$isFragment = isset($_GET['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

$pageTitle = "Cập nhật thông tin";
// Force customer layout
$forceCustomerView = true;
require_once __DIR__ . '/../../functions/customer_functions.php';

// Nếu load trực tiếp thì redirect qua shell
if (!$isFragment) {
    $self = str_replace('\\', '/', $_SERVER['REQUEST_URI']);
    if (empty($_GET['from_shell'])) {
        $shellUrl = '/btl_nhom-13-demo/views/customer_home.php?load=' . urlencode($self);
        header('Location: ' . $shellUrl);
        exit();
    }
    require_once __DIR__ . '/../../includes/header.php';
}

// Lấy customer theo user
$user = getCurrentUser();
$customer = null;
if ($user && isset($user['id'])) {
    $customer = getCustomerByUserId(intval($user['id']));
}
?>

<!-- ====================== NỘI DUNG ========================= -->

<style>
.page-title {
    font-size: 32px !important;
    font-weight: 900 !important;
    color: #fff !important;
    margin: 0 !important;
    padding: 0 !important;
    margin-bottom: 490px !important;
}
</style>
<div class="container py-3">

    <!-- TIÊU ĐỀ ĐƯA LÊN CAO HƠN -->
    <h2 class="page-title mb-4" style="font-size: 28px; font-weight: 600; color: #ff7f50;">
        <i class="fas fa-edit"></i>
        Cập nhật thông tin
    </h2>

    <?php if ($customer): ?>
    <div class="card" style="max-width: 600px;">
        <div class="card-body">
            <form action="../../handle/customer_process.php" method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="id" value="<?php echo $customer['id']; ?>">
                
                <div class="mb-3">
                    <label class="form-label">Họ tên</label>
                    <input type="text" class="form-control" name="name" 
                           value="<?php echo htmlspecialchars($customer['name']); ?>" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" class="form-control" name="phone" 
                           value="<?php echo htmlspecialchars($customer['phone'] ?? ''); ?>">
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Biển số xe</label>
                    <input type="text" class="form-control" name="license_plate" 
                           value="<?php echo htmlspecialchars($customer['license_plate'] ?? ''); ?>">
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Lưu thay đổi
                    </button>
                    <a href="profile.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>
    <?php else: ?>
    <div class="alert alert-warning">
        Không tìm thấy thông tin khách hàng.
    </div>
    <?php endif; ?>
</div>

<?php if (!$isFragment) require_once __DIR__ . '/../../includes/footer.php'; ?>
