<?php
require_once __DIR__ . '/../functions/auth.php';
checkLogin(__DIR__ . '/../index.php');
requireRole('admin', __DIR__ . '/../index.php');

$pageTitle = "Quản lý khách hàng";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../functions/customer_functions.php';

// Lấy thống kê
$customers = getAllCustomers();
$totalCustomers = count($customers);
$customersWithVehicles = array_reduce($customers, function($carry, $customer) {
    return $carry + (!empty($customer['license_plate']) ? 1 : 0);
}, 0);
$newCustomersThisMonth = array_reduce($customers, function($carry, $customer) {
    return $carry + (date('Y-m', strtotime($customer['created_at'] ?? '')) === date('Y-m') ? 1 : 0);
}, 0);
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="customer-dashboard">
    <!-- Header -->
    <div class="dashboard-header">
        <div class="header-content">
            <div class="title-group">
                <h1>Quản lý khách hàng</h1>
                <p>Danh sách và quản lý thông tin khách hàng</p>
            </div>
            <button class="btn-add" id="createCustomerBtn" onclick="openCustomerModal('create')">
                <span class="btn-icon">+</span>
                <span>Thêm khách hàng</span>
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
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value"><?php echo $totalCustomers; ?></div>
                <div class="stat-label">Tổng khách hàng</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-wrapper purple">
                <i class="fas fa-car"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value"><?php echo $customersWithVehicles; ?></div>
                <div class="stat-label">Có xe đăng ký</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-wrapper green">
                <i class="fas fa-user-plus"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value"><?php echo $newCustomersThisMonth; ?></div>
                <div class="stat-label">Mới tháng này</div>
            </div>
        </div>
    </div>

    <!-- Customer List Table -->
    <div class="content-card">
        <div class="card-header">
            <h2>Danh sách chi tiết</h2>
            <span class="count-badge"><?php echo $totalCustomers; ?></span>
        </div>
        <div class="card-body">
            <?php if (empty($customers)): ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Chưa có khách hàng nào</h3>
                    <p>Bắt đầu bằng cách thêm khách hàng đầu tiên của bạn</p>
                    <button class="btn-primary" onclick="openCustomerModal('create')">
                        <span class="btn-icon">+</span>
                        <span>Thêm khách hàng</span>
                    </button>
                </div>
            <?php else: ?>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Khách hàng</th>
                                <th>Số điện thoại</th>
                                <th>Biển số xe</th>
                                <th>Ngày đăng ký</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td>
                                    <div class="table-user-info">
                                        <div class="table-user-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div>
                                            <div class="user-name"><?php echo htmlspecialchars($customer["name"] ?? 'N/A'); ?></div>
                                            <div class="user-id">ID: <?php echo $customer['id']; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="tel:<?php echo $customer['phone'] ?? ''; ?>" class="phone-link">
                                        <i class="fas fa-phone"></i>
                                        <?php echo htmlspecialchars($customer["phone"] ?? 'N/A'); ?>
                                    </a>
                                </td>
                                <td>
                                    <?php if (!empty($customer["license_plate"])): ?>
                                        <span class="license-plate-badge">
                                            <i class="fas fa-car"></i>
                                            <?php echo htmlspecialchars($customer["license_plate"]); ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="no-vehicle">Chưa đăng ký</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo $customer["created_at"] ? date('d/m/Y', strtotime($customer["created_at"])) : 'N/A'; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="action-btn edit" onclick="editCustomer(<?php echo $customer['id']; ?>, '<?php echo htmlspecialchars($customer['name'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($customer['phone'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($customer['license_plate'], ENT_QUOTES); ?>')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="action-btn delete" onclick="deleteCustomer(<?php echo $customer['id']; ?>)">
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

