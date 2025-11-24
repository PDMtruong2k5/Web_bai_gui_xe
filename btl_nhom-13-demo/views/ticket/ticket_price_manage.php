    <?php
require_once __DIR__ . '/../../functions/auth.php';
checkLogin(__DIR__ . '/../../index.php');
requireRole('admin', __DIR__ . '/../../index.php');

$pageTitle = "Quản lý giá vé";
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../functions/ticket_functions.php';
require_once __DIR__ . '/../../functions/db_connection.php';

// Lấy các loại vé từ bảng tickets
$conn = getDbConnection();
$ticketTypes = [];

if ($conn) {
    $result = mysqli_query($conn, "SELECT DISTINCT ticket_type FROM tickets ORDER BY ticket_type ASC");
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $ticketTypes[] = $row['ticket_type'];
        }
    }
    mysqli_close($conn);
}

// Nếu không có dữ liệu, dùng mặc định
if (empty($ticketTypes)) {
    $ticketTypes = ['day', 'month', 'year'];
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="price-manage-dashboard">
    <!-- Header -->
    <div class="dashboard-header">
        <div class="header-content">
            <div class="title-group">
                <h1>Quản lý giá vé</h1>
                <p>Sửa giá vé mặc định cho các loại vé (ngày, tháng, năm)</p>
            </div>
        </div>
    </div>

    <!-- SECTION: Giá vé mặc định -->
    <div class="content-card">
        <div class="card-header">
            <h2>Sửa giá vé</h2>
            <span class="badge-info">Chọn loại vé để sửa giá</span>
        </div>
        <div class="card-body">
            <form id="priceForm" class="price-form">
                <input type="hidden" name="action" value="update_price">
                
                <div class="form-group">
                    <label class="form-label">Chọn loại vé <span class="required">*</span></label>
                    <select name="ticket_type" id="ticket_type_select" class="form-input" required onchange="loadPriceData(this.value)">
                        <option value="">-- Chọn loại vé --</option>
                        <?php foreach ($ticketTypes as $type): ?>
                        <option value="<?php echo $type; ?>"><?php 
                            $labels = ['day' => 'Vé ngày', 'month' => 'Vé tháng', 'year' => 'Vé năm'];
                            echo $labels[$type] ?? ucfirst($type); 
                        ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Giá hiện tại</label>
                    <div class="price-display" id="current_price">Chọn loại vé để xem giá</div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Giá mới (₫) <span class="required">*</span></label>
                    <input type="number" name="base_price" id="price_value" class="form-input" placeholder="0" required min="1" step="1000">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Mô tả</label>
                    <textarea name="description" id="price_description" class="form-textarea" rows="3" placeholder="Nhập mô tả về loại vé này"></textarea>
                </div>
                
                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i>
                    <span>Cập nhật giá vé</span>
                </button>
            </form>
        </div>
    </div>
</div>

<style>
:root {
    --white: #ffffff;
    --gray-50: #f8f9fa;
    --gray-100: #f1f3f5;
    --gray-200: #e9ecef;
    --gray-300: #dee2e6;
    --gray-500: #adb5bd;
    --gray-600: #868e96;
    --gray-700: #495057;
    --gray-900: #212529;
    --blue: #4c6ef5;
    --red: #f03e3e;
    --shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.06);
    --radius: 12px;
    --radius-sm: 8px;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.price-manage-dashboard {
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
    color: var(--gray-900);
    margin-bottom: 4px;
}

.title-group p {
    font-size: 15px;
    color: var(--gray-600);
}

/* Content Card */
.content-card {
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
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

.badge-info {
    padding: 4px 12px;
    background: rgba(76, 110, 245, 0.1);
    color: var(--blue);
    border-radius: 12px;
    font-size: 13px;
    font-weight: 500;
}

.card-body {
    padding: 24px;
}

/* Price Form */
.price-form {
    max-width: 500px;
}

.price-display {
    padding: 16px;
    background: var(--gray-100);
    border-radius: var(--radius-sm);
    font-size: 16px;
    font-weight: 600;
    color: var(--blue);
}

.btn-submit {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 28px;
    background: var(--gray-900);
    color: var(--white);
    border: none;
    border-radius: var(--radius-sm);
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-submit:hover {
    background: var(--gray-800);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

/* Prices Grid */
.prices-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.price-card {
    padding: 20px;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-sm);
    transition: all 0.2s;
}

.price-card:hover {
    box-shadow: var(--shadow-md);
    border-color: var(--blue);
}

.price-header {
    display: flex;
    justify-content: space-between;
    align-items: start;
    margin-bottom: 16px;
}

.price-header h3 {
    font-size: 16px;
    font-weight: 600;
    color: var(--gray-900);
}

.btn-edit-small {
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

.btn-edit-small:hover {
    background: var(--blue);
    color: var(--white);
}

.price-value {
    font-size: 28px;
    font-weight: 700;
    color: var(--blue);
    margin-bottom: 8px;
}

.price-description {
    font-size: 13px;
    color: var(--gray-600);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-icon {
    font-size: 48px;
    color: var(--gray-300);
    margin-bottom: 16px;
}

.empty-state p {
    font-size: 15px;
    color: var(--gray-600);
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

@keyframes modalSlide {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}

.modal-content {
    background: var(--white);
    border-radius: var(--radius);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    overflow: hidden;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 24px;
    border-bottom: 1px solid var(--gray-200);
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
    color: var(--gray-900);
}

.modal-body {
    padding: 24px;
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

.form-input,
.form-textarea {
    width: 100%;
    padding: 10px 12px;
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
    border-color: var(--blue);
    box-shadow: 0 0 0 2px rgba(76, 110, 245, 0.1);
}

.form-textarea {
    resize: vertical;
}

.form-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    margin-top: 24px;
}

/* Buttons */
.btn-primary,
.btn-secondary {
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
    box-shadow: var(--shadow-md);
}

.btn-secondary {
    background: var(--white);
    color: var(--gray-700);
    border: 1px solid var(--gray-300);
}

.btn-secondary:hover {
    background: var(--gray-50);
}

@media (max-width: 768px) {
    .header-content {
        flex-direction: column;
        align-items: stretch;
    }
    
    .modal-container {
        width: 95%;
        margin: 16px;
    }
}
</style>

<script>
// Dữ liệu giá vé mặc định
const defaultPrices = {
    'day': 10000,
    'month': 200000,
    'year': 2000000
};

const ticketLabels = {
    'day': 'Vé ngày',
    'month': 'Vé tháng',
    'year': 'Vé năm'
};

function loadPriceData(ticketType) {
    if (!ticketType) {
        document.getElementById('current_price').textContent = 'Chọn loại vé để xem giá';
        document.getElementById('price_value').value = '';
        document.getElementById('price_description').value = '';
        return;
    }
    
    // Lấy giá mặc định
    const price = defaultPrices[ticketType] || 0;
    const label = ticketLabels[ticketType] || ticketType;
    
    document.getElementById('current_price').textContent = number_format(price) + '₫';
    document.getElementById('price_value').value = price;
    document.getElementById('price_description').value = label;
}

function number_format(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.').replace(/,/g, '.');
}

// Form Submit
document.getElementById('priceForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const ticketType = document.getElementById('ticket_type_select').value;
    if (!ticketType) {
        alert('Vui lòng chọn loại vé');
        return;
    }
    
    const formData = new FormData(this);
    
    fetch('../../handle/ticket_price_manage_process.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(json => {
        if (json && json.success) {
            alert(json.message || 'Cập nhật thành công');
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
</script>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
