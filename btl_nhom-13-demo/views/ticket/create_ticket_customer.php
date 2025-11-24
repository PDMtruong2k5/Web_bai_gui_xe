<?php
require_once __DIR__ . '/../../functions/auth.php';
checkLogin(__DIR__ . '/../../index.php');

// Yêu cầu role customer
if (getCurrentUser()['role'] !== 'customer') {
    header('Location: ../../index.php');
    exit();
}

$pageTitle = "Đặt vé";
require_once __DIR__ . '/../../functions/ticket_functions.php';
require_once __DIR__ . '/../../functions/customer_functions.php';

$user = getCurrentUser();

// Support fragment/ajax mode: when included via AJAX we should not render header/footer
$isFragment = isset($_GET['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
// modal_full: render compact page header inside the fragment so the modal looks like the full page
$isModalFull = isset($_GET['modal_full']);

if (!$isFragment) {
    // Force header to render as customer view even if user has admin role but accessing customer page
    $forceCustomerView = true;
    $pageTitle = "Đặt vé";
    require_once __DIR__ . '/../../includes/header.php';
} else {
    // when fragment and modal_full requested, we still output a compact header bar to mimic full page
    if ($isModalFull) {
        // Minimal header HTML to show title & breadcrumb inside modal
        ?>
        <div class="modal-page-header" style="display:flex;justify-content:space-between;align-items:center;padding:12px 18px;border-bottom:1px solid #e9ecef;background:#fff;">
            <div>
                <div style="font-size:14px;color:#6b7280;margin-bottom:4px;">Trang chủ / Đặt vé mới</div>
                <h3 style="margin:0;font-size:20px;color:#111827">Đăng ký vé mới</h3>
            </div>
            <div>
                <button id="modalCloseBtn" style="background:none;border:1px solid #e5e7eb;padding:6px 10px;border-radius:6px;cursor:pointer;">Đóng</button>
            </div>
        </div>
        <div style="padding:18px;background:#fff;">
        <?php
    }
}

// Try to find the customer's record linked to this user
$customer = null;
if ($user && isset($user['id'])) {
    $customer = getCustomerByUserId(intval($user['id']));
}
?>

<div class="container py-4">
    <div class="page-header">
        <h2 class="page-title">
            <i class="fas fa-ticket-alt"></i>
            Đăng ký vé mới
        </h2>
    </div>

    <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        <?php echo htmlspecialchars($_GET['error']); ?>
    </div>
    <?php endif; ?>

    <?php if ($customer): ?>
        <div class="card" style="max-width: 600px;">
            <div class="card-body">
                <form id="bookingFormFragment" action="/btl_nhom-13-demo/handle/ticket_process.php" method="POST">
                    <input type="hidden" name="action" value="create">
                    <input type="hidden" name="customer_id" value="<?php echo htmlspecialchars($customer['id']); ?>">

                    <div class="mb-3">
                        <label class="form-label" style="color: #fff;">Loại vé</label>
                        <select name="ticket_type" class="form-control" required id="ticketType" style="color: #fff;" onchange="updateTicketPrice()">
                            <option value="">Chọn loại vé</option>
                            <option value="day">Vé ngày</option>
                            <option value="month">Vé tháng</option>
                            <option value="year">Vé năm</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="color: #fff;">Ngày bắt đầu</label>
                        <input type="date" name="start_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required style="color: #fff;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="color: #fff;">Giá vé (VNĐ)</label>
                        <input type="number" name="price" id="ticketPrice" class="form-control" style="color: #fff;">
                        <small class="text-muted">Giá vé sẽ được tự động điền theo loại vé</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Đăng ký vé</button>
                        <button type="button" class="btn btn-secondary" id="fragmentCancel">Hủy</button>
                    </div>
                </form>
            </div>
        </div>
        <?php else: ?>
            <?php if ($isFragment && $isModalFull): ?>
                <!-- Khi được nhúng trong modal: chỉ hiển thị thông báo ngắn + nút hành động -->
                <div style="max-width:700px;margin:12px;">
                    <div style="background:#fff3cd;border:1px solid #ffeeba;padding:16px;border-radius:8px;color:#856404;">
                        <strong>Chưa có hồ sơ khách hàng</strong>
                        <p style="margin:8px 0 0;">Chúng tôi không tìm thấy hồ sơ liên kết với tài khoản của bạn. Vui lòng cập nhật hồ sơ trước khi đặt vé để thông tin được lưu và liên kết.</p>
                        <div style="margin-top:12px;display:flex;gap:8px;justify-content:flex-end;">
                            <a href="/btl_nhom-13-demo/views/customer/edit_profile.php" class="btn btn-primary" target="_blank">Cập nhật hồ sơ</a>
                            <button type="button" class="btn btn-secondary" id="fragmentCloseNotice">Đóng</button>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card" style="max-width:700px;">
                    <div class="card-body">
                        <h5 class="card-title">Chưa có thông tin khách hàng</h5>
                        <p>Chúng tôi không tìm thấy hồ sơ khách hàng liên kết với tài khoản của bạn. Vui lòng điền thông tin dưới đây để tạo hồ sơ (dữ liệu sẽ được liên kết với tài khoản của bạn).</p>

                        <form id="createCustomerFormFragment">
                            <div class="mb-3">
                                <label class="form-label">Họ và tên</label>
                                <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($user['username'] ?? '') ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Số điện thoại</label>
                                <input type="text" name="phone" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Biển số</label>
                                <input type="text" name="license_plate" class="form-control" required>
                            </div>
                            <input type="hidden" name="user_id" value="<?= intval($user['id'] ?? 0) ?>">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">Tạo hồ sơ và tiếp tục</button>
                                <button type="button" class="btn btn-secondary" id="fragmentCancel">Hủy</button>
                            </div>
                            <div id="createCustomerMsg" style="margin-top:12px;display:none"></div>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>

    <?php
    // if modal_full, close the wrapper div opened earlier
    if ($isFragment && $isModalFull) {
        echo "</div>"; // close padding wrapper
    }
    ?>
</div>

<script>
const defaultPrices = {
    'day': 10000,
    'month': 200000,
    'year': 2000000
};

// If booking form exists, wire up price change
var typeEl = document.getElementById('ticketType');
if (typeEl) {
    typeEl.addEventListener('change', function() {
        var price = defaultPrices[this.value] || '';
        var priceEl = document.getElementById('ticketPrice');
        if (priceEl) priceEl.value = price;
    });
}

// If create-customer form exists, submit via AJAX to create customer linked to this user
var createForm = document.getElementById('createCustomerForm') || document.getElementById('createCustomerFormFragment');
if (createForm) {
    createForm.addEventListener('submit', function(e){
        e.preventDefault();
        var fd = new FormData(createForm);
        var xhr = new XMLHttpRequest();
        xhr.open('POST', '/btl_nhom-13-demo/handle/customer_process.php?action=create', true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onload = function(){
            var msg = document.getElementById('createCustomerMsg');
            try { var res = JSON.parse(xhr.responseText); } catch(err){ res = null; }
            if (xhr.status === 200 && res && res.success) {
                if (msg) { msg.style.display='block'; msg.style.color='green'; msg.innerText='Tạo hồ sơ thành công, đang nạp form đặt vé...'; }
                // If this fragment is shown in the modal, reload the fragment into the modal body
                var overlay = document.getElementById('bookingModal');
                var modalBody = overlay ? overlay.querySelector('.modal-body') : null;
                if (modalBody) {
                    modalBody.innerHTML = '<p style="text-align:center;">Đang tải...</p>';
                    var r = new XMLHttpRequest();
                    r.open('GET', '/btl_nhom-13-demo/views/ticket/create_ticket_customer.php?modal_full=1', true);
                    r.setRequestHeader('X-Requested-With','XMLHttpRequest');
                    r.onload = function(){
                        if (r.status === 200) {
                            modalBody.innerHTML = r.responseText;
                            // execute scripts inside the new fragment
                            var scripts = modalBody.querySelectorAll('script');
                            scripts.forEach(function(oldScript){
                                var newScript = document.createElement('script');
                                if (oldScript.src) {
                                    newScript.src = oldScript.src; newScript.async = false;
                                    newScript.onload = function(){ if (newScript.parentNode) newScript.parentNode.removeChild(newScript); };
                                    newScript.onerror = function(){ setTimeout(function(){ if (newScript.parentNode) newScript.parentNode.removeChild(newScript); },3000); };
                                    document.body.appendChild(newScript);
                                } else {
                                    try { newScript.textContent = oldScript.textContent; document.body.appendChild(newScript); setTimeout(function(){ if (newScript.parentNode) newScript.parentNode.removeChild(newScript); },0); } catch(e){ try{ eval(oldScript.textContent); }catch(e){} }
                                }
                            });
                        } else {
                            modalBody.innerHTML = '<div class="alert alert-danger">Không thể nạp form đặt vé.</div>';
                        }
                    };
                    r.onerror = function(){ modalBody.innerHTML = '<div class="alert alert-danger">Lỗi khi nạp form</div>'; };
                    r.send();
                } else {
                    // Not in a modal context: fallback to reload the page
                    setTimeout(function(){ window.location.reload(); }, 700);
                }
            } else {
                if (msg) { msg.style.display='block'; msg.style.color='red'; msg.innerText = (res && res.message) ? res.message : 'Có lỗi khi tạo hồ sơ'; }
            }
        };
        xhr.onerror = function(){ var msg = document.getElementById('createCustomerMsg'); if (msg) { msg.style.display='block'; msg.style.color='red'; msg.innerText='Lỗi kết nối'; } };
        xhr.send(fd);
    });
}
</script>

<?php if (!$isFragment) require_once __DIR__ . '/../../includes/footer.php'; ?>

<?php if ($isFragment): ?>
<script>
// Default prices for ticket types
const defaultPrices = { 'day': 10000, 'month': 200000, 'year': 2000000 };

function updateTicketPrice() {
    const ticketType = document.getElementById('ticketType');
    const priceEl = document.getElementById('ticketPrice');
    if (ticketType && priceEl) {
        priceEl.value = defaultPrices[ticketType.value] || '';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function(){
    updateTicketPrice(); // Set initial price if needed
    
    var defaultPrices = { 'day':10000, 'month':200000, 'year':2000000 };
    var ticketType = document.getElementById('ticketType');
    if (ticketType) {
        ticketType.addEventListener('change', function(){
            var priceEl = document.getElementById('ticketPrice');
            if (priceEl) priceEl.value = defaultPrices[this.value] || '';
        });
    }

    // booking form fragment submit via AJAX
    var bookingForm = document.getElementById('bookingFormFragment');
    if (bookingForm) {
                bookingForm.addEventListener('submit', function(e){
            e.preventDefault();
            var fd = new FormData(bookingForm);
            var xhr = new XMLHttpRequest();
            xhr.open('POST', bookingForm.getAttribute('action') + '?', true);
            xhr.setRequestHeader('X-Requested-With','XMLHttpRequest');
            xhr.onload = function(){
                try { var res = JSON.parse(xhr.responseText); } catch(err){ res = null; }
                    if (xhr.status === 200 && res && res.success) {
                    // After ticket created, create a payment record and show QR. Inform user: chờ thanh toán.
                    var ticket = res.ticket;
                    var payXhr = new XMLHttpRequest();
                    payXhr.open('POST', '/btl_nhom-13-demo/handle/customer_payment.php', true);
                    payXhr.setRequestHeader('X-Requested-With','XMLHttpRequest');
                    var fd2 = new FormData();
                    fd2.append('ticket_id', ticket.id);
                    fd2.append('amount', ticket.price);
                        payXhr.onload = function(){
                        try { var pres = JSON.parse(payXhr.responseText); } catch(e){ pres = null; }
                        if (payXhr.status === 200 && pres && pres.success) {
                            if (typeof showPaymentQRInModal === 'function') {
                                // show CHỜ THANH TOÁN message + QR
                                showPaymentQRInModal(pres);
                                return;
                            }
                            // fallback: open QR in new tab but do not redirect
                            window.open('/btl_nhom-13-demo/views/transaction/payment_qr.php?id=' + pres.payment_id, '_blank');
                            alert('Yêu cầu đặt vé đã được tạo. Vui lòng chuyển khoản để hoàn tất.');
                        } else {
                            alert('Yêu cầu đặt vé đã được tạo nhưng không thể tạo giao dịch thanh toán');
                        }
                    };
                    payXhr.onerror = function(){ alert('Lỗi tạo giao dịch thanh toán'); window.location.href = '/btl_nhom-13-demo/views/ticket.php'; };
                    payXhr.send(fd2);
                } else {
                    alert((res && res.message) ? res.message : 'Có lỗi khi đăng ký vé');
                }
            };
            xhr.onerror = function(){ alert('Lỗi kết nối'); };
            xhr.send(fd);
        });
    }

    // create-customer fragment submit via AJAX
    var createForm = document.getElementById('createCustomerFormFragment');
    if (createForm) {
        createForm.addEventListener('submit', function(e){
            e.preventDefault();
            var fd = new FormData(createForm);
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '/btl_nhom-13-demo/handle/customer_process.php?action=create', true);
            xhr.setRequestHeader('X-Requested-With','XMLHttpRequest');
            xhr.onload = function(){
                try { var res = JSON.parse(xhr.responseText); } catch(err){ res = null; }
                var msg = document.getElementById('createCustomerMsg');
                if (xhr.status === 200 && res && res.success) {
                    if (msg) { msg.style.display='block'; msg.style.color='green'; msg.innerText='Tạo hồ sơ thành công, nạp lại...'; }
                    setTimeout(function(){ window.location.reload(); }, 800);
                } else {
                    if (msg) { msg.style.display='block'; msg.style.color='red'; msg.innerText = (res && res.message) ? res.message : 'Có lỗi khi tạo hồ sơ'; }
                }
            };
            xhr.onerror = function(){ var msg = document.getElementById('createCustomerMsg'); if (msg) { msg.style.display='block'; msg.style.color='red'; msg.innerText='Lỗi kết nối'; } };
            xhr.send(fd);
        });
    }

    // fragment cancel buttons close modal if present
    document.querySelectorAll('#fragmentCancel').forEach(function(btn){
        btn.addEventListener('click', function(){
            var overlay = document.getElementById('bookingModal');
            if (overlay) overlay.style.display = 'none';
        });
    });
    // fragment notice close button
    var noticeClose = document.getElementById('fragmentCloseNotice');
    if (noticeClose) {
        noticeClose.addEventListener('click', function(){
            var overlay = document.getElementById('bookingModal');
            if (overlay) overlay.style.display = 'none';
        });
    }
});
</script>
<?php endif; ?>