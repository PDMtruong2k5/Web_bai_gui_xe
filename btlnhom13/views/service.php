<?php
require_once __DIR__ . '/../functions/auth.php';
checkLogin(__DIR__ . '/../index.php');
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Loại Dịch Vụ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/main.css">
</head>

<body>
    <?php include './menu.php'; ?>
    
    <div class="main-container">
        <div class="page-header">
            <h1 class="page-title">
                <i class="fas fa-concierge-bell text-primary"></i>
                DANH SÁCH LOẠI DỊCH VỤ
            </h1>
            <a href="service/create_service.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm dịch vụ mới
            </a>
        </div>

        <?php
        // Hiển thị thông báo thành công
        if (isset($_GET['success'])) {
            echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                ' . htmlspecialchars($_GET['success']) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>';
        }
        
        // Hiển thị thông báo lỗi
        if (isset($_GET['error'])) {
            echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                ' . htmlspecialchars($_GET['error']) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>';
        }
        ?>
        
        <div class="table-container">
        <table class="table table-hover">
            <thead>
                <tr>
                    <!-- <th scope="col">STT</th> -->
                    <th scope="col">ID</th>
                    <th scope="col">Mã dịch vụ</th>
                    <th scope="col">Tên dịch vụ</th>
                    <th scope="col">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php
                require_once '../functions/service_functions.php';
                $services = getAllServices();

                foreach($services as $index => $srv){
                    $stt = $index + 1;
            ?>
                <tr>
                    <td><?= $srv["id"] ?></td>
                    <td><?= $srv["service_code"] ?></td>
                    <td><?= $srv["service_name"] ?></td>
                    <td>
                        <a href="service/edit_service.php?id=<?= $srv["id"] ?>" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Sửa
                        </a>
                        <a href="../handle/service_process.php?action=delete&id=<?= $srv["id"] ?>"
                            class="btn btn-danger btn-sm"
                            onclick="return confirm('Bạn có chắc chắn muốn xóa loại dịch vụ này?')">
                            <i class="fas fa-trash"></i> Xóa
                        </a>
                    </td>
                </tr>
                <?php } ?>

            </tbody>
        </table>
        </div>
        
        <?php if(empty($services)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-concierge-bell"></i>
            </div>
            <h4>Chưa có loại dịch vụ nào</h4>
            <p>Hãy thêm loại dịch vụ đầu tiên vào hệ thống</p>
        </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>

</html>