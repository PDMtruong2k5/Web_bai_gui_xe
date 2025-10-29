<?php
require_once __DIR__ . '/../../functions/auth.php';
checkLogin(__DIR__ . '/../../index.php');
require_once __DIR__ . '/../../functions/transaction_functions.php';
?>
<!DOCTYPE html>
<html>

<head>
    <title>Bãi gửi xe - Chỉnh sửa giao dịch</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="container mt-3">
        <h3 class="mt-3 mb-4 text-center">CHỈNH SỬA GIAO DỊCH</h3>
        <?php
            if (!isset($_GET['id']) || empty($_GET['id'])) {
                header("Location: ../transaction.php?error=Không tìm thấy giao dịch");
                exit;
            }

            $id = (int)$_GET['id'];
            require_once __DIR__ . '/../../handle/transaction_process.php';
            $transactionInfo = handleGetTransactionById($id);

            if (!$transactionInfo) {
                header("Location: ../transaction.php?error=Không tìm thấy giao dịch");
                exit;
            }

            $vehicles = getAllVehiclesForDropdown();
            $services = getAllServiceTypesForDropdown();

            if (isset($_GET['error'])) {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">'
                    . htmlspecialchars($_GET['error']) .
                    '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' .
                '</div>';
            }
        ?>
        <script>
            setTimeout(() => {
                const alertNode = document.querySelector('.alert');
                if (alertNode) {
                    const bsAlert = bootstrap.Alert.getOrCreateInstance(alertNode);
                    bsAlert.close();
                }
            }, 3000);
        </script>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <form action="../../handle/transaction_process.php" method="POST">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($transactionInfo['id']) ?>">

                            <div class="mb-3">
                                <label for="vehicle_id" class="form-label">Xe gửi</label>
                                <select class="form-select" id="vehicle_id" name="vehicle_id" required>
                                    <option value="">-- Chọn xe --</option>
                                    <?php foreach ($vehicles as $vehicle): ?>
                                        <option value="<?= $vehicle['id'] ?>" <?= $vehicle['id'] == $transactionInfo['vehicle_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($vehicle['vehicle_plate']) ?> - <?= htmlspecialchars($vehicle['vehicle_owner']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="service_id" class="form-label">Loại dịch vụ</label>
                                <select class="form-select" id="service_id" name="service_id" required>
                                    <option value="">-- Chọn loại dịch vụ --</option>
                                    <?php foreach ($services as $service): ?>
                                        <option value="<?= $service['id'] ?>" <?= $service['id'] == $transactionInfo['service_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($service['service_code']) ?> - <?= htmlspecialchars($service['service_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="amount" class="form-label">Số tiền (VNĐ)</label>
                                <input type="number" class="form-control" id="amount" name="amount" min="0" step="1000" value="<?= htmlspecialchars($transactionInfo['amount']) ?>" required>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="../transaction.php" class="btn btn-secondary me-md-2">Hủy</a>
                                <button type="submit" class="btn btn-primary">Cập nhật</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>


