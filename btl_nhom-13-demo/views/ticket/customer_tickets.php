<?php
require_once __DIR__ . '/../../functions/auth.php';
checkLogin(__DIR__ . '/../../index.php');

$isFragment = isset($_GET['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');

$forceCustomerView = true;

if (getCurrentUser()['role'] !== 'customer') {
    header('Location: ../../index.php');
    exit();
}

$pageTitle = "Vé của tôi";

if (!$isFragment) {
    $self = str_replace('\\', '/', $_SERVER['REQUEST_URI']);
    if (empty($_GET['from_shell'])) {
        $shellUrl = '/btl_nhom-13-demo/views/customer_home.php?load=' . urlencode($self);
        header('Location: ' . $shellUrl);
        exit();
    }
    require_once __DIR__ . '/../../includes/header.php';
}

require_once __DIR__ . '/../../functions/customer_functions.php';
require_once __DIR__ . '/../../functions/ticket_functions.php';
require_once __DIR__ . '/../../functions/payment_gateway.php';

$user = getCurrentUser();
$customer = null;
if ($user && isset($user['id'])) {
    $customer = getCustomerByUserId(intval($user['id']));
}

$tickets = [];
$pendingTickets = [];
$visibleTickets = [];
if ($customer) {
    $all = getTicketsByCustomerId($customer['id']);
    foreach ($all as $tt) {
        $s = strtolower($tt['status'] ?? '');
        if ($s === 'pending_payment' || $s === 'awaiting_confirmation') {
            $pendingTickets[] = $tt;
        } else {
            $visibleTickets[] = $tt;
        }
    }
    $tickets = $visibleTickets;
}
?>
<style>
/* ===== CRITICAL FIX - OVERRIDE GRID LAYOUT ===== */
#mainContent {
    display: block !important;
    padding: 0 !important;
}

#mainContent > div {
    display: block !important;
}

.alert,
.alert.pending-payments,
.page-header,
.tickets-container {
    grid-column: 1 / -1 !important;
    width: 100% !important;
}

* {
    box-sizing: border-box;
}

/* ===== PAGE HEADER ===== */
.page-header {
    padding: 10px 2rem 1rem 2rem !important;
    max-width: 1200px !important;
    margin: 0 auto !important;
}

.page-title {
    font-size: 32px !important;
    font-weight: 900 !important;
    color: #fff !important;
    margin: 0 0 0.5rem 0 !important;
    padding: 0 !important;
}

/* ===== COMPACT PENDING PAYMENTS ===== */
.alert.pending-payments {
    background: rgba(59, 130, 246, 0.06) !important;
    border: 1px solid rgba(59, 130, 246, 0.2) !important;
    border-radius: 10px !important;
    padding: 0.8rem 1.2rem !important;
    margin: 0 auto 1rem !important;
    max-width: 1200px !important;
    width: calc(100% - 4rem) !important;
    height: auto !important;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.15) !important;
}

.pending-intro {
    font-size: 13px !important;
    color: #3b82f6 !important;
    margin: 0 0 0.6rem 0 !important;
    line-height: 1.4 !important;
    font-weight: 600 !important;
}

.pending-intro strong {
    color: #2563eb !important;
    font-weight: 800 !important;
}

.pending-header {
    display: flex !important;
    align-items: center !important;
    gap: 0.4rem !important;
    margin: 0 0 0.6rem 0 !important;
    font-weight: 800 !important;
    font-size: 14px !important;
    color: #1e40af !important;
}

.pending-header .count {
    background: rgba(59, 130, 246, 0.2) !important;
    padding: 0.15rem 0.5rem !important;
    border-radius: 10px !important;
    font-size: 12px !important;
}

.pending-list {
    list-style: none !important;
    padding: 0 !important;
    margin: 0 !important;
    display: grid !important;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)) !important;
    gap: 0.5rem !important;
}

