<?php
require_once __DIR__ . '/../../functions/auth.php';
checkLogin(__DIR__ . '/../../index.php');
require_once __DIR__ . '/../../functions/area_functions.php';
require_once __DIR__ . '/../../handle/area_process.php';
?>
<!DOCTYPE html>
<html>

<head>
    <title>Chỉnh sửa khu vực</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-3">
        <h3 class="mt-3 mb-4 text-center">CHỈNH SỬA KHU VỰC</h3>
        <?php
            if (!isset($_GET['id']) || empty($_GET['id'])) {
                header("Location: ../area.php?error=Không tìm thấy khu vực");
                exit;
            }
            $id = (int)$_GET['id'];
            $area = handleGetAreaById($id);
            if (!$area) {
                header("Location: ../area.php?error=Không tìm thấy khu vực");
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
                        <form action="../../handle/area_process.php" method="POST">
                            <input type="hidden" name="action" value="edit">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($area['id']) ?>">
                            <div class="mb-3">
                                <label for="class_name" class="form-label">Tên khu vực</label>
                                <input type="text" class="form-control" id="class_name" name="class_name" value="<?= htmlspecialchars($area['area_name']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="school_year" class="form-label">Mô tả/Sức chứa</label>
                                <input type="text" class="form-control" id="school_year" name="school_year" value="<?= htmlspecialchars($area['area_desc']) ?>" required>
                            </div>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="../area.php" class="btn btn-secondary me-md-2">Hủy</a>
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


