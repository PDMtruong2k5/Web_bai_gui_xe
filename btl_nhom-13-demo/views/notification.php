<?php
require_once __DIR__ . '/../functions/auth.php';
checkLogin(__DIR__ . '/../index.php');
requireRole('admin', __DIR__ . '/../index.php');

$pageTitle = "Quản lý thông báo";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../functions/notification_functions.php';

$notifications = getAllNotifications();
$totalNotifications = count($notifications);
$activeNotifications = count(array_filter($notifications, fn($n) => $n['is_active']));
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="notification-dashboard">
    <!-- Header -->
    <div class="dashboard-header">
        <div class="header-content">
            <div class="title-group">
                <h1>Quản lý thông báo</h1>
                <p>Tạo và quản lý thông báo hiển thị cho người dùng</p>
            </div>
            <button class="btn-add" onclick="openNotificationModal('create')">
                <span class="btn-icon">+</span>
                <span>Thêm thông báo</span>
            </button>
        </div>
    </div>

    <!-- Alerts -->
    <?php if (isset($_GET['success'])): ?>
    <div class="alert success">
        <div class="alert-icon">✓</div>
        <span><?php echo htmlspecialchars($_GET['success']); ?></span>
        <button class="alert-close" onclick="this.parentElement.remove()">×</button>
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
    <div class="alert error">
        <div class="alert-icon">!</div>
        <span><?php echo htmlspecialchars($_GET['error']); ?></span>
        <button class="alert-close" onclick="this.parentElement.remove()">×</button>
    </div>
    <?php endif; ?>

    <!-- Stats Overview -->
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon-wrapper blue">
                <i class="fas fa-bell"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value"><?php echo $totalNotifications; ?></div>
                <div class="stat-label">Tổng thông báo</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-wrapper green">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value"><?php echo $activeNotifications; ?></div>
                <div class="stat-label">Thông báo hoạt động</div>
            </div>
        </div>
    </div>

    <!-- Notifications Table -->
    <div class="content-card">
        <div class="card-header">
            <h2>Danh sách thông báo</h2>
            <span class="count-badge"><?php echo $totalNotifications; ?></span>
        </div>
        <div class="card-body">
            <?php if (empty($notifications)): ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-bell"></i>
                    </div>
                    <h3>Chưa có thông báo nào</h3>
                    <p>Bắt đầu bằng cách tạo thông báo đầu tiên của bạn</p>
                    <button class="btn-primary" onclick="openNotificationModal('create')">
                        <span class="btn-icon">+</span>
                        <span>Thêm thông báo</span>
                    </button>
                </div>
            <?php else: ?>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Tiêu đề</th>
                                <th>Loại</th>
                                <th>Trạng thái</th>
                                <th>Ngày tạo</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($notifications as $notification): ?>
                            <tr>
                                <td>
                                    <div class="notification-title-cell">
                                        <strong><?php echo htmlspecialchars($notification['title']); ?></strong>
                                        <p class="notification-preview"><?php echo substr(htmlspecialchars($notification['message']), 0, 50) . (strlen($notification['message']) > 50 ? '...' : ''); ?></p>
                                    </div>
                                </td>
                                <td>
                                    <span class="type-badge type-<?php echo $notification['type']; ?>">
                                        <?php 
                                            $types = ['info' => 'Thông tin', 'success' => 'Thành công', 'warning' => 'Cảnh báo', 'error' => 'Lỗi'];
                                            echo $types[$notification['type']] ?? $notification['type'];
                                        ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="status-toggle">
                                        <input type="checkbox" class="status-checkbox" 
                                               data-id="<?php echo $notification['id']; ?>"
                                               <?php echo $notification['is_active'] ? 'checked' : ''; ?>
                                               onchange="toggleNotification(<?php echo $notification['id']; ?>, this.checked)">
                                        <label></label>
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($notification['created_at'])); ?></small>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="action-btn edit" onclick="editNotification(<?php echo $notification['id']; ?>, '<?php echo htmlspecialchars($notification['title'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($notification['message'], ENT_QUOTES); ?>', '<?php echo $notification['type']; ?>')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="action-btn delete" onclick="deleteNotification(<?php echo $notification['id']; ?>, '<?php echo htmlspecialchars($notification['title'], ENT_QUOTES); ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Notification Modal -->
<div id="notificationModal" class="modal">
    <div class="modal-backdrop" onclick="closeNotificationModal()"></div>
    <div class="modal-container large">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle">Thêm thông báo mới</h3>
                <button class="modal-close" onclick="closeNotificationModal()">×</button>
            </div>
            <form id="notificationForm" class="modal-body">
                <input type="hidden" name="action" id="form_action" value="create">
                <input type="hidden" name="id" id="form_id" value="">
                
                <div class="form-group">
                    <label class="form-label">Tiêu đề <span class="required">*</span></label>
                    <input type="text" name="title" id="form_title" class="form-input" placeholder="Nhập tiêu đề thông báo" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Nội dung <span class="required">*</span></label>
                    <textarea name="message" id="form_message" class="form-textarea" rows="5" placeholder="Nhập nội dung thông báo" required></textarea>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Loại thông báo <span class="required">*</span></label>
                        <select name="type" id="form_type" class="form-input" required>
                            <option value="info">Thông tin</option>
                            <option value="success">Thành công</option>
                            <option value="warning">Cảnh báo</option>
                            <option value="error">Lỗi</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Trạng thái</label>
                        <div class="status-toggle-form">
                            <input type="checkbox" name="is_active" id="form_is_active" value="1" checked>
                            <label for="form_is_active">Kích hoạt ngay</label>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="closeNotificationModal()">Hủy</button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-check"></i>
                        <span>Lưu lại</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div id="deleteModal" class="modal">
    <div class="modal-backdrop" onclick="closeDeleteModal()"></div>
    <div class="modal-container small">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Xác nhận xóa</h3>
                <button class="modal-close" onclick="closeDeleteModal()">×</button>
            </div>
            <div class="modal-body">
                <div class="delete-warning">
                    <div class="warning-icon">⚠</div>
                    <p>Bạn có chắc chắn muốn xóa thông báo <strong id="delete_name"></strong>?</p>
                    <small>Hành động này không thể hoàn tác</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeDeleteModal()">Hủy</button>
                <button type="button" class="btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash"></i>
                    <span>Xóa</span>
                </button>
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
    --green: #37b24d;
    --red: #f03e3e;
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

.notification-dashboard {
    max-width: 1200px;
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

.btn-add {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    background: var(--white);
    color: var(--gray-900);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius);
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: var(--shadow-sm);
}

.btn-add:hover {
    border-color: var(--gray-300);
    box-shadow: var(--shadow);
    transform: translateY(-1px);
}

.btn-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 20px;
    height: 20px;
    background: var(--gray-900);
    color: var(--white);
    border-radius: 4px;
    font-size: 16px;
    font-weight: 600;
    line-height: 1;
}

