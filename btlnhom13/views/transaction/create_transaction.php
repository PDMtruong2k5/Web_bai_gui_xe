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
    <title>Bãi gửi xe - Thêm giao dịch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="container mt-3">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h3 class="mt-3 mb-4">THÊM GIAO DỊCH</h3>
                
                <?php if (isset($_GET['error'])) { ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_GET['error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php } ?>

                <form action="../../handle/transaction_process.php" method="POST">
                    <input type="hidden" name="action" value="create">
                    
                    <div class="mb-3">
                        <label for="vehicle_id" class="form-label">Xe gửi</label>
                        <select class="form-select" id="vehicle_id" name="vehicle_id" required>
                            <option value="">-- Chọn xe --</option>
                            <?php foreach ($vehicles as $vehicle): ?>
                                <option value="<?= $vehicle['id'] ?>">
                                    <?= htmlspecialchars($vehicle['vehicle_owner']) ?> (<?= htmlspecialchars($vehicle['vehicle_plate']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="service_id" class="form-label">Loại dịch vụ</label>
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


