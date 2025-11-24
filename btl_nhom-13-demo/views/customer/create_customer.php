<?php
require_once __DIR__ . '/../../functions/auth.php';
checkLogin(__DIR__ . '/../../index.php');
?>
<!DOCTYPE html>
<html>

<head>
    <title>Thêm khách hàng mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            overflow-x: hidden;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            50% { transform: translate(-20px, 20px) rotate(5deg); }
        }

        .form-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            padding: 50px 45px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3), 0 0 0 1px rgba(255,255,255,0.1);
            width: 100%;
            max-width: 550px;
            margin: auto;
            position: relative;
            z-index: 1;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .form-header {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
        }

        .form-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .form-icon i {
            color: white;
            font-size: 32px;
        }

        .form-title {
            color: #2d3748;
            font-size: 28px;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .form-subtitle {
            color: #718096;
            font-size: 14px;
            margin-top: 8px;
        }

        .input-group-custom {
            position: relative;
            margin-bottom: 28px;
        }

        .form-label {
            font-weight: 600;
            margin-bottom: 10px;
            color: #4a5568;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .form-label i {
            color: #667eea;
            font-size: 16px;
        }

        .input-wrapper {
            position: relative;
        }

        .form-control {
            border: 2px solid #e2e8f0;
            padding: 14px 16px 14px 45px;
            height: auto;
            font-size: 15px;
            border-radius: 12px;
            background-color: #f8fafc;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            width: 100%;
        }

        .form-control:focus {
            background-color: #fff;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
            outline: none;
            transform: translateY(-2px);
        }

        .form-control:valid {
            border-color: #48bb78;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            font-size: 18px;
            transition: all 0.3s ease;
            pointer-events: none;
        }

        .form-control:focus + .input-icon {
            color: #667eea;
        }

        .btn-submit {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 16px 32px;
            color: white;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .btn-submit::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }

        .btn-submit:hover::before {
            left: 100%;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-cancel {
            background: #e2e8f0;
            border: none;
            padding: 16px 24px;
            color: #4a5568;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-cancel:hover {
            background: #cbd5e0;
            color: #2d3748;
            transform: translateY(-2px);
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 16px 20px;
            margin-bottom: 30px;
            animation: slideDown 0.4s ease-out;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-danger {
            background: linear-gradient(135deg, #fc8181 0%, #f56565 100%);
            color: white;
        }

        .text-danger {
            color: #f56565 !important;
        }

        .button-group {
            display: flex;
            gap: 12px;
            margin-top: 35px;
        }

        /* Responsive */
        @media (max-width: 576px) {
            .form-container {
                padding: 35px 25px;
            }

            .form-title {
                font-size: 24px;
            }

            .button-group {
                flex-direction: column;
            }

            .btn-cancel {
                width: 100%;
            }
        }

        /* Loading state */
        .btn-submit.loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .btn-submit.loading i {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="form-container">
            <div class="form-header">
                <div class="form-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h3 class="form-title">Thêm Khách Hàng</h3>
                <p class="form-subtitle">Điền thông tin khách hàng mới vào form bên dưới</p>
            </div>

            <?php if (isset($_GET['error'])) { ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?= htmlspecialchars($_GET['error']) ?>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                </div>
            <?php } ?>

            <form action="../../handle/customer_process.php" method="POST" id="customerForm">
                <input type="hidden" name="action" value="create">
                
                <div class="input-group-custom">
                    <label for="name" class="form-label">
                        <i class="fas fa-user"></i>
                        Họ và tên
                        <span class="text-danger">*</span>
                    </label>
                    <div class="input-wrapper">
                        <input type="text" 
                               class="form-control" 
                               id="name" 
                               name="name" 
                               placeholder="Nhập họ và tên khách hàng"
                               required>
                        <i class="fas fa-user input-icon"></i>
                    </div>
                </div>

                <div class="input-group-custom">
                    <label for="phone" class="form-label">
                        <i class="fas fa-phone"></i>
                        Số điện thoại
                        <span class="text-danger">*</span>
                    </label>
                    <div class="input-wrapper">
                        <input type="tel" 
                               class="form-control" 
                               id="phone" 
                               name="phone" 
                               placeholder="Nhập số điện thoại (10-11 số)"
                               pattern="[0-9]{10,11}"
                               required>
                        <i class="fas fa-phone input-icon"></i>
                    </div>
                </div>
                

                <div class="input-group-custom">
                    <label for="license_plate" class="form-label">
                        <i class="fas fa-car"></i>
                        Biển số xe
                        <span class="text-danger">*</span>
                    </label>
                    <div class="input-wrapper">
                        <input type="text" 
                               class="form-control" 
                               id="license_plate" 
                               name="license_plate" 
                               placeholder="Nhập biển số xe (VD: 43A-12345)"
                               required>
                        <i class="fas fa-car input-icon"></i>
                    </div>
                </div>

                <div class="input-group-custom">
                    <label for="role" class="form-label">
                        <i class="fas fa-user-tag"></i>
                        Vai trò
                        <span class="text-danger">*</span>
                    </label>
                    <div class="input-wrapper">
                        <select class="form-control" id="role" name="role" required>
                            <option value="">Chọn vai trò</option>
                            <option value="customer">Khách hàng</option>
                            <option value="admin">Quản trị viên</option>
                        </select>
                        <i class="fas fa-user-tag input-icon"></i>
                    </div>
                </div>

                <div class="button-group">
                    <button type="submit" class="btn btn-submit flex-grow-1" id="submitBtn">
                        <i class="fas fa-check me-2"></i>
                        Thêm khách hàng
                    </button>
                    <a href="../customer.php" class="btn btn-cancel">
                        <i class="fas fa-times me-2"></i>
                        Hủy
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto capitalize license plate
        document.getElementById('license_plate').addEventListener('input', function(e) {
            this.value = this.value.toUpperCase();
        });

        // Form submission with loading state
        document.getElementById('customerForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.classList.add('loading');
            submitBtn.innerHTML = '<i class="fas fa-spinner me-2"></i>Đang xử lý...';
        });

        // Auto hide alert after 3s
        setTimeout(() => {
            const alertNode = document.querySelector('.alert');
            if (alertNode) {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alertNode);
                bsAlert.close();
            }
        }, 3000);

        // Input validation feedback
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.value.trim() !== '') {
                    this.classList.add('filled');
                } else {
                    this.classList.remove('filled');
                }
            });
        });
    </script>
</body>

</html>