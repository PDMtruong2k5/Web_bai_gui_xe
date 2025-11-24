<?php
require_once __DIR__ . '/../../functions/auth.php';
require_once __DIR__ . '/../../functions/payment_settings.php';
checkLogin(__DIR__ . '/../../index.php');

$user = getCurrentUser();
if (($user['role'] ?? '') !== 'admin') {
    header('Location: ../../index.php');
    exit();
}

$pageTitle = 'Cấu hình thanh toán';
require_once __DIR__ . '/../../includes/header.php';

$settings = load_payment_settings();
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bank_account = $_POST['bank_account'] ?? '';
    $bank_name = $_POST['bank_name'] ?? '';
    $qr_image_path = $settings['qr_image'] ?? '';
    
    // handle file upload
    if (!empty($_FILES['qr_image']['name'])) {
        $up = $_FILES['qr_image'];
        $ext = pathinfo($up['name'], PATHINFO_EXTENSION);
        $destDir = __DIR__ . '/../../data';
        if (!is_dir($destDir)) mkdir($destDir, 0755, true);
        $filename = 'payment_qr_' . time() . '.' . $ext;
        $dest = $destDir . '/' . $filename;
        if (move_uploaded_file($up['tmp_name'], $dest)) {
            $qr_image_path = '/btl_nhom-13-demo/data/' . $filename;
        }
    }

    if (save_payment_settings($bank_account, $bank_name, $qr_image_path)) {
        $message = 'Lưu cấu hình thanh toán thành công!';
        $messageType = 'success';
        $settings = load_payment_settings();
    } else {
        $message = 'Không thể lưu cấu hình.';
        $messageType = 'error';
    }
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="settings-dashboard">
    <!-- Header -->
    <div class="dashboard-header">
        <div class="header-content">
            <div class="title-group">
                <h1>Cấu hình thanh toán</h1>
                <p>Thiết lập thông tin thanh toán và phương thức nhận tiền</p>
            </div>
        </div>
    </div>

    <!-- Message Alert -->
    <?php if ($message): ?>
    <div class="alert <?php echo $messageType; ?>">
        <div class="alert-icon"><?php echo $messageType === 'success' ? '✓' : '!'; ?></div>
        <span><?php echo htmlspecialchars($message); ?></span>
        <button class="alert-close" onclick="this.parentElement.remove()">×</button>
    </div>
    <?php endif; ?>

    <!-- Settings Form -->
    <div class="content-card">
        <div class="card-header">
            <h2>Thông tin thanh toán</h2>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data" class="settings-form">
                <!-- Bank Account Info -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-university"></i>
                        <h3>Thông tin tài khoản ngân hàng</h3>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Tên ngân hàng <span class="required">*</span></label>
                        <input type="text" name="bank_name" class="form-input" 
                            value="<?php echo htmlspecialchars($settings['bank_name'] ?? ''); ?>"
                            placeholder="VD: Vietcombank, Techcombank, etc." required>
                        <small class="form-hint">Tên ngân hàng nhận tiền</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Số tài khoản <span class="required">*</span></label>
                        <input type="text" name="bank_account" class="form-input"
                            value="<?php echo htmlspecialchars($settings['bank_account'] ?? ''); ?>"
                            placeholder="VD: 1012345678" required>
                        <small class="form-hint">Số tài khoản ngân hàng nhận tiền</small>
                    </div>
                </div>

                <!-- QR Code Section -->
                <div class="form-section">
                    <div class="section-title">
                        <i class="fas fa-qrcode"></i>
                        <h3>Mã QR thanh toán</h3>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Hình ảnh mã QR</label>
                        <div class="file-upload-area">
                            <input type="file" name="qr_image" id="qrImageInput" class="file-input" accept="image/*">
                            <label for="qrImageInput" class="file-upload-label">
                                <div class="upload-icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <span class="upload-text">Nhấp để chọn hoặc kéo thả hình ảnh</span>
                                <small>PNG, JPG, GIF tối đa 5MB</small>
                            </label>
                        </div>
                        <small class="form-hint">Hình ảnh mã QR sẽ được hiển thị cho khách hàng khi thanh toán</small>
                    </div>

                    <?php if (!empty($settings['qr_image'])): ?>
                    <div class="qr-preview">
                        <label class="form-label">Mã QR hiện tại</label>
                        <div class="preview-container">
                            <img src="<?php echo htmlspecialchars($settings['qr_image']); ?>" alt="QR Code" class="preview-image">
                            <div class="preview-info">
                                <p><strong>Đường dẫn:</strong></p>
                                <code><?php echo htmlspecialchars($settings['qr_image']); ?></code>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Action Buttons -->
                <div class="form-actions">
                    <a href="../denho.php" class="btn-secondary">
                        <i class="fas fa-times"></i>
                        <span>Hủy</span>
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i>
                        <span>Lưu cấu hình</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Info Section -->
    <div class="info-cards">
        <div class="info-card">
            <div class="info-icon">
                <i class="fas fa-info-circle"></i>
            </div>
            <div class="info-content">
                <h4>Thông tin tài khoản</h4>
                <p>Thông tin tài khoản ngân hàng sẽ được hiển thị trong trang thanh toán để khách hàng chuyển khoản</p>
            </div>
        </div>

        <div class="info-card">
            <div class="info-icon">
                <i class="fas fa-qrcode"></i>
            </div>
            <div class="info-content">
                <h4>Mã QR thanh toán</h4>
                <p>Mã QR này sẽ được hiển thị để khách hàng quét thanh toán nhanh chóng</p>
            </div>
        </div>

        <div class="info-card">
            <div class="info-icon">
                <i class="fas fa-lock"></i>
            </div>
            <div class="info-content">
                <h4>Bảo mật</h4>
                <p>Tất cả thông tin được mã hóa và lưu trữ an toàn trên máy chủ</p>
            </div>
        </div>
    </div>