/* Alerts */
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

/* Stats */
.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}

.stat-card {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 24px;
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    transition: all 0.2s;
}

.stat-card:hover {
    box-shadow: var(--shadow);
    transform: translateY(-2px);
}

.stat-icon-wrapper {
    width: 56px;
    height: 56px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: var(--radius);
    font-size: 24px;
}

.stat-icon-wrapper.blue {
    background: rgba(76, 110, 245, 0.1);
    color: var(--blue);
}

.stat-icon-wrapper.green {
    background: rgba(55, 178, 77, 0.1);
    color: var(--green);
}

.stat-details {
    flex: 1;
}

.stat-value {
    font-size: 32px;
    font-weight: 700;
    color: var(--gray-900);
    line-height: 1;
    margin-bottom: 4px;
}

.stat-label {
    font-size: 14px;
    color: var(--gray-600);
    font-weight: 500;
}

/* Content Card */
.content-card {
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-sm);
    margin-bottom: 24px;
    overflow: hidden;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 24px;
    border-bottom: 1px solid var(--gray-100);
}

.card-header h2 {
    font-size: 18px;
    font-weight: 600;
    color: var(--gray-900);
}

.count-badge {
    padding: 4px 12px;
    background: var(--gray-100);
    color: var(--gray-700);
    border-radius: 12px;
    font-size: 13px;
    font-weight: 600;
}

.card-body {
    padding: 24px;
}