<!-- Customer Modal -->
<div id="customerModal" class="modal">
    <div class="modal-backdrop" onclick="closeCustomerModal()"></div>
    <div class="modal-container">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="customerModalTitle">Thêm khách hàng mới</h3>
                <button class="modal-close" onclick="closeCustomerModal()">×</button>
            </div>
            <form id="customerForm" class="modal-body">
                <input type="hidden" name="action" id="form_action" value="create">
                <input type="hidden" name="id" id="form_id" value="">
                
                <div class="form-group">
                    <label class="form-label">Tên khách hàng <span class="required">*</span></label>
                    <input type="text" name="name" id="form_name" class="form-input" placeholder="Nhập tên khách hàng" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Số điện thoại <span class="required">*</span></label>
                    <input type="tel" name="phone" id="form_phone" class="form-input" placeholder="Nhập số điện thoại" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Biển số xe</label>
                    <input type="text" name="license_plate" id="form_license_plate" class="form-input" placeholder="Nhập biển số xe">
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="closeCustomerModal()">Hủy</button>
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
                    <p>Bạn có chắc chắn muốn xóa khách hàng <strong id="delete_customer_name"></strong>?</p>
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

.customer-dashboard {
    max-width: 1400px;
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
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
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

.stat-icon-wrapper.purple {
    background: rgba(121, 80, 242, 0.1);
    color: var(--purple);
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

.table-user-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.table-user-avatar {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--gray-100);
    color: var(--gray-700);
    border-radius: var(--radius-sm);
    font-size: 16px;
}

.user-name {
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-900);
}

.user-id {
    font-size: 12px;
    color: var(--gray-500);
    font-family: 'Courier New', monospace;
}

.phone-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: var(--blue);
    text-decoration: none;
    font-size: 13px;
    transition: color 0.2s;
}

.phone-link:hover {
    color: var(--gray-900);
}

.license-plate-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: var(--gray-100);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-sm);
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-700);
}

.no-vehicle {
    font-size: 13px;
    color: var(--gray-500);
    font-style: italic;
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
@media (max-width: 968px) {
    .header-content {
        flex-direction: column;
        align-items: stretch;
    }

    .btn-add {
        justify-content: center;
    }

    .stats-container {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 640px) {
    .customer-dashboard {
        padding: 24px 16px;
    }

    .stats-container {
        grid-template-columns: 1fr;
    }

    .modal-container {
        width: 95%;
        margin: 16px;
    }
}
</style>

<script>
// Modal Functions
const customerModal = document.getElementById('customerModal');
const deleteModal = document.getElementById('deleteModal');
let deleteCustomerId = null;

function openCustomerModal(mode = 'create', data = {}) {
    customerModal.classList.add('show');
    const title = document.getElementById('customerModalTitle');
    const formAction = document.getElementById('form_action');
    const formId = document.getElementById('form_id');
    
    if (mode === 'create') {
        title.textContent = 'Thêm khách hàng mới';
        formAction.value = 'create';
        formId.value = '';
        document.getElementById('form_name').value = '';
        document.getElementById('form_phone').value = '';
        document.getElementById('form_license_plate').value = '';
    } else {
        title.textContent = 'Chỉnh sửa khách hàng';
        formAction.value = 'edit';
        formId.value = data.id || '';
        document.getElementById('form_name').value = data.name || '';
        document.getElementById('form_phone').value = data.phone || '';
        document.getElementById('form_license_plate').value = data.license_plate || '';
    }
}

function closeCustomerModal() {
    customerModal.classList.remove('show');
    document.getElementById('customerForm').reset();
}

function editCustomer(id, name, phone, licensePlate) {
    openCustomerModal('edit', {
        id: id,
        name: name,
        phone: phone,
        license_plate: licensePlate
    });
}

function deleteCustomer(id) {
    deleteCustomerId = id;
    document.getElementById('delete_customer_name').textContent = '#' + id;
    deleteModal.classList.add('show');
}

function closeDeleteModal() {
    deleteModal.classList.remove('show');
    deleteCustomerId = null;
}

// Form Submit
document.getElementById('customerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('../handle/customer_process.php', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(r => r.json())
    .then(json => {
        if (json && json.success) {
            closeCustomerModal();
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
    if (!deleteCustomerId) return;
    
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', deleteCustomerId);
    
    fetch('../handle/customer_process.php', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(r => r.json())
    .then(json => {
        if (json && json.success) {
            closeDeleteModal();
            location.reload();
        } else {
            alert(json.message || 'Có lỗi xảy ra khi xóa khách hàng');
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
