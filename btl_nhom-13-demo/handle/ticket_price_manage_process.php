<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../functions/ticket_price_manage_functions.php';

$action = $_POST['action'] ?? '';

if ($action === 'update_price') {
    $ticket_type = $_POST['ticket_type'] ?? '';
    $base_price = floatval($_POST['base_price'] ?? 0);
    $description = $_POST['description'] ?? '';
    
    if (!$ticket_type || $base_price <= 0) {
        echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
        exit;
    }
    
    if (updateTicketPrice($ticket_type, $base_price, $description)) {
        echo json_encode(['success' => true, 'message' => 'Cập nhật giá vé thành công']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi cập nhật giá vé']);
    }
}

elseif ($action === 'add_promotion') {
    $name = $_POST['name'] ?? '';
    $ticket_type = $_POST['ticket_type'] ?? '';
    $discount_percent = intval($_POST['discount_percent'] ?? 0);
    $discount_amount = floatval($_POST['discount_amount'] ?? 0);
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    
    if (!$name || !$ticket_type || (!$discount_percent && !$discount_amount)) {
        echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
        exit;
    }
    
    if (addPromotion($name, $ticket_type, $discount_percent, $discount_amount, $start_date, $end_date)) {
        echo json_encode(['success' => true, 'message' => 'Thêm khuyến mãi thành công']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi thêm khuyến mãi']);
    }
}

elseif ($action === 'edit_promotion') {
    $id = intval($_POST['id'] ?? 0);
    $name = $_POST['name'] ?? '';
    $ticket_type = $_POST['ticket_type'] ?? '';
    $discount_percent = intval($_POST['discount_percent'] ?? 0);
    $discount_amount = floatval($_POST['discount_amount'] ?? 0);
    $start_date = $_POST['start_date'] ?? '';
    $end_date = $_POST['end_date'] ?? '';
    $is_active = intval($_POST['is_active'] ?? 1);
    
    if (!$id || !$name || !$ticket_type) {
        echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
        exit;
    }
    
    if (updatePromotion($id, $name, $ticket_type, $discount_percent, $discount_amount, $start_date, $end_date, $is_active)) {
        echo json_encode(['success' => true, 'message' => 'Cập nhật khuyến mãi thành công']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi cập nhật khuyến mãi']);
    }
}

elseif ($action === 'delete_promotion') {
    $id = intval($_POST['id'] ?? 0);
    
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
        exit;
    }
    
    if (deletePromotion($id)) {
        echo json_encode(['success' => true, 'message' => 'Xóa khuyến mãi thành công']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi xóa khuyến mãi']);
    }
}

elseif ($action === 'toggle_promotion') {
    $id = intval($_POST['id'] ?? 0);
    
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
        exit;
    }
    
    if (togglePromotionStatus($id)) {
        echo json_encode(['success' => true, 'message' => 'Cập nhật trạng thái thành công']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi cập nhật trạng thái']);
    }
}

else {
    echo json_encode(['success' => false, 'message' => 'Action không hợp lệ']);
}
?>