/* Table */
.table-wrapper {
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table thead {
    border-bottom: 1px solid var(--gray-200);
}

.data-table th {
    padding: 12px 16px;
    text-align: left;
    font-size: 12px;
    font-weight: 600;
    color: var(--gray-600);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.data-table td {
    padding: 16px;
    border-bottom: 1px solid var(--gray-100);
}

.data-table tbody tr:hover {
    background: var(--gray-50);
}

.notification-title-cell strong {
    display: block;
    color: var(--gray-900);
    font-size: 14px;
    margin-bottom: 4px;
}

.notification-preview {
    font-size: 12px;
    color: var(--gray-600);
    margin: 0;
}

.type-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.type-info {
    background: rgba(76, 110, 245, 0.1);
    color: var(--blue);
}

.type-success {
    background: rgba(55, 178, 77, 0.1);
    color: var(--green);
}

.type-warning {
    background: rgba(253, 126, 20, 0.1);
    color: var(--orange);
}

.type-error {
    background: rgba(240, 62, 62, 0.1);
    color: var(--red);
}

.status-toggle {
    display: flex;
    align-items: center;
}

.status-checkbox {
    display: none;
}

.status-toggle label {
    width: 44px;
    height: 24px;
    background: var(--gray-300);
    border-radius: 12px;
    position: relative;
    cursor: pointer;
    transition: all 0.2s;
}

.status-checkbox:checked + label {
    background: var(--green);
}

.status-toggle label::after {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    background: var(--white);
    border-radius: 50%;
    top: 2px;
    left: 2px;
    transition: left 0.2s;
}

.status-checkbox:checked + label::after {
    left: 22px;
}

.text-muted {
    color: var(--gray-600);
}

.action-buttons {
    display: flex;
    gap: 6px;
}

.action-btn {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--gray-100);
    border: none;
    border-radius: 6px;
    color: var(--gray-600);
    cursor: pointer;
    transition: all 0.2s;
}

.action-btn:hover {
    background: var(--gray-200);
    color: var(--gray-900);
}

.action-btn.delete:hover {
    background: rgba(240, 62, 62, 0.1);
    color: var(--red);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 80px 24px;
}

.empty-icon {
    font-size: 64px;
    color: var(--gray-300);
    margin-bottom: 20px;
}

.empty-state h3 {
    font-size: 20px;
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: 8px;
}

.empty-state p {
    font-size: 15px;
    color: var(--gray-600);
    margin-bottom: 24px;
}

/* Modal */
.modal {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 1000;
    align-items: center;
    justify-content: center;
}

.modal.show {
    display: flex;
}

.modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.4);
    backdrop-filter: blur(4px);
}

.modal-container {
    position: relative;
    width: 90%;
    max-width: 500px;
    animation: modalSlide 0.3s ease;
}

.modal-container.large {
    max-width: 600px;
}

.modal-container.small {
    max-width: 400px;
}

@keyframes modalSlide {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.modal-content {
    background: var(--white);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    overflow: hidden;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 24px;
    border-bottom: 1px solid var(--gray-100);
}

.modal-header h3 {
    font-size: 18px;
    font-weight: 600;
    color: var(--gray-900);
}

.modal-close {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    border: none;
    color: var(--gray-500);
    font-size: 28px;
    cursor: pointer;
    border-radius: 6px;
    transition: all 0.2s;
}

.modal-close:hover {
    background: var(--gray-100);
    color: var(--gray-700);
}

.modal-body {
    padding: 24px;
}

.modal-footer {
    padding: 16px 24px;
    border-top: 1px solid var(--gray-100);
    display: flex;
    gap: 12px;
    justify-content: flex-end;
}

/* Form */
.form-group {
    margin-bottom: 20px;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    font-size: 14px;
    font-weight: 500;
    color: var(--gray-700);
}

.required {
    color: var(--red);
}

.form-input,
.form-textarea {
    width: 100%;
    padding: 12px 14px;
    background: var(--white);
    border: 1px solid var(--gray-300);
    border-radius: var(--radius-sm);
    font-size: 14px;
    color: var(--gray-900);
    font-family: inherit;
    transition: all 0.2s;
}

.form-input:focus,
.form-textarea:focus {
    outline: none;
    border-color: var(--gray-900);
}

.form-textarea {
    resize: vertical;
    min-height: 100px;
}

.status-toggle-form {
    display: flex;
    align-items: center;
    gap: 8px;
}

.status-toggle-form input {
    width: 44px;
    height: 24px;
    cursor: pointer;
    appearance: none;
    background: var(--gray-300);
    border-radius: 12px;
    outline: none;
    transition: all 0.2s;
    position: relative;
}

.status-toggle-form input:checked {
    background: var(--green);
}

.status-toggle-form input::before {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    background: var(--white);
    border-radius: 50%;
    top: 2px;
    left: 2px;
    transition: left 0.2s;
}

.status-toggle-form input:checked::before {
    left: 22px;
}

.status-toggle-form label {
    font-size: 14px;
    color: var(--gray-700);
    cursor: pointer;
}

.form-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    margin-top: 24px;
}