.pending-item {
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    font-size: 13px !important;
    color: #0f172a !important;
    background: rgba(255, 255, 255, 0.04) !important;
    padding: 0.6rem 0.8rem !important;
    border-radius: 6px !important;
    border: 1px solid rgba(59, 130, 246, 0.12) !important;
    transition: all 0.2s ease !important;
}

.pending-item:hover {
    background: rgba(255, 255, 255, 0.06) !important;
    border-color: rgba(59, 130, 246, 0.25) !important;
}

.pending-left {
    display: flex !important;
    align-items: center !important;
    gap: 0.6rem !important;
}

.pending-item .req {
    font-weight: 800 !important;
    color: #3b82f6 !important;
    font-size: 13px !important;
}

.pending-item .amt {
    font-weight: 800 !important;
    color: #2563eb !important;
    font-size: 15px !important;
}

.pending-right {
    display: flex !important;
    align-items: center !important;
    gap: 0.5rem !important;
}

.pending-item .status {
    font-size: 11px !important;
    color: #60a5fa !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.3px !important;
}

.pending-item .pay-link {
    color: #fff !important;
    text-decoration: none !important;
    font-weight: 700 !important;
    background: linear-gradient(135deg, #ff6b35 0%, #ff8c42 100%) !important;
    padding: 0.4rem 0.8rem !important;
    border-radius: 5px !important;
    transition: all 0.2s ease !important;
    font-size: 11px !important;
    text-transform: uppercase !important;
    letter-spacing: 0.3px !important;
}

.pending-item .pay-link:hover {
    background: linear-gradient(135deg, #ff8c42 0%, #ff6b35 100%) !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 3px 8px rgba(255, 107, 53, 0.3) !important;
}

/* ===== TICKETS CONTAINER ===== */
.tickets-container {
    padding: 0 2rem 3rem 2rem !important;
    max-width: 1200px !important;
    margin: 0 auto !important;
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 1rem !important;
    row-gap: 1rem !important;
    column-gap: 1rem !important;
    justify-content: center !important;
}

/* ===== PARKING TICKET CARD ===== */
.parking-ticket {
    background: linear-gradient(to right, #fffbe6 0%, #ffe6c7 32%, #ffffff 32.5%, #ffffff 100%) !important;
    border: 2px solid #444 !important;
    border-radius: 8px !important;
    overflow: visible !important;
    display: flex !important;
    flex-direction: row !important;
    flex: 0 0 calc(50% - 0.5rem) !important; 
    max-width: calc(50% - 0.5rem) !important;
    width: calc(50% - 0.5rem) !important;
    min-height: 280px !important;
    height: auto !important;
    box-shadow: 0 12px 50px rgba(0, 0, 0, 0.5) !important;
    transition: 0.3s ease !important;
    margin: 0 !important;
}
.alert:not(.pending-payments) {
    max-width: 1200px !important;
    margin: 1rem auto 2rem !important;
    padding: 1.5rem 2rem !important;
    background: rgba(251, 191, 36, 0.1) !important;
    border: 1px solid rgba(251, 191, 36, 0.3) !important;
    border-radius: 12px !important;
    color: #fbbf24 !important;
}

.alert a {
    color: #ff6b35 !important;
    text-decoration: underline !important;
}

.empty-card {
    max-width: 1200px !important;
    margin: 0 auto !important;
    padding: 3rem 2rem !important;
    background: rgba(15, 23, 42, 0.6) !important;
    backdrop-filter: blur(20px) !important;
    border-radius: 20px !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    text-align: center !important;
    color: #cbd5e1 !important;
    font-size: 16px !important;
}

/* ===== PARKING TICKET CARD ===== */
.parking-ticket {
    background: linear-gradient(to right, #fffbe6 0%, #ffe6c7 32%, #ffffff 32.5%, #ffffff 100%) !important;
    border: 2px solid #444 !important;
    border-radius: 8px !important;
    overflow: visible !important;
    display: flex !important;
    flex-direction: row !important;
    flex: 0 0 calc(50% - 0.5rem) !important; 
    max-width: calc(50% - 0.5rem) !important;
    min-height: 280px !important;
    height: auto !important;
    box-shadow: 0 12px 50px rgba(0, 0, 0, 0.5) !important;
    transition: 0.3s ease !important;
}

.parking-ticket:hover {
    transform: translateY(-4px) !important;
    box-shadow: 0 16px 60px rgba(0, 0, 0, 0.6), inset 0 1px 0 rgba(255,255,255,0.1) !important;
}

/* ===== TICKET LEFT SECTION ===== */
.ticket-left {
    background: linear-gradient(135deg, #fffbe6 0%, #ffe6c7 100%) !important;
    color: #222 !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: center !important;
    align-items: center !important;
    gap: 1.2rem !important;
    width: 150px !important;
    min-width: 150px !important;
    padding: 2rem 1rem !important;
    flex-shrink: 0 !important;
    border-right: 3px dashed #666 !important;
}

.ticket-logo {
    height: 60px !important;
    width: auto !important;
    object-fit: contain !important;
    filter: drop-shadow(0 2px 8px rgba(255,107,53,0.25)) !important;
}

.ticket-title {
    font-size: 18px !important;
    font-weight: 700 !important;
    letter-spacing: 2px !important;
    text-align: center !important;
    margin: 0 !important;
    margin-bottom: 50px;
}

.ticket-type-badge {
    background: linear-gradient(135deg, #ff6b35 0%, #ff8c42 100%) !important;
    color: white !important;
    padding: 0.6rem 1.2rem !important;
    border-radius: 4px !important;
    font-size: 13px !important;
    font-weight: 800 !important;
    text-transform: uppercase !important;
    letter-spacing: 1.5px !important;
    box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3) !important;
}

/* ===== TICKET RIGHT SECTION ===== */
.ticket-right {
    flex: 1 !important;
    padding: 2.5rem 2rem !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-between !important;
    background: #fff !important;
}

.ticket-row {
    display: flex !important;
    flex-direction: row !important;
    gap: 2rem !important;
    margin-bottom: 1rem !important;
    flex-wrap: wrap !important;
}

.ticket-info-item {
    display: flex !important;
    flex-direction: column !important;
    gap: 0.4rem !important;
}

.ticket-info-label {
    font-size: 13px !important;
    color: #ff8c42 !important;
    text-transform: uppercase !important;
    font-weight: 700 !important;
    letter-spacing: 0.8px !important;
    line-height: 1.2 !important;
}

.ticket-info-value {
    font-size: 15px !important;
    color: #222 !important;
    font-weight: 700 !important;
    letter-spacing: 0.5px !important;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif !important;
    line-height: 1.3 !important;
    word-break: break-word !important;
}

.ticket-info-value.price {
    font-size: 22px !important;
    color: #ff6b35 !important;
    font-weight: 900 !important;
}

/* ===== TICKET FOOTER ===== */
.ticket-footer {
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    gap: 1rem !important;
    padding-top: 1.2rem !important;
    border-top: 2px dashed #ddd !important;
    margin-top: auto !important;
}

.ticket-code-section {
    flex: 1 !important;
}

.ticket-code-label {
    font-size: 9px !important;
    color: #888 !important;
    text-transform: uppercase !important;
    font-weight: 700 !important;
    margin-bottom: 0.3rem !important;
}

.ticket-code {
    font-size: 17px !important;
    font-weight: 900 !important;
    color: #1a1a1a !important;
    letter-spacing: 2px !important;
    font-family: 'Courier New', monospace !important;
}

.barcode-section {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    gap: 0.3rem !important;
    flex: 0 0 auto !important;
}

.barcode {
    display: flex !important;
    gap: 1px !important;
    align-items: flex-end !important;
    height: 35px !important;
}

.barcode-line {
    background: #1a1a1a !important;
    width: 2px !important;
}

.barcode-line.tall { height: 100% !important; }
.barcode-line.medium { height: 75% !important; }
.barcode-line.short { height: 50% !important; }

.barcode-text {
    font-size: 8px !important;
    color: #1a1a1a !important;
    font-weight: 700 !important;
    letter-spacing: 0.5px !important;
    font-family: 'Courier New', monospace !important;
}

.ticket-status {
    padding: 0.5rem 1rem !important;
    border-radius: 4px !important;
    font-size: 11px !important;
    font-weight: 800 !important;
    text-transform: uppercase !important;
    letter-spacing: 1px !important;
    border: 2px solid !important;
    text-align: center !important;
    flex: 0 0 auto !important;
    white-space: nowrap !important;
}

.ticket-status.active {
    background: rgba(16, 185, 129, 0.12) !important;
    color: #16a34a !important;
    border-color: #16a34a !important;
}

.ticket-status.expired {
    background: rgba(220, 38, 38, 0.12) !important;
    color: #dc2626 !important;
    border-color: #dc2626 !important;
}

/* ===== RESPONSIVE DESIGN ===== */
@media (max-width: 1200px) {
    .parking-ticket {
        width: 100% !important;
        max-width: 100% !important;
        flex: 0 0 100% !important;
    }
}

@media (max-width: 768px) {
    .page-header {
        padding: 110px 1rem 1rem !important;
    }
    
    .page-title {
        font-size: 28px !important;
    }
    
    .tickets-container {
        padding: 0 1rem 2rem !important;
    }
    
    .alert.pending-payments {
        width: calc(100% - 2rem) !important;
        padding: 0.7rem 1rem !important;
        margin: 0.5rem auto 1rem !important;
    }
    
    .pending-list {
        grid-template-columns: 1fr !important;
        gap: 0.4rem !important;
    }
    
    .pending-item {
        flex-direction: column !important;
        align-items: flex-start !important;
        gap: 0.6rem !important;
        padding: 0.8rem !important;
    }
    
    .pending-right {
        flex-direction: row !important;
        align-items: center !important;
        width: 100% !important;
        justify-content: space-between !important;
    }
    
    .pending-item .pay-link {
        flex: 0 0 auto !important;
    }
    
    .parking-ticket {
        flex-direction: column !important;
        min-height: auto !important;
        height: auto !important;
        background: #ffffff !important;
    }
    
    .ticket-left {
        width: 100% !important;
        min-width: 100% !important;
        border-right: none !important;
        border-bottom: 3px dashed #666 !important;
        padding: 1.5rem !important;
    }
    
    .ticket-right {
        padding: 1.5rem !important;
    }
    
    .ticket-row {
        gap: 1rem !important;
    }
    
    .ticket-footer {
        flex-direction: column !important;
        align-items: flex-start !important;
    }
}
</style>

<div class="page-header">
    <h2 class="page-title"><i class="fas fa-ticket-alt"></i> Vé của tôi</h2>
</div>

<?php if (!$customer): ?>
    <div class="alert">Không tìm thấy hồ sơ khách hàng. Vui lòng cập nhật <a href="/btl_nhom-13-demo/views/customer/edit_profile.php">hồ sơ</a> để bắt đầu đặt vé.</div>
<?php else: ?>
    <?php if (!empty($pendingTickets)): ?>
        <div class="alert pending-payments">
            <div class="pending-intro">Bạn có <strong><?php echo count($pendingTickets); ?> yêu cầu vé</strong> đang chờ thanh toán. Vui lòng hoàn tất thanh toán để kích hoạt vé.</div>
            <div class="pending-header"><strong>Yêu cầu đang chờ thanh toán</strong> <span class="count">(<?php echo count($pendingTickets); ?>)</span></div>
            <ul class="pending-list">
                <?php foreach ($pendingTickets as $pt): ?>
                    <li class="pending-item">
                        <div class="pending-left">
                            <div class="req">#<?php echo $pt['id']; ?></div>
                            <div class="amt"><?php echo number_format($pt['price'],0,',','.'); ?> đ</div>
                        </div>
                        <div class="pending-right">
                            <div class="status">Chờ thanh toán</div>
                            <a class="pay-link" href="/btl_nhom-13-demo/views/transaction/payment_qr.php?id=<?php $p = get_payment_by_ticket_id($pt['id']); echo $p ? $p['id'] : ''; ?>" target="_blank">Xem QR</a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <?php if (empty($tickets)): ?>
        <div class="empty-card">Bạn chưa đặt vé nào.</div>
    <?php else: ?>
        <div class="tickets-container">
            <?php foreach($tickets as $t): ?>
                <?php
                    $typeLabel = ($t['ticket_type']=='day') ? 'Vé ngày' : (($t['ticket_type']=='month')?'Vé tháng':'Vé năm');
                    $start = date('d/m/Y', strtotime($t['start_date']));
                    $end = date('d/m/Y', strtotime($t['end_date']));
                    $price = number_format($t['price'],0,',','.');
                    $s = isset($t['status']) ? strtolower($t['status']) : '';
                    if ($s === 'active') {
                        $statusClass = 'active';
                        $statusText = '✓ HỮU LỰC';
                    } elseif ($s === 'pending_payment' || $s === 'awaiting_confirmation') {
                        $statusClass = 'processing';
                        $statusText = '● ĐANG XỬ LÝ';
                    } else {
                        $statusClass = 'expired';
                        $statusText = '✗ HẾT HẠN';
                    }
                    $ticketId = $t['id'] ?? 'GU-XE-001';
                    $licensePlate = htmlspecialchars($t['license_plate'] ?? $customer['license_plate']);
                ?>
                <div class="parking-ticket">
                    <div class="ticket-left">
                        <img src="/btl_nhom-13-demo/images/guxe-logo.png" alt="GỬI XE" class="ticket-logo" onerror="this.style.display='none'">
                        <h3 class="ticket-title">GỬI XE</h3>
                        <div class="ticket-type-badge"><?php echo htmlspecialchars($typeLabel); ?></div>
                    </div>
                    
                    <div class="ticket-right">
                        <div class="ticket-row">
                            <div class="ticket-info-item">
                                <span class="ticket-info-label">Biển số xe</span>
                                <span class="ticket-info-value"><?php echo $licensePlate; ?></span>
                            </div>
                            <div class="ticket-info-item">
                                <span class="ticket-info-label">Từ ngày</span>
                                <span class="ticket-info-value"><?php echo $start; ?></span>
                            </div>
                            <div class="ticket-info-item">
                                <span class="ticket-info-label">Đến ngày</span>
                                <span class="ticket-info-value"><?php echo $end; ?></span>
                            </div>
                        </div>
                        
                        <div class="ticket-row">
                            <div class="ticket-info-item">
                                <span class="ticket-info-label">Loại vé</span>
                                <span class="ticket-info-value"><?php echo htmlspecialchars($typeLabel); ?></span>
                            </div>
                            <div class="ticket-info-item">
                                <span class="ticket-info-label">Giá vé</span>
                                <span class="ticket-info-value price"><?php echo $price; ?>₫</span>
                            </div>
                        </div>
                        
                        <div class="ticket-footer">
                            <div class="ticket-code-section">
                                <div class="ticket-code-label">Mã vé</div>
                                <div class="ticket-code"><?php echo htmlspecialchars($ticketId); ?></div>
                            </div>
                            
                            <div class="barcode-section">
                                <div class="barcode">
                                    <div class="barcode-line tall"></div>
                                    <div class="barcode-line short"></div>
                                    <div class="barcode-line medium"></div>
                                    <div class="barcode-line tall"></div>
                                    <div class="barcode-line short"></div>
                                    <div class="barcode-line medium"></div>
                                    <div class="barcode-line tall"></div>
                                    <div class="barcode-line short"></div>
                                    <div class="barcode-line tall"></div>
                                </div>
                                <span class="barcode-text"><?php echo htmlspecialchars($ticketId); ?></span>
                            </div>
                            
                            <div class="ticket-status <?php echo $statusClass; ?>"><?php echo $statusText; ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php if (!$isFragment) require_once __DIR__ . '/../../includes/header.php'; ?>