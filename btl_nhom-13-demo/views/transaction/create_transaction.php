<?php
require_once __DIR__ . '/../../functions/auth.php';
checkLogin(__DIR__ . '/../../index.php');
require_once __DIR__ . '/../../functions/transaction_functions.php';

$vehicles = getAllVehiclesForDropdown();
$services = getAllServiceTypesForDropdown();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Thêm giao dịch mới</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #4e54c8 0%, #8f94fb 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 20px;
        }
        .form-container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 600px;
            margin: auto;
        }
        .form-title {
            color: #2d3748;
            text-align: center;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 30px;
        }
        .form-control, .form-select {
            border: 1px solid #e2e8f0;
            padding: 12px 16px;
            height: auto;
            font-size: 16px;
            border-radius: 10px;
            background-color: #f8fafc;
            transition: all 0.3s ease;
        }
        .form-control:focus, .form-select:focus {
            background-color: #fff;
            border-color: #4e54c8;
            box-shadow: 0 0 0 3px rgba(78,84,200,0.1);
        }
        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
            color: #4a5568;
        }
        .btn-submit {
            background: #4e54c8;
            border: none;
            padding: 12px 24px;
            color: white;
            border-radius: 10px;
            font-weight: 500;
            width: 100%;
            transition: all 0.3s ease;
        }
        .btn-submit:hover {
            background: #3c4293;
            transform: translateY(-1px);
        }
        .btn-cancel {
            background: #718096;
            border: none;
            padding: 12px 24px;
            color: white;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-cancel:hover {
            background: #4a5568;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="form-container">
            <h3 class="form-title">THÊM GIAO DỊCH</h3>
                
                <?php if (isset($_GET['error'])) { ?>
                    <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?= htmlspecialchars($_GET['error']) ?>
                        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                    </div>
                <?php } ?>

                <form action="../../handle/transaction_process.php" method="POST">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="mb-4">
                        <label for="vehicle_id" class="form-label">
                            Xe gửi
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="vehicle_id" name="vehicle_id" required>
                            <option value="">-- Chọn xe --</option>
                            <?php foreach ($vehicles as $vehicle): ?>
                                <option value="<?= $vehicle['id'] ?>">
                                    <?= htmlspecialchars($vehicle['vehicle_owner']) ?> (<?= htmlspecialchars($vehicle['vehicle_plate']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Chọn xe đã đăng ký trong hệ thống</div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="service_id" class="form-label">
                            Loại dịch vụ
                            <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="service_id" name="service_id" required>
                            <option value="">-- Chọn loại dịch vụ --</option>
                            <?php foreach ($services as $service): ?>
                                <option value="<?= $service['id'] ?>">
                                    <?= htmlspecialchars($service['service_name']) ?> (<?= htmlspecialchars($service['service_code']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="amount" class="form-label">Số tiền (VNĐ)</label>
                        <input type="number" class="form-control" id="amount" name="amount" min="0" step="1000" required>
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Thêm giao dịch</button>
                        <a href="../transaction.php" class="btn btn-secondary">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        setTimeout(() => {
            const alertNode = document.querySelector('.alert');
            if (alertNode) {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alertNode);
                bsAlert.close();
            }
        }, 3000);
    </script>
</body>

</html>