/* Buttons */
.btn-primary,
.btn-secondary,
.btn-danger {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 20px;
    border: none;
    border-radius: var(--radius-sm);
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
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

.btn-danger {
    background: var(--red);
    color: var(--white);
}

.btn-danger:hover {
    background: #e03131;
}

/* Delete Warning */
.delete-warning {
    text-align: center;
    padding: 20px;
}

.warning-icon {
    font-size: 48px;
    color: var(--orange);
    margin-bottom: 16px;
}

.delete-warning p {
    font-size: 15px;
    color: var(--gray-700);
    line-height: 1.6;
    margin-bottom: 8px;
}

.delete-warning strong {
    color: var(--gray-900);
    font-weight: 600;
}

.delete-warning small {
    font-size: 13px;
    color: var(--gray-500);
}

/* Responsive */
@media (max-width: 768px) {
    .header-content {
        flex-direction: column;
        align-items: stretch;
    }

    .btn-add {
        justify-content: center;
    }

    .form-row {
        grid-template-columns: 1fr;
    }

    .modal-container {
        width: 95%;
        margin: 16px;
    }
}
</style>

<script>
const notificationModal = document.getElementById('notificationModal');
const deleteModal = document.getElementById('deleteModal');
let deleteNotificationId = null;

function openNotificationModal(mode = 'create', data = {}) {
    notificationModal.classList.add('show');
    const title = document.getElementById('modalTitle');
    const formAction = document.getElementById('form_action');
    const formId = document.getElementById('form_id');
    
    if (mode === 'create') {
        title.textContent = 'Thêm thông báo mới';
        formAction.value = 'create';
        formId.value = '';
        document.getElementById('form_title').value = '';
        document.getElementById('form_message').value = '';
        document.getElementById('form_type').value = 'info';
        document.getElementById('form_is_active').checked = true;
    } else {
        title.textContent = 'Chỉnh sửa thông báo';
        formAction.value = 'edit';
        formId.value = data.id || '';
        document.getElementById('form_title').value = data.title || '';
        document.getElementById('form_message').value = data.message || '';
        document.getElementById('form_type').value = data.type || 'info';
    }
}

function closeNotificationModal() {
    notificationModal.classList.remove('show');
    document.getElementById('notificationForm').reset();
}

function openDeleteModal() {
    deleteModal.classList.add('show');
}

function closeDeleteModal() {
    deleteModal.classList.remove('show');
    deleteNotificationId = null;
}

function editNotification(id, title, message, type) {
    openNotificationModal('edit', { id: id, title: title, message: message, type: type });
}

function deleteNotification(id, title) {
    deleteNotificationId = id;
    document.getElementById('delete_name').textContent = title;
    openDeleteModal();
}

function toggleNotification(id, isActive) {
    const formData = new FormData();
    formData.append('action', 'toggle');
    formData.append('id', id);
    formData.append('is_active', isActive ? 1 : 0);
    
    fetch('../handle/notification_process.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(json => {
        if (!json.success) {
            alert('Cập nhật trạng thái thất bại');
            location.reload();
        }
    })
    .catch(err => console.error(err));
}

// Form Submit
document.getElementById('notificationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const isActive = document.getElementById('form_is_active').checked ? 1 : 0;
    formData.set('is_active', isActive);
    
    fetch('../handle/notification_process.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(json => {
        if (json && json.success) {
            closeNotificationModal();
            location.reload();
        } else {
            alert(json.message || 'Có lỗi xảy ra');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Lỗi mạng, vui lòng thử lại');
    });
});

// Confirm Delete
document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (!deleteNotificationId) return;
    
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', deleteNotificationId);
    
    fetch('../handle/notification_process.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(json => {
        if (json && json.success) {
            closeDeleteModal();
            location.reload();
        } else {
            alert(json.message || 'Có lỗi xảy ra khi xóa thông báo');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Lỗi mạng, vui lòng thử lại');
    });
});

// Auto-hide alerts
setTimeout(() => {
    document.querySelectorAll('.alert').forEach(alert => {
        alert.style.display = 'none';
    });
}, 5000);
</script>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
