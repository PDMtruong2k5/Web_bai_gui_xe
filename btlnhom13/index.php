<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - Quản Lý Bãi Gửi Xe </title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: url(https://scr.vn/wp-content/uploads/2020/07/Si%C3%AAu-xe-Mc-Laren-4k-ch%E1%BA%A5t-ch%C6%A1i-scaled.jpg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
            background-size: cover;
        }

        /* Animated background elements */
        .bg-animation {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .car-icon {
            position: absolute;
            font-size: 40px;
            opacity: 0.1;
            animation: float 20s infinite ease-in-out;
        }

        .car-icon:nth-child(1) { top: 10%; left: 10%; animation-delay: 0s; }
        .car-icon:nth-child(2) { top: 20%; right: 15%; animation-delay: 3s; }
        .car-icon:nth-child(3) { bottom: 15%; left: 20%; animation-delay: 6s; }
        .car-icon:nth-child(4) { bottom: 20%; right: 10%; animation-delay: 9s; }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(5deg); }
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 1100px;
            width: 90%;
            display: grid;
            grid-template-columns: 1fr 1fr;
            overflow: hidden;
            position: relative;
            z-index: 1;
            animation: slideIn 0.6s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .left-panel {
            background: linear-gradient(135deg, #000000ff 0%, #ffffffff 100%);
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            position: relative;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .logo-section {
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .parking-icon {
            font-size: 80px;
            margin-bottom: 20px;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .logo-section h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }

        .logo-section p {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 30px;
        }

        .features {
            text-align: left;
            margin-top: 30px;
        }

        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            animation: fadeInLeft 0.6s ease-out backwards;
        }

        .feature-item:nth-child(1) { animation-delay: 0.2s; }
        .feature-item:nth-child(2) { animation-delay: 0.4s; }
        .feature-item:nth-child(3) { animation-delay: 0.6s; }

        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .feature-icon {
            font-size: 24px;
            margin-right: 15px;
        }

        .right-panel {
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header {
            margin-bottom: 40px;
        }

        .form-header h2 {
            font-size: 28px;
            font-weight: 700;
            color: #1a202c;
            margin-bottom: 8px;
        }

        .form-header p {
            color: #718096;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 24px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #4a5568;
            font-weight: 500;
            font-size: 14px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            font-size: 20px;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px 14px 48px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s;
            background: #f7fafc;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .alert {
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            animation: slideDown 0.4s ease-out;
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

        .alert-danger {
            background: #fff5f5;
            color: #c53030;
            border-left: 4px solid #fc8181;
        }

        .alert-success {
            background: #f0fff4;
            color: #22543d;
            border-left: 4px solid #68d391;
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            background color: #0c0000ff;;
            color: black;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            padding: 15px;
            background: rgba(0, 0, 0, 0.1);
            color: white;
            font-size: 13px;
            z-index: 1;
        }

        @media (max-width: 968px) {
            .login-container {
                grid-template-columns: 1fr;
                max-width: 500px;
            }

            .left-panel {
                padding: 40px 30px;
            }

            .features {
                display: none;
            }

            .right-panel {
                padding: 40px 30px;
            }
        }
    </style>
</head>
<body>

    <div class="login-container">
        <div class="left-panel">
            <div class="logo-section">
                <img src="images/guixe.jpg" alt="Logo" width="120" height="120">
                <h1>Hệ Thống Quản Lý Bãi Gửi Xe Thông Minh</h1>
            </div>
            
            <div class="features">
                <div class="feature-item">
                    <div>Quản lý xe ra vào hiệu quả</div>
                </div>
                <div class="feature-item">
                    <div>Thanh toán nhanh chóng</div>
                </div>
                <div class="feature-item">

                    <div>Báo cáo thống kê chi tiết</div>
                </div>
            </div>
        </div>

        <div class="right-panel">
            <div class="form-header">
                <h2>Đăng Nhập</h2>
                <p>Chào mừng trở lại! Vui lòng đăng nhập để tiếp tục.</p>
            </div>
            <div style="margin-bottom: 12px;">
                <a href="/btlnhom13/views/customer/index.php" style="font-size:14px;color:#2b6cb0;text-decoration:none;font-weight:600;">Khách hàng? Tra cứu gửi xe tại đây</a>
            </div>

            <form action="./handle/login_process.php" method="POST">
                <!-- Alert Messages -->
                <div id="alertContainer"></div>

                <!-- Username -->
                <div class="form-group">
                    <label for="username">Tên đăng nhập</label>
                    <div class="input-wrapper">
                        <input 
                            type="text" 
                            name="username" 
                            id="username" 
                            class="form-control"
                            placeholder="Nhập tên đăng nhập"
                            required 
                            autocomplete="username"
                        />
                    </div>
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <div class="input-wrapper">
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            class="form-control"
                            placeholder="Nhập mật khẩu"
                            required
                            autocomplete="current-password"
                        />
                    </div>
                </div>

                <button type="submit" name="login" class="btn-login">
                    Đăng Nhập
                </button>
            </form>
        </div>
    </div>

    <footer class="footer">
        Copyright © 2025 
    </footer>

    <script>
        // Simulate PHP session messages for demo
        // In production, replace this with actual PHP session handling
        const urlParams = new URLSearchParams(window.location.search);
        const alertContainer = document.getElementById('alertContainer');
        
        if (urlParams.get('error')) {
            alertContainer.innerHTML = `
                <div class="alert alert-danger">
                    ${decodeURIComponent(urlParams.get('error'))}
                </div>
            `;
        }
        
        if (urlParams.get('success')) {
            alertContainer.innerHTML = `
                <div class="alert alert-success">
                    ${decodeURIComponent(urlParams.get('success'))}
                </div>
            `;
        }

        // Auto-remove alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>