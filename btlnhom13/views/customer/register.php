<?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký khách hàng</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family:'Inter',sans-serif; background:#f7fafc; margin:0; }
        .container { max-width:480px; margin:60px auto; padding:0 16px; }
        .card { background:#fff; border-radius:16px; box-shadow:0 10px 30px rgba(0,0,0,.08); padding:24px; }
        h1 { margin:0 0 8px; font-size:22px; }
        .muted { color:#718096; margin-bottom:16px; }
        .form-group { margin-bottom:14px; }
        label { display:block; margin-bottom:8px; color:#4a5568; font-weight:600; }
        input { width:100%; padding:12px 14px; border:2px solid #e2e8f0; border-radius:12px; font-size:15px; background:#f9fbfd; }
        input:focus { outline:none; border-color:#667eea; background:#fff; box-shadow:0 0 0 3px rgba(102,126,234,.12); }
        button { width:100%; padding:12px 18px; border:none; background:#2f855a; color:#fff; font-weight:600; border-radius:12px; cursor:pointer; }
        .links { display:flex; justify-content:space-between; margin-top:12px; }
        .link { text-decoration:none; color:#2b6cb0; font-weight:600; }
        .alert { padding:10px 12px; border-radius:10px; margin-bottom:12px; font-size:14px; }
        .alert-danger { background:#fff5f5; color:#c53030; border-left:4px solid #fc8181; }
        .alert-success { background:#f0fff4; color:#22543d; border-left:4px solid #68d391; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>Đăng ký khách hàng</h1>
            <div class="muted">Tạo tài khoản để tra cứu và sử dụng dịch vụ.</div>
            <?php if (!empty($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            <?php if (!empty($_SESSION['success'])): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
            <?php endif; ?>
            <form action="/btlnhom13/handle/customer_register_process.php" method="POST">
                <div class="form-group">
                    <label for="username">Tên đăng nhập</label>
                    <input type="text" id="username" name="username" required />
                </div>
                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <input type="password" id="password" name="password" required />
                </div>
                <div class="form-group">
                    <label for="confirm">Xác nhận mật khẩu</label>
                    <input type="password" id="confirm" name="confirm" required />
                </div>
                <button type="submit" name="register">Đăng ký</button>
            </form>
            <div class="links">
                <a class="link" href="/btlnhom13/views/customer/login.php">Đăng nhập</a>
                <a class="link" href="/btlnhom13/views/customer/index.php">Trang khách hàng</a>
            </div>
        </div>
    </div>
</body>
</html>