</div>

<style>
:root {
    --white: #ffffff;
    --off-white: #fafbfc;
    --gray-50: #f8f9fa;
    --gray-100: #f1f3f5;
    --gray-200: #e9ecef;
    --gray-300: #dee2e6;
    --gray-400: #ced4da;
    --gray-500: #adb5bd;
    --gray-600: #868e96;
    --gray-700: #495057;
    --gray-800: #343a40;
    --gray-900: #212529;
    --blue: #4c6ef5;
    --purple: #7950f2;
    --red: #f03e3e;
    --green: #37b24d;
    --orange: #fd7e14;
    --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.02);
    --shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.08);
    --radius-sm: 8px;
    --radius: 12px;
    --radius-lg: 16px;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', Roboto, sans-serif;
    background: var(--gray-50);
    color: var(--gray-900);
    line-height: 1.6;
}

.settings-dashboard {
    max-width: 900px;
    margin: 0 auto;
    padding: 40px 24px;
}

/* Header */
.dashboard-header {
    margin-bottom: 32px;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 24px;
}

.title-group h1 {
    font-size: 32px;
    font-weight: 700;
    letter-spacing: -0.5px;
    color: var(--gray-900);
    margin-bottom: 4px;
}

.title-group p {
    font-size: 15px;
    color: var(--gray-600);
}

/* Alert */
.alert {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 20px;
    background: var(--white);
    border: 1px solid;
    border-radius: var(--radius);
    margin-bottom: 24px;
    box-shadow: var(--shadow-sm);
}

.alert.success {
    border-color: var(--green);
    color: var(--green);
}

.alert.error {
    border-color: var(--red);
    color: var(--red);
}

.alert-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 24px;
    height: 24px;
    font-size: 18px;
    font-weight: 700;
}

.alert-close {
    margin-left: auto;
    background: none;
    border: none;
    font-size: 24px;
    color: inherit;
    opacity: 0.5;
    cursor: pointer;
    transition: opacity 0.2s;
}

.alert-close:hover {
    opacity: 1;
}

/* Content Card */
.content-card {
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    margin-bottom: 32px;
    overflow: hidden;
}

.card-header {
    padding: 24px;
    border-bottom: 1px solid var(--gray-100);
}

.card-header h2 {
    font-size: 20px;
    font-weight: 600;
    color: var(--gray-900);
}

.card-body {
    padding: 32px;
}

/* Form */
.settings-form {
    display: flex;
    flex-direction: column;
    gap: 32px;
}

.form-section {
    padding-bottom: 24px;
    border-bottom: 1px solid var(--gray-100);
}

.form-section:last-of-type {
    border-bottom: none;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 24px;
}

.section-title i {
    font-size: 20px;
    color: var(--blue);
}

.section-title h3 {
    font-size: 16px;
    font-weight: 600;
    color: var(--gray-900);
    margin: 0;
}

.form-group {
    margin-bottom: 24px;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-700);
}

.required {
    color: var(--red);
}

