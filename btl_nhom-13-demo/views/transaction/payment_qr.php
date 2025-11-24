<?php
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '/../../functions/payment_gateway.php';
require_once __DIR__ . '/../../functions/payment_settings.php';

$payment_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$payment = null;
if ($payment_id > 0) $payment = get_payment_by_id($payment_id);

if (!$payment) {
    echo '<p>Không tìm thấy giao dịch.</p>'; exit();
}

$settings = load_payment_settings();
$bank_account = $settings['bank_account'] ?? '';
$bank_name = $settings['bank_name'] ?? '';

// Build minimal payload and prefer admin-uploaded QR image
$payload = sprintf('THANHTOAN|%d|%0.2f', $payment['id'], $payment['amount']);
$qr_image = $settings['qr_image'] ?? '';
$qr_url = $qr_image ? $qr_image : ('https://chart.googleapis.com/chart?chs=400x400&cht=qr&chl=' . urlencode($payload));
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>QR Thanh toán</title>
<style>body{font-family:Arial,Helvetica,sans-serif;background:#0f172a;color:#fff;display:flex;align-items:center;justify-content:center;height:100vh;margin:0} .card{background:rgba(255,255,255,0.04);padding:24px;border-radius:12px;text-align:center} img{max-width:100%;height:auto}</style>
</head>
<body>
<div class="card">
    <h2>Thanh toán vé</h2>
    <p>Số tiền: <strong><?php echo number_format($payment['amount'],0,',','.'); ?> VND</strong></p>
    <p>Ghi chú chuyển khoản: <strong>THANHTOAN<?php echo $payment['id']; ?></strong></p>
    <?php if (!empty($bank_account) || !empty($bank_name)): ?>
        <p>Ngân hàng: <strong><?php echo htmlspecialchars($bank_name); ?></strong></p>
        <p>Số tài khoản: <strong><?php echo htmlspecialchars($bank_account); ?></strong></p>
    <?php endif; ?>
    <img src="<?php echo htmlspecialchars($qr_url); ?>" alt="QR thanh toán">
    <p style="margin-top:12px;font-size:14px;color:#cbd5e1">Quét QR để xem nội dung chuyển khoản. Khi nhận được tiền, admin sẽ xác nhận thủ công.</p>
</div>
</body>
</html>
