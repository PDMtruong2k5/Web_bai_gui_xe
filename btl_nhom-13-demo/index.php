<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Đăng nhập - GUIXE</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/btl_nhom-13-demo/assets/css/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #1a273e 0%, #293548 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .auth-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            max-width: 1200px;
            width: 100%;
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }

        .auth-sidebar {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 4rem 3rem;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 1.5rem;
            justify-content: center;
        }

        .logo-box {
            width: 250px;
            height: 250px;
            background: transparent;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .logo-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .brand-title {
            font-size: 32px;
            font-weight: 800;
            background: linear-gradient(135deg, #ff6b35 0%, #ff8c42 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .sidebar-content h2 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 1.5rem;
            line-height: 1.3;
        }

        .sidebar-content p {
            font-size: 16px;
            line-height: 1.8;
            color: #cbd5e1;
            margin-bottom: 2rem;
        }

        .feature-list {
            list-style: none;
        }

        .feature-list li {
            padding: 12px 0;
            font-size: 15px;
            color: #e2e8f0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .feature-list li::before {
            content: "✓";
            width: 24px;
            height: 24px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            flex-shrink: 0;
        }

        .auth-content {
            padding: 4rem 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .auth-header {
            margin-bottom: 2rem;
        }

        .auth-header h3 {
            font-size: 28px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }

        .auth-header p {
            color: #64748b;
            font-size: 15px;
        }

        .tabs {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            border-bottom: 2px solid #e2e8f0;
        }

        .tab-btn {
            background: none;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            font-weight: 600;
            color: #64748b;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            transition: all 0.3s;
        }

        .tab-btn.active {
            color: #ff6b35;
            border-bottom-color: #ff6b35;
        }

        .tab-btn:hover {
            color: #ff6b35;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.3s;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 0.5rem;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            font-size: 15px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            transition: all 0.3s;
            font-family: inherit;
        }

        .form-control:focus {
            outline: none;
            border-color: #ff6b35;
            box-shadow: 0 0 0 4px rgba(255, 107, 53, 0.1);
        }

        .btn {
            padding: 14px 28px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(135deg, #ff6b35 0%, #ff8c42 100%);
            color: white;
            width: 100%;
            box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 107, 53, 0.4);
        }

        .btn-link {
            background: none;
            color: #64748b;
            padding: 0;
            font-size: 14px;
            text-align: center;
            display: block;
            margin-top: 1rem;
        }

        .btn-link:hover {
            color: #ff6b35;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-size: 14px;
            border-left: 4px solid;
            animation: slideDown 0.3s;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            border-left-color: #ff6b35;
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-left: 4px solid #ff6b35;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            border-left-color: #10b981;
            color: #6ee7b7;
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-left: 4px solid #10b981;
        }

        @keyframes slideDown {
            from { 
                opacity: 0;
                transform: translateY(-10px);
            }
            to { 
                opacity: 1;
                transform: translateY(0);
            }
        }

        .footer-text {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #e2e8f0;
            color: #94a3b8;
            font-size: 14px;
        }

        @media (max-width: 968px) {
            .auth-container {
                grid-template-columns: 1fr;
            }

            .auth-sidebar {
                padding: 3rem 2rem;
            }

            .auth-content {
                padding: 3rem 2rem;
            }

            .sidebar-content h2 {
                font-size: 24px;
            }
        }

        @media (max-width: 640px) {
            body {
                padding: 1rem;
            }

            .auth-sidebar {
                padding: 2rem 1.5rem;
            }

            .auth-content {
                padding: 2rem 1.5rem;
            }

            .logo-section {
                margin-bottom: 2rem;
            }

            .brand-title {
                font-size: 24px;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-sidebar">
            <div class="logo-section">
                <div class="logo-box">
                    <img src="/btl_nhom-13-demo/images/LOGOTRANG.png" alt="GỬI XE">
                </div>
            </div>

            <div class="sidebar-content">
                <h2>Hệ thống quản lý bãi gửi xe thông minh</h2>
                <p>Giải pháp gửi xe hiện đại, an toàn và tiện lợi nhất Việt Nam. Quản lý xe của bạn chỉ với vài thao tác đơn giản.</p>
            </div>
        </div>

        <div class="auth-content">
            <div class="auth-header">
                <h3>Chào mừng bạn</h3>
                <p>Đăng nhập hoặc tạo tài khoản mới để bắt đầu</p>
            </div>

            <?php
                // Hiển thị thông báo lỗi/thành công
                if (isset($_GET['error'])) {
                    echo '<div class="alert alert-error">
                        ❌ ' . htmlspecialchars($_GET['error']) . '
                    </div>';
                }
                if (isset($_GET['success'])) {
                    echo '<div class="alert alert-success">
                        ✓ Đăng ký thành công! Vui lòng đăng nhập để tiếp tục.
                    </div>';
                }
                
                // Kiểm tra xem nên hiển thị tab nào
                $activeTab = 'login';
                if (isset($_GET['error']) || isset($_GET['success'])) {
                    $activeTab = 'register';
                }
            ?>

            <div class="tabs">
                <button class="tab-btn <?php echo ($activeTab === 'login' ? 'active' : ''); ?>" onclick="switchTab('login')">Đăng nhập</button>
                <button class="tab-btn <?php echo ($activeTab === 'register' ? 'active' : ''); ?>" onclick="switchTab('register')">Đăng ký</button>
            </div>

            <div id="login" class="tab-content <?php echo ($activeTab === 'login' ? 'active' : ''); ?>">
                <form action="/btl_nhom-13-demo/handle/login_process.php" method="POST">
                    <div class="form-group">
                        <label class="form-label">Tên đăng nhập</label>
                        <input class="form-control" type="text" name="username" placeholder="Nhập tên đăng nhập" required />
                    </div>
                    <div class="form-group">
                        <label class="form-label">Mật khẩu</label>
                        <input class="form-control" type="password" name="password" placeholder="Nhập mật khẩu" required />
                    </div>
                    <button class="btn btn-primary" type="submit" name="login">Đăng nhập</button>
                    <a class="btn btn-link" href="/btl_nhom-13-demo/handle/logout_process.php">Quên mật khẩu?</a>
                </form>
            </div>

            <div id="register" class="tab-content <?php echo ($activeTab === 'register' ? 'active' : ''); ?>">
                <form action="/btl_nhom-13-demo/handle/register_process.php" method="POST" onsubmit="return validateRegister()">
                    <div class="form-group">
                        <label class="form-label">Họ tên <span style="color: #ff6b35;">*</span></label>
                        <input class="form-control" type="text" name="full_name" placeholder="Nhập họ tên đầy đủ" minlength="3" maxlength="100" required />
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tên đăng nhập <span style="color: #ff6b35;">*</span></label>
                        <input class="form-control" type="text" name="username" placeholder="Chọn tên đăng nhập" minlength="4" maxlength="45" required />
                    </div>
                    <div class="form-group">
                        <label class="form-label">Mật khẩu <span style="color: #ff6b35;">*</span></label>
                        <input class="form-control" type="password" id="reg_password" name="password" placeholder="Tạo mật khẩu" minlength="6" maxlength="255" required />
                    </div>
                    <div class="form-group">
                        <label class="form-label">Xác nhận mật khẩu <span style="color: #ff6b35;">*</span></label>
                        <input class="form-control" type="password" id="reg_password_confirm" name="password_confirm" placeholder="Xác nhận mật khẩu" minlength="6" maxlength="255" required />
                    </div>
                    <div class="form-group">
                        <label class="form-label">Số điện thoại</label>
                        <input class="form-control" type="text" name="phone" placeholder="0xxxxxxxxx" maxlength="20" />
                    </div>
                    <div class="form-group">
                        <label class="form-label">Biển số xe</label>
                        <input class="form-control" type="text" name="license_plate" placeholder="VD: 43-ABC-1234" maxlength="20" />
                    </div>
                    <button class="btn btn-primary" type="submit" name="register">Đăng ký tài khoản</button>
                </form>
            </div>

            <div class="footer-text">
                © 2025 GU-XE. Bản quyền thuộc về GU-XE.
            </div>
        </div>
    </div>

    <script>
        function switchTab(tab) {
            // Remove active class from all tabs and content
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
            
            // Add active class to selected tab and content
            event.target.classList.add('active');
            document.getElementById(tab).classList.add('active');
        }

        function validateRegister() {
            const password = document.getElementById('reg_password').value;
            const passwordConfirm = document.getElementById('reg_password_confirm').value;
            const phone = document.querySelector('input[name="phone"]').value;
            
            // Kiểm tra mật khẩu trùng khớp
            if (password !== passwordConfirm) {
                alert('❌ Mật khẩu không trùng khớp!');
                return false;
            }
            
            // Kiểm tra số điện thoại (nếu nhập)
            if (phone && !/^0\d{9}$/.test(phone)) {
                alert('❌ Số điện thoại phải có định dạng: 0xxxxxxxxx (10 số)');
                return false;
            }
            
            return true;
        }
    </script>
</body>
</html>