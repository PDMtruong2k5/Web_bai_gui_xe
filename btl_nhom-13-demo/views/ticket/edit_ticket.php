<?php
require_once __DIR__ . '/../../functions/auth.php';
checkLogin(__DIR__ . '/../../index.php');
require_once __DIR__ . '/../../functions/ticket_functions.php';
require_once __DIR__ . '/../../functions/customer_functions.php';
require_once __DIR__ . '/../../handle/ticket_process.php';

$customers = getAllCustomersForDropdown();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ../ticket.php?error=Không tìm thấy vé");
    exit;
}
$id = (int)$_GET['id'];
$ticket = handleGetTicketById($id);
if (!$ticket) {
    header("Location: ../ticket.php?error=Không tìm thấy vé");
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Chỉnh sửa vé</title>
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
        .form-control {
            border: 1px solid #e2e8f0;
            padding: 12px 16px;
            height: auto;
            font-size: 16px;
            border-radius: 10px;
            background-color: #f8fafc;
            transition: all 0.3s ease;
        }
        .form-control:focus {
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
            <h3 class="form-title">CHỈNH SỬA VÉ</h3>
        <?php
            if (isset($_GET['error'])) {
                echo '<div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>' 
                    . htmlspecialchars($_GET['error']) .
                    '<button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>';
            }
        ?>
        <form action="../../handle/ticket_process.php" method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" name="id" value="<?= htmlspecialchars($ticket['id']) ?>">
            
            <div class="mb-4">
                <label for="customer_id" class="form-label">
                    Khách hàng
                    <span class="text-danger">*</span>
                </label>
                <select class="form-control" id="customer_id" name="customer_id" required>
                    <?php foreach($customers as $customer): ?>
                        <option value="<?= $customer['id'] ?>" <?= $customer['id'] == $ticket['customer_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($customer['name']) ?> - <?= htmlspecialchars($customer['license_plate']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-4">
                <label for="ticket_type" class="form-label">
                    Loại vé
                    <span class="text-danger">*</span>
                </label>
                <select class="form-control" id="ticket_type" name="ticket_type" required>
                    <option value="day" <?= $ticket['ticket_type'] == 'day' ? 'selected' : '' ?>>Vé ngày</option>
                    <option value="month" <?= $ticket['ticket_type'] == 'month' ? 'selected' : '' ?>>Vé tháng</option>
                    <option value="year" <?= $ticket['ticket_type'] == 'year' ? 'selected' : '' ?>>Vé năm</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label for="start_date" class="form-label">
                    Ngày bắt đầu
                    <span class="text-danger">*</span>
                </label>
                <input type="date" 
                       class="form-control" 
                       id="start_date" 
                       name="start_date" 
                       value="<?= date('Y-m-d', strtotime($ticket['start_date'])) ?>"
                       required>
            </div>

            <div class="mb-4">
                <label for="end_date" class="form-label">
                    Ngày kết thúc
                    <span class="text-danger">*</span>
                </label>
                <input type="date" 
                       class="form-control" 
                       id="end_date" 
                       name="end_date" 
                       value="<?= date('Y-m-d', strtotime($ticket['end_date'])) ?>"
                       required>
            </div>

            <div class="mb-4">
                <label for="price" class="form-label">
                    Giá vé (VNĐ)
                    <span class="text-danger">*</span>
                </label>
                <input type="number" 
                       class="form-control" 
                       id="price" 
                       name="price" 
                       value="<?= $ticket['price'] ?>"
                       min="0" 
                       step="1000"
                       required>
            </div>

            <div class="mb-4">
                <label for="status" class="form-label">
                    Trạng thái
                    <span class="text-danger">*</span>
                </label>
                <select class="form-control" id="status" name="status" required>
                    <option value="active" <?= $ticket['status'] == 'active' ? 'selected' : '' ?>>Đang hoạt động</option>
                    <option value="expired" <?= $ticket['status'] == 'expired' ? 'selected' : '' ?>>Hết hạn</option>
                    <option value="cancelled" <?= $ticket['status'] == 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                </select>
            </div>

            <div class="d-flex gap-3 mt-5">
                <button type="submit" class="btn btn-submit flex-grow-1">
                    <i class="fas fa-save me-2"></i>
                    Lưu thay đổi
                </button>
                <a href="../ticket.php" class="btn btn-cancel">
                    <i class="fas fa-times"></i>
                </a>
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