.form-input {
    width: 100%;
    padding: 12px 14px;
    background: var(--white);
    border: 1px solid var(--gray-300);
    border-radius: var(--radius-sm);
    font-size: 14px;
    color: var(--gray-900);
    transition: all 0.2s;
}

.form-input:focus {
    outline: none;
    border-color: var(--gray-900);
    box-shadow: 0 0 0 3px rgba(33, 37, 41, 0.1);
}

.form-hint {
    display: block;
    margin-top: 6px;
    font-size: 13px;
    color: var(--gray-500);
}

/* File Upload */
.file-upload-area {
    position: relative;
}

.file-input {
    display: none;
}

.file-upload-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 12px;
    padding: 32px;
    background: var(--gray-50);
    border: 2px dashed var(--gray-300);
    border-radius: var(--radius);
    cursor: pointer;
    transition: all 0.2s;
}

.file-upload-label:hover {
    border-color: var(--blue);
    background: rgba(76, 110, 245, 0.02);
}

.upload-icon {
    font-size: 32px;
    color: var(--gray-400);
}

.upload-text {
    font-weight: 600;
    color: var(--gray-700);
}

.file-upload-label small {
    color: var(--gray-500);
}

/* QR Preview */
.qr-preview {
    margin-top: 24px;
    padding-top: 24px;
    border-top: 1px solid var(--gray-100);
}

.preview-container {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 16px;
    background: var(--gray-50);
    border-radius: var(--radius);
}

.preview-image {
    width: 120px;
    height: 120px;
    border-radius: var(--radius);
    border: 1px solid var(--gray-200);
    object-fit: cover;
}

.preview-info {
    flex: 1;
}

.preview-info p {
    font-size: 13px;
    color: var(--gray-600);
    margin-bottom: 8px;
}

.preview-info strong {
    color: var(--gray-900);
}

.preview-info code {
    display: block;
    margin-top: 6px;
    padding: 8px 12px;
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: 4px;
    font-size: 12px;
    color: var(--gray-700);
    word-break: break-all;
    font-family: 'Courier New', monospace;
}

/* Form Actions */
.form-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    padding-top: 24px;
    border-top: 1px solid var(--gray-100);
}

.btn-primary,
.btn-secondary {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border: none;
    border-radius: var(--radius-sm);
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
}

.btn-primary {
    background: var(--gray-900);
    color: var(--white);
}

.btn-primary:hover {
    background: var(--gray-800);
    transform: translateY(-1px);
    box-shadow: var(--shadow);
}

.btn-secondary {
    background: var(--white);
    color: var(--gray-700);
    border: 1px solid var(--gray-300);
}

.btn-secondary:hover {
    background: var(--gray-50);
}

/* Info Cards */
.info-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
}

.info-card {
    display: flex;
    gap: 16px;
    padding: 20px;
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
}

.info-icon {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(76, 110, 245, 0.1);
    color: var(--blue);
    border-radius: var(--radius-sm);
    font-size: 20px;
    flex-shrink: 0;
}

.info-content h4 {
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: 6px;
}

.info-content p {
    font-size: 13px;
    color: var(--gray-600);
    line-height: 1.5;
}

/* Responsive */
@media (max-width: 640px) {
    .settings-dashboard {
        padding: 24px 16px;
    }

    .title-group h1 {
        font-size: 24px;
    }

    .form-actions {
        flex-direction: column-reverse;
    }

    .btn-primary,
    .btn-secondary {
        width: 100%;
        justify-content: center;
    }

    .preview-container {
        flex-direction: column;
        align-items: flex-start;
    }

    .info-cards {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
// File upload drag and drop
const fileInput = document.getElementById('qrImageInput');
const uploadLabel = document.querySelector('.file-upload-label');

if (uploadLabel) {
    uploadLabel.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadLabel.style.borderColor = '#4c6ef5';
        uploadLabel.style.background = 'rgba(76, 110, 245, 0.05)';
    });

    uploadLabel.addEventListener('dragleave', () => {
        uploadLabel.style.borderColor = '#dee2e6';
        uploadLabel.style.background = '#f8f9fa';
    });

    uploadLabel.addEventListener('drop', (e) => {
        e.preventDefault();
        uploadLabel.style.borderColor = '#dee2e6';
        uploadLabel.style.background = '#f8f9fa';
        
        if (e.dataTransfer.files.length > 0) {
            fileInput.files = e.dataTransfer.files;
        }
    });
}

// Auto-hide alerts
setTimeout(() => {
    document.querySelectorAll('.alert').forEach(alert => {
        alert.style.display = 'none';
    });
}, 5000);
</script>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
