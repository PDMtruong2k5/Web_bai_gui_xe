<?php
require_once __DIR__ . '/../../functions/auth.php';
checkLogin(__DIR__ . '/../../index.php');
?>
<!DOCTYPE html>
<html>

<head>
    <title>Thêm khu vực</title>
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
            border-radius: 18px;
            padding: 34px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 640px;
            margin: auto;
        }
        .form-title { text-align:center; font-size:24px; font-weight:600; color:#2d3748; margin-bottom:20px; }
        .form-control { border:1px solid #e2e8f0; padding:12px 14px; border-radius:10px; background:#f8fafc; }
        .form-control:focus { background:#fff; border-color:#4e54c8; box-shadow:0 0 0 3px rgba(78,84,200,0.08); }
        .form-label { font-weight:500; color:#4a5568; margin-bottom:6px; }
        .form-text { color:#718096; font-size:0.875rem; margin-top:4px; }
        .btn-primary { background:#4e54c8; border:none; }
        .btn-primary:hover { background:#3c4293; }
        .btn-secondary { background:#718096; border:none; }
        .btn-secondary:hover { background:#4a5568; }
    </style>
</head>

<body>
    <div class="container">
        <div class="form-container">
            <h3 class="form-title">THÊM KHU VỰC</h3>
            <?php
                if (isset($_GET['error'])) {
                    echo '<div class="alert alert-danger d-flex align-items-center mb-4" role="alert">'
                        . '<i class="fas fa-exclamation-circle me-2"></i>'
                        . htmlspecialchars($_GET['error'])
                        . '<button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>'
                        . '</div>';
                }
            ?>

            <form action="../../handle/area_process.php" method="POST">
                <input type="hidden" name="action" value="create">

                <div class="mb-4">
                    <label for="class_name" class="form-label">Tên khu vực <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="class_name" name="class_name" placeholder="VD: Khu A - Tầng 1" required>
                    <div class="form-text">Tên khu vực hoặc vị trí</div>
                </div>

                <div class="mb-4">
                    <label for="school_year" class="form-label">Mô tả / Sức chứa</label>
                    <input type="text" class="form-control" id="school_year" name="school_year" placeholder="VD: 20 xe" required>
                    <div class="form-text">Ghi chú ngắn hoặc sức chứa tối đa</div>
                </div>

                <div class="d-flex gap-3 mt-4">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="fas fa-plus me-2"></i>Thêm khu vực</button>
                    <a href="../area.php" class="btn btn-secondary"><i class="fas fa-times"></i></a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto hide alert after 3s
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


