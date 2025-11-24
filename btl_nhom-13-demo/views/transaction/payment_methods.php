<?php
// Tệp view cho phần quản lý phương thức thanh toán
include_once '../../includes/header.php';
require_once '../../functions/transaction_functions.php';

// Lấy danh sách phương thức thanh toán
$conn = getDbConnection();
$sql = "SELECT * FROM payment_methods ORDER BY id";
$result = mysqli_query($conn, $sql);
$payment_methods = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $payment_methods[] = $row;
    }
}
mysqli_close($conn);
?>

<div class="container">
    <h2>Quản lý phương thức thanh toán</h2>

    <!-- Form thêm phương thức thanh toán mới -->
    <div class="add-method-section">
        <h3>Thêm phương thức thanh toán mới</h3>
        <form method="post" action="../../handle/transaction_process.php" class="add-method-form">
            <input type="hidden" name="action" value="add_payment_method">
            <div class="form-group">
                <label>Tên phương thức:</label>
                <input type="text" name="method_name" required>
            </div>
            <div class="form-group">
                <label>Mã phương thức:</label>
                <input type="text" name="method_code" required>
            </div>
            <div class="form-group">
                <label>Cấu hình (JSON):</label>
                <textarea name="config" rows="4" placeholder="{ &quot;key&quot;: &quot;value&quot; }"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Thêm mới</button>
        </form>
    </div>

    <!-- Danh sách phương thức thanh toán -->
    <div class="methods-list">
        <h3>Danh sách phương thức thanh toán</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên phương thức</th>
                    <th>Mã</th>
                    <th>Trạng thái</th>
                    <th>Cấu hình</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payment_methods as $method): ?>
                <tr>
                    <td><?php echo $method['id']; ?></td>
                    <td><?php echo $method['method_name']; ?></td>
                    <td><?php echo $method['method_code']; ?></td>
                    <td>
                        <div class="status-toggle">
                            <input type="checkbox" 
                                   id="status_<?php echo $method['id']; ?>" 
                                   class="toggle-input"
                                   <?php echo $method['is_active'] ? 'checked' : ''; ?>
                                   onchange="togglePaymentMethod(<?php echo $method['id']; ?>, this.checked)">
                            <label for="status_<?php echo $method['id']; ?>" class="toggle-label"></label>
                        </div>
                    </td>
                    <td>
                        <button onclick="showConfig(<?php echo htmlspecialchars(json_encode($method['config'])); ?>)" 
                                class="btn btn-info btn-sm">
                            Xem cấu hình
                        </button>
                    </td>
                    <td>
                        <button onclick="editPaymentMethod(<?php echo $method['id']; ?>)" 
                                class="btn btn-warning btn-sm">
                            Sửa
                        </button>
                        <button onclick="deletePaymentMethod(<?php echo $method['id']; ?>)" 
                                class="btn btn-danger btn-sm">
                            Xóa
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal xem cấu hình -->
<div class="modal" id="configModal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Cấu hình phương thức thanh toán</h3>
        <pre id="configContent"></pre>
    </div>
</div>

<style>
.add-method-section {
    background: #f8f9fa;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 5px;
}

.add-method-form {
    max-width: 500px;
}

.status-toggle {
    position: relative;
    display: inline-block;
}

.toggle-input {
    display: none;
}

.toggle-label {
    display: block;
    width: 48px;
    height: 24px;
    background: #e9ecef;
    border-radius: 12px;
    cursor: pointer;
    position: relative;
}

.toggle-label:after {
    content: '';
    position: absolute;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: white;
    top: 2px;
    left: 2px;
    transition: 0.3s;
}

.toggle-input:checked + .toggle-label {
    background: #28a745;
}

.toggle-input:checked + .toggle-label:after {
    left: calc(100% - 22px);
}

.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.4);
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 600px;
    border-radius: 5px;
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover {
    color: black;
}

#configContent {
    background: #f8f9fa;
    padding: 10px;
    border-radius: 4px;
    white-space: pre-wrap;
}
</style>

<script>
// Hiển thị modal cấu hình
function showConfig(config) {
    const modal = document.getElementById('configModal');
    const content = document.getElementById('configContent');
    content.textContent = JSON.stringify(config, null, 2);
    modal.style.display = 'block';
}

// Đóng modal
document.querySelector('.close').onclick = function() {
    document.getElementById('configModal').style.display = 'none';
}

// Bật/tắt phương thức thanh toán
async function togglePaymentMethod(id, status) {
    try {
        const response = await fetch('../../handle/transaction_process.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=toggle_payment_method&id=${id}&status=${status ? 1 : 0}`
        });
        const result = await response.json();
        if (!result.success) {
            alert('Có lỗi xảy ra khi cập nhật trạng thái');
            // Revert the toggle
            document.getElementById(`status_${id}`).checked = !status;
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi cập nhật trạng thái');
        // Revert the toggle
        document.getElementById(`status_${id}`).checked = !status;
    }
}

// Sửa phương thức thanh toán
function editPaymentMethod(id) {
    // Implement edit functionality
    alert('Chức năng đang được phát triển');
}

// Xóa phương thức thanh toán
function deletePaymentMethod(id) {
    if (confirm('Bạn có chắc muốn xóa phương thức thanh toán này?')) {
        // Implement delete functionality
        alert('Chức năng đang được phát triển');
    }
}
</script>

<?php include_once '../../includes/footer.php'; ?>