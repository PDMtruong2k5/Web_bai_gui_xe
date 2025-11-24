<?php
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/payment_gateway.php';
require_once __DIR__ . '/../functions/payment_settings.php';
require_once __DIR__ . '/../functions/ticket_functions.php';

// AJAX endpoint for customers to create payment for a ticket and receive payment id
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// must be logged in
$user = getCurrentUser();
if (!$user) {
    echo json_encode(['success' => false, 'message' => 'Bạn phải đăng nhập']);
    exit();
}

$ticket_id = isset($_POST['ticket_id']) ? intval($_POST['ticket_id']) : 0;
$amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;

if ($ticket_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Thiếu ID vé']);
    exit();
}

$ticket = getTicketById($ticket_id);
if (!$ticket) {
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy vé']);
    exit();
}

if ($amount <= 0) $amount = floatval($ticket['price']);

$payment = create_payment_record($ticket_id, $amount, 'Thanh toán vé #' . $ticket_id);
if (!$payment) {
    echo json_encode(['success' => false, 'message' => 'Không thể tạo giao dịch thanh toán']);
    exit();
}

// Build QR payload and image URL to show immediately in the modal
$settings = load_payment_settings();
$bank_account = $settings['bank_account'] ?? '';
$bank_name = $settings['bank_name'] ?? '';
// Build a minimal payload (without embedding bank info); bank info shown separately.
$payload = sprintf('THANHTOAN|%d|%0.2f', $payment['id'], $payment['amount']);
// Prefer admin-uploaded QR image if present; otherwise generate QR from payload
$qr_image = !empty($settings['qr_image']) ? $settings['qr_image'] : null;
$qr_url = $qr_image ? $qr_image : ('https://chart.googleapis.com/chart?chs=400x400&cht=qr&chl=' . urlencode($payload));

// return payment info and QR url so frontend can display it inline
echo json_encode([
    'success' => true,
    'payment_id' => $payment['id'],
    'amount' => $payment['amount'],
    'payload' => $payload,
    'qr_url' => $qr_url,
    'bank_account' => $bank_account,
    'bank_name' => $bank_name,
    'qr_image' => $qr_image,
]);
exit();
