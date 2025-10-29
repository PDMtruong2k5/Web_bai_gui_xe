<?php
require_once __DIR__ . '/../../functions/auth.php';
checkLogin(__DIR__ . '/../../index.php');
require_once __DIR__ . '/../../functions/vehicle_functions.php';
require_once __DIR__ . '/../../handle/vehicle_process.php';
?>
<!DOCTYPE html>
<html>

<head>
    <title>Chỉnh sửa xe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-3">
        <h3 class="mt-3 mb-4 text-center">CHỈNH SỬA XE</h3>
        <?php
            if (!isset($_GET['id']) || empty($_GET['id'])) {
                header("Location: ../vehicle.php?error=Không tìm thấy xe");
                exit;
            }
            $id = (int)$_GET['id'];
            $vehicle = handleGetVehicleById($id);
            if (!$vehicle) {
                header("Location: ../vehicle.php?error=Không tìm thấy xe");
                exit;
            }
            if (isset($_GET['error'])) {
                echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">'
                    . htmlspecialchars($_GET['error']) .
                    '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' .
                '</div>';
            }
        ?>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <form action="../../handle/vehicle_process.php" method="POST">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($vehicle['id']) ?>">
                            <div class="mb-3">
                                <label for="vehicle_plate" class="form-label">Biển số xe</label>
                                <input type="text" class="form-control" id="vehicle_plate" name="vehicle_plate" value="<?= htmlspecialchars($vehicle['vehicle_plate']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="vehicle_owner" class="form-label">Chủ xe/Loại xe</label>
                                <input type="text" class="form-control" id="vehicle_owner" name="vehicle_owner" value="<?= htmlspecialchars($vehicle['vehicle_owner']) ?>" required>
                            </div>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="../vehicle.php" class="btn btn-secondary me-md-2">Hủy</a>
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


