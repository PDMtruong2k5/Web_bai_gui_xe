<?php
require_once __DIR__ . '/../../functions/auth.php';
checkLogin(__DIR__ . '/../../index.php');
require_once __DIR__ . '/../../functions/service_functions.php';
?>
<!DOCTYPE html>
<html>

<head>
    <title>Chỉnh sửa loại dịch vụ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-3">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h3 class="mt-3 mb-4">CHỈNH SỬA LOẠI DỊCH VỤ</h3>
                <?php
                    if (!isset($_GET['id']) || empty($_GET['id'])) {
                        header("Location: ../service.php?error=Không tìm thấy dịch vụ");
                        exit();
                    }
                    $id = (int)$_GET['id'];
                    $service = getServiceById($id);
                    if (!$service) {
                        header("Location: ../service.php?error=Không tìm thấy dịch vụ");
                        exit();
                    }
                    if (isset($_GET['error'])) {
                        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">'
                            . htmlspecialchars($_GET['error']) .
                            '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' .
                        '</div>';
                    }
                ?>
                <form action="../../handle/service_process.php" method="POST">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($service['id']) ?>">
                    <div class="mb-3">
                        <label for="service_code" class="form-label">Mã dịch vụ</label>
                        <input type="text" class="form-control" id="service_code" name="service_code" value="<?= htmlspecialchars($service['service_code']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="service_name" class="form-label">Tên dịch vụ</label>
                        <input type="text" class="form-control" id="service_name" name="service_name" value="<?= htmlspecialchars($service['service_name']) ?>" required>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="../service.php" class="btn btn-secondary me-md-2">Hủy</a>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>


