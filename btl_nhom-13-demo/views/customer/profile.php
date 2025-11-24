<?php
require_once __DIR__ . '/../../functions/auth.php';
checkLogin(__DIR__ . '/../../index.php');

// Yêu cầu role customer
if (getCurrentUser()['role'] !== 'customer') {
    header('Location: ../../index.php');
    exit();
}

$pageTitle = "Thông tin cá nhân";
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../functions/customer_functions.php';

// Lấy thông tin khách hàng từ session user
$user = getCurrentUser();
$customer = null;

// Tìm customer record dựa vào full_name từ users
$conn = getDbConnection();
$sql = "SELECT c.* FROM customers c 
        INNER JOIN users u ON u.full_name = c.name 
        WHERE u.id = ?";
$stmt = mysqli_prepare($conn, $sql);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $user['id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result && mysqli_num_rows($result) > 0) {
        $customer = mysqli_fetch_assoc($result);
    }
    mysqli_stmt_close($stmt);
}
mysqli_close($conn);
?>

<div class="container py-3">
    <h2 class="page-title mb-4" style="font-weight: 600;">
        <i class="fas fa-user"></i>
        Thông tin cá nhân
    </h2>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($_GET['success']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <?php if ($customer): ?>
                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Họ tên:</div>
                    <div class="col-md-9"><?php echo htmlspecialchars($customer['name']); ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Số điện thoại:</div>
                    <div class="col-md-9"><?php echo htmlspecialchars($customer['phone'] ?? 'Chưa cập nhật'); ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Biển số xe:</div>
                    <div class="col-md-9"><?php echo htmlspecialchars($customer['license_plate'] ?? 'Chưa cập nhật'); ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 fw-bold">Ngày đăng ký:</div>
                    <div class="col-md-9"><?php echo date('d/m/Y', strtotime($customer['created_at'])); ?></div>
                </div>

                <div class="mt-3">
                    <a href="edit_profile.php" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Cập nhật thông tin
                    </a>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">
                    Không tìm thấy thông tin khách hàng.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
