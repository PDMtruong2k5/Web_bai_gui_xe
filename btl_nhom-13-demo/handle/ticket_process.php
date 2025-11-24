
<?php
// ==========================
// Xử lý chức năng vé
// ==========================

// Import các hàm xử lý liên quan
require_once __DIR__ . '/../functions/ticket_functions.php';
require_once __DIR__ . '/../functions/payment_gateway.php';

// Xác định action từ request
$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif (isset($_POST['action'])) {
    $action = $_POST['action'];
}

// Điều hướng theo action
switch ($action) {
    case 'create':
        handleCreateTicket();
        break;
    case 'edit':
        handleEditTicket();
        break;
    case 'cancel':
        handleCancelTicket();
        break;
    case 'update_price':
        handleUpdateTicketPrice();
        break;
    // Có thể bổ sung thêm các case khác nếu cần
}

// ==========================
// Các hàm tiện ích lấy dữ liệu
// ==========================
function handleGetAllTickets() {
    return getAllTickets();
}
function handleGetTicketById($id) {
    return getTicketById($id);
}

// ==========================
// Các hàm xử lý chính
// ==========================

/**
 * Tạo vé mới
 */
function handleCreateTicket() {
    $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        if ($isAjax) {
            echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
            exit();
        }
        header("Location: ../views/ticket.php?error=Phương thức không hợp lệ");
        exit();
    }
    if (!isset($_POST['customer_id']) || !isset($_POST['ticket_type']) || !isset($_POST['start_date'])) {
        if ($isAjax) {
            echo json_encode(['success' => false, 'message' => 'Thiếu thông tin cần thiết']);
            exit();
        }
        header("Location: ../views/ticket/create_ticket.php?error=Thiếu thông tin cần thiết");
        exit();
    }
    $customer_id = (int)$_POST['customer_id'];
    $ticket_type = trim($_POST['ticket_type']);
    $start_date = trim($_POST['start_date']);
    $price = isset($_POST['price']) ? floatval($_POST['price']) : 0;
    $end_date = calculateEndDate($start_date, $ticket_type);
    if ($price <= 0) {
        $price = getTicketPrice($ticket_type);
    }
    if (empty($ticket_type) || empty($start_date) || $price <= 0) {
        if ($isAjax) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin']);
            exit();
        }
        header("Location: ../views/ticket/create_ticket.php?error=Vui lòng điền đầy đủ thông tin");
        exit();
    }
    $result = addTicket($customer_id, $ticket_type, $start_date, $end_date, $price);
    if ($result) {
        if ($isAjax) {
            $newTicket = getTicketById($result);
            echo json_encode(['success' => true, 'ticket' => $newTicket]);
            exit();
        }
        $payment = create_payment_record($result, $price, 'Thanh toán vé #' . $result);
        if ($payment && isset($payment['id'])) {
            header("Location: ../views/transaction/payment_qr.php?id=" . $payment['id']);
        } else {
            header("Location: ../views/ticket.php?success=Yêu cầu đặt vé đã được tạo - Chờ thanh toán&warning=Không thể tạo bản ghi thanh toán");
        }
    } else {
        if ($isAjax) {
            echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra khi đăng ký vé']);
            exit();
        }
        header("Location: ../views/ticket/create_ticket.php?error=Có lỗi xảy ra khi đăng ký vé");
    }
    exit();
}

/**
 * Sửa thông tin vé
 */
function handleEditTicket() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/ticket.php?error=Phương thức không hợp lệ");
        exit();
    }
    if (!isset($_POST['id']) || !isset($_POST['customer_id']) || !isset($_POST['ticket_type']) || !isset($_POST['start_date']) || !isset($_POST['price']) || !isset($_POST['status'])) {
        header("Location: ../views/ticket.php?error=Thiếu thông tin cần thiết");
        exit();
    }
    $id = (int)$_POST['id'];
    $customer_id = (int)$_POST['customer_id'];
    $ticket_type = trim($_POST['ticket_type']);
    $start_date = trim($_POST['start_date']);
    $end_date = trim($_POST['end_date']);
    $price = floatval($_POST['price']);
    $status = trim($_POST['status']);
    if (empty($ticket_type) || empty($start_date) || empty($end_date) || $price <= 0) {
        header("Location: ../views/ticket/edit_ticket.php?id=" . $id . "&error=Vui lòng điền đầy đủ thông tin");
        exit();
    }
    $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    $result = updateTicket($id, $customer_id, $ticket_type, $start_date, $end_date, $price, $status);
    if ($result) {
        if ($isAjax) {
            $updated = getTicketById($id);
            echo json_encode(['success' => true, 'ticket' => $updated]);
            exit();
        }
        header("Location: ../views/ticket.php?success=Cập nhật vé thành công");
    } else {
        if ($isAjax) {
            echo json_encode(['success' => false, 'message' => 'Cập nhật vé thất bại']);
            exit();
        }
        header("Location: ../views/ticket/edit_ticket.php?id=" . $id . "&error=Cập nhật vé thất bại");
    }
    exit();
}

/**
 * Hủy vé
 */
function handleCancelTicket() {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        header("Location: ../views/ticket.php?error=Phương thức không hợp lệ");
        exit();
    }
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        header("Location: ../views/ticket.php?error=Không tìm thấy ID vé");
        exit();
    }
    $id = (int)$_GET['id'];
    $result = cancelTicket($id);
    if ($result) {
        header("Location: ../views/ticket.php?success=Hủy vé thành công");
    } else {
        header("Location: ../views/ticket.php?error=Hủy vé thất bại");
    }
    exit();
}

/**
 * Cập nhật giá vé
 */
function handleUpdateTicketPrice() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/ticket.php?error=Phương thức không hợp lệ");
        exit();
    }
    if (!isset($_POST['ticket_type']) || !isset($_POST['price'])) {
        header("Location: ../views/ticket.php?error=Thiếu thông tin cần thiết");
        exit();
    }
    $ticket_type = trim($_POST['ticket_type']);
    $price = floatval($_POST['price']);
    if ($price <= 0) {
        header("Location: ../views/ticket.php?error=Giá vé phải lớn hơn 0");
        exit();
    }
    $result = updateTicketPrice($ticket_type, $price);
    if ($result) {
        header("Location: ../views/ticket.php?success=Cập nhật giá vé thành công");
    } else {
        header("Location: ../views/ticket.php?error=Cập nhật giá vé thất bại");
    }
    exit();
}

// ==========================
// Kết thúc file
// ==========================

