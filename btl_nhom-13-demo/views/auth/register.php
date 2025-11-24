<?php
require_once __DIR__ . '/../../functions/auth.php';
// Nếu đã đăng nhập thì chuyển hướng
if (isLoggedIn()) {
    header('Location: ../customer_home.php');
    exit();
}

$pageTitle = 'Đăng ký tài khoản';
require_once __DIR__ . '/../../includes/header.php';
?>
<div class="page-header">
    <h2 class="page-title">Đăng ký tài khoản khách</h2>
</div>

<?php if (isset($_GET['error'])): ?>
<div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
<?php endif; ?>

<?php if (isset($_GET['success'])): ?>
<div class="alert alert-success"><?php echo htmlspecialchars($_GET['success']); ?></div>
<?php endif; ?>

<form action="../../handle/register_process.php" method="POST" style="max-width:600px" onsubmit="return validateForm()">
    <div class="mb-3">
        <label class="form-label">Họ tên <span style="color: red;">*</span></label>
        <input type="text" name="full_name" class="form-control" required minlength="3" maxlength="100" placeholder="Nhập họ tên đầy đủ">
        <small class="form-text text-muted">Tối thiểu 3 ký tự</small>
    </div>

    <div class="mb-3">
        <label class="form-label">Tên đăng nhập <span style="color: red;">*</span></label>
        <input type="text" name="username" class="form-control" required minlength="4" maxlength="45" placeholder="Nhập tên đăng nhập">
        <small class="form-text text-muted">4-45 ký tự, không chứa khoảng trắng</small>
    </div>

    <div class="mb-3">
        <label class="form-label">Mật khẩu <span style="color: red;">*</span></label>
        <input type="password" name="password" id="password" class="form-control" required minlength="6" maxlength="255" placeholder="Nhập mật khẩu">
        <small class="form-text text-muted">Tối thiểu 6 ký tự</small>
    </div>

    <div class="mb-3">
        <label class="form-label">Xác nhận mật khẩu <span style="color: red;">*</span></label>
        <input type="password" name="password_confirm" id="password_confirm" class="form-control" required minlength="6" maxlength="255" placeholder="Xác nhận mật khẩu">
        <small class="form-text text-muted">Nhập lại mật khẩu để xác nhận</small>
    </div>

    <div class="mb-3">
        <label class="form-label">Số điện thoại</label>
        <input type="text" name="phone" class="form-control" maxlength="20" placeholder="0xxxxxxxxx">
        <small class="form-text text-muted">Định dạng: 0xxxxxxxxx (10 số)</small>
    </div>

    <div class="mb-3">
        <label class="form-label">Biển số xe</label>
        <input type="text" name="license_plate" class="form-control" maxlength="20" placeholder="VD: 43-ABC-1234">
        <small class="form-text text-muted">Biển số xe (không bắt buộc)</small>
    </div>

    <button type="submit" class="btn btn-primary">Đăng ký</button>
    <a href="../../index.php" class="btn btn-secondary">Quay lại</a>
</form>

<script>
function validateForm() {
    const password = document.getElementById('password').value;
    const passwordConfirm = document.getElementById('password_confirm').value;
    const phone = document.querySelector('input[name="phone"]').value;
    
    // Kiểm tra mật khẩu trùng khớp
    if (password !== passwordConfirm) {
        alert('Mật khẩu không trùng khớp!');
        return false;
    }
    
    // Kiểm tra số điện thoại (nếu nhập)
    if (phone && !/^0\d{9}$/.test(phone)) {
        alert('Số điện thoại phải có định dạng: 0xxxxxxxxx (10 số)');
        return false;
    }
    
    return true;
}
</script>

</div>
</body>
</html>
