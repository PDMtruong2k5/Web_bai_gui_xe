<?php
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/payment_gateway.php';
require_once __DIR__ . '/../functions/ticket_functions.php';

// Endpoint for actions on payments from customer side
// POST action=mark_user_transferred & payment_id
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

$user = getCurrentUser();
if (!$user) {
    echo json_encode(['success' => false, 'message' => 'Bạn phải đăng nhập']); exit();
}

$action = $_POST['action'] ?? '';
$payment_id = isset($_POST['payment_id']) ? intval($_POST['payment_id']) : 0;
if ($payment_id <= 0) { echo json_encode(['success' => false, 'message' => 'Thiếu ID giao dịch']); exit(); }

switch ($action) {
    case 'mark_user_transferred':
        // mark payment status awaiting_confirmation
        $ok = mark_payment_awaiting($payment_id);
        if ($ok) echo json_encode(['success' => true]);
        else echo json_encode(['success' => false, 'message' => 'Không thể cập nhật trạng thái']);
        break;
    case 'admin_mark_paid':
        // only admin allowed
        if (($user['role'] ?? '') !== 'admin') {
            echo json_encode(['success' => false, 'message' => 'Không có quyền']); break;
        }
        // get payment to find ticket
        $payment = get_payment_by_id($payment_id);
        if (!$payment) { echo json_encode(['success' => false, 'message' => 'Không tìm thấy giao dịch']); break; }
        $ok = mark_payment_paid($payment_id);
        if ($ok) {
            // set ticket active
            $tid = intval($payment['ticket_id']);
            // load ticket to get current fields
            $t = getTicketById($tid);
            if ($t) {
                updateTicket($t['id'], $t['customer_id'], $t['ticket_type'], $t['start_date'], $t['end_date'], $t['price'], 'active');
            }
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không thể cập nhật trạng thái thanh toán']);
        }
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ']);
}
exit();
