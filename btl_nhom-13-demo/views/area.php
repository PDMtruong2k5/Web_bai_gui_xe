<?php
require_once __DIR__ . '/../functions/auth.php';
checkLogin(__DIR__ . '/../index.php');
// Yêu cầu phải là admin
requireRole('admin', __DIR__ . '/../index.php');

$pageTitle = "Quản lý khu vực";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../functions/area_functions.php';

// Lấy thống kê
$areas = getAllAreas();
$totalAreas = count($areas);
$totalCapacity = array_reduce($areas, function($carry, $area) {
    return $carry + (int)preg_replace('/[^0-9]/', '', $area['area_desc']);
}, 0);
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="area-dashboard">
    <!-- Header -->
    <div class="dashboard-header">
        <div class="header-content">
            <div class="title-group">
                <h1>Quản lý khu vực</h1>
                <p>Sơ đồ và thống kê các khu vực đậu xe</p>
            </div>
            <button class="btn-add" id="createAreaBtn" onclick="openAreaModal('create')">
                <span class="btn-icon">+</span>
                <span>Thêm khu vực</span>
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
                <i class="fas fa-map-marked-alt"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value"><?php echo $totalAreas; ?></div>
                <div class="stat-label">Tổng khu vực</div>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon-wrapper purple">
                <i class="fas fa-car"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value"><?php echo number_format($totalCapacity); ?></div>
                <div class="stat-label">Sức chứa tổng</div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="content-card">
        <div class="card-header">
            <h2>Thống kê sức chứa</h2>
        </div>
        <div class="card-body">
            <div class="charts-grid">
                <div class="chart-container">
                    <h4>Phân bổ theo khu vực</h4>
                    <canvas id="capacityDonut"></canvas>
                </div>
                <div class="chart-container">
                    <h4>Sức chứa chi tiết</h4>
                    <canvas id="capacityBar"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Area Grid -->
    <div class="content-card">
        <div class="card-header">
            <h2>Sơ đồ khu vực</h2>
        </div>
        <div class="card-body">
            <div class="areas-grid">
                <?php foreach ($areas as $a): 
                    $cap = preg_replace('/[^0-9]/', '', $a['area_desc']);
                    $current = (int)$a['current_vehicles'];
                    $percentage = $cap ? round(($current / $cap) * 100) : 0;
                ?>
                <div class="area-card" onclick="editArea(<?php echo $a['id']; ?>, '<?php echo htmlspecialchars($a['area_name'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($a['area_desc'], ENT_QUOTES); ?>', <?php echo $current; ?>)">
                    <div class="area-header">
                        <div class="area-icon">
                            <i class="fas fa-parking"></i>
                        </div>
                        <button class="area-menu" onclick="event.stopPropagation(); showAreaMenu(<?php echo $a['id']; ?>, '<?php echo htmlspecialchars($a['area_name'], ENT_QUOTES); ?>')">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                    </div>
                    <div class="area-content">
                        <h3><?php echo htmlspecialchars($a['area_name']); ?></h3>
                        <div class="area-stats">
                            <span class="current-count"><?php echo $current; ?></span>
                            <span class="separator">/</span>
                            <span class="max-count"><?php echo $cap ?: htmlspecialchars($a['area_desc']); ?></span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?php echo min($percentage, 100); ?>%"></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Area List Table -->
    <div class="content-card">
        <div class="card-header">
            <h2>Danh sách chi tiết</h2>
            <span class="count-badge"><?php echo $totalAreas; ?></span>
        </div>
        <div class="card-body">
            <?php if (empty($areas)): ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <h3>Chưa có khu vực nào</h3>
                    <p>Bắt đầu bằng cách thêm khu vực đầu tiên của bạn</p>
                    <button class="btn-primary" onclick="openAreaModal('create')">
                        <span class="btn-icon">+</span>
                        <span>Thêm khu vực</span>
                    </button>
                </div>
            <?php else: ?>
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Khu vực</th>
                                <th>Sức chứa</th>
                                <th>Trạng thái</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($areas as $area): 
                                $cap = preg_replace('/[^0-9]/', '', $area['area_desc']);
                                $current = (int)$area['current_vehicles'];
                                $percentage = $cap ? round(($current / $cap) * 100) : 0;
                            ?>
                            <tr>
                                <td>
                                    <div class="table-area-info">
                                        <div class="table-area-icon">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
                                        <div>
                                            <div class="area-name"><?php echo htmlspecialchars($area['area_name']); ?></div>
                                            <div class="area-id">ID: <?php echo $area['id']; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="capacity-info">
                                        <span class="capacity-text"><?php echo $current; ?>/<?php echo $cap ?: htmlspecialchars($area['area_desc']); ?></span>
                                        <div class="mini-progress">
                                            <div class="mini-progress-fill" style="width: <?php echo min($percentage, 100); ?>%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($percentage >= 90): ?>
                                        <span class="status-badge full">Đầy</span>
                                    <?php elseif ($percentage >= 70): ?>
                                        <span class="status-badge high">Cao</span>
                                    <?php else: ?>
                                        <span class="status-badge available">Còn chỗ</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="action-btn edit" onclick="editArea(<?php echo $area['id']; ?>, '<?php echo htmlspecialchars($area['area_name'], ENT_QUOTES); ?>', '<?php echo htmlspecialchars($area['area_desc'], ENT_QUOTES); ?>', <?php echo (int)$area['current_vehicles']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="action-btn delete" onclick="deleteArea(<?php echo $area['id']; ?>, '<?php echo htmlspecialchars($area['area_name'], ENT_QUOTES); ?>')">
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

<!-- Area Modal -->
<div id="areaModal" class="modal">
    <div class="modal-backdrop" onclick="closeAreaModal()"></div>
    <div class="modal-container">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="areaModalTitle">Thêm khu vực mới</h3>
                <button class="modal-close" onclick="closeAreaModal()">×</button>
            </div>
            <form id="areaForm" class="modal-body">
                <input type="hidden" name="action" id="form_action" value="create">
                <input type="hidden" name="id" id="form_id" value="">
                
                <div class="form-group">
                    <label class="form-label">Tên khu vực <span class="required">*</span></label>
                    <input type="text" name="class_name" id="class_name" class="form-input" placeholder="Nhập tên khu vực" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Sức chứa <span class="required">*</span></label>
                    <div class="input-group">
                        <input type="number" name="school_year" id="school_year" class="form-input" placeholder="0" required min="1">
                        <span class="input-suffix">xe</span>
                    </div>
                    <small class="form-hint">Nhập số lượng xe tối đa có thể chứa</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Xe đã có <span class="required">*</span></label>
                    <div class="input-group">
                        <input type="number" name="current_vehicles" id="current_vehicles" class="form-input" placeholder="0" required min="0">
                        <span class="input-suffix">xe</span>
                    </div>
                    <small class="form-hint">Nhập số lượng xe hiện đang có trong khu vực</small>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn-secondary" onclick="closeAreaModal()">Hủy</button>
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
                    <p>Bạn có chắc chắn muốn xóa khu vực <strong id="delete_area_name"></strong>?</p>
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

.area-dashboard {
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

.stat-icon-wrapper.purple {
    background: rgba(121, 80, 242, 0.1);
    color: var(--purple);
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

/* Charts */
.charts-grid {
    display: grid;
    grid-template-columns: 1fr 1.5fr;
    gap: 24px;
}

.chart-container {
    padding: 20px;
    background: var(--gray-50);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius);
}

.chart-container h4 {
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 16px;
}

/* Areas Grid */
.areas-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 20px;
}

.area-card {
    padding: 24px;
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius);
    cursor: pointer;
    transition: all 0.2s;
}

.area-card:hover {
    border-color: var(--gray-300);
    box-shadow: var(--shadow);
    transform: translateY(-2px);
}

.area-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
}

.area-icon {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--gray-900);
    color: var(--white);
    border-radius: var(--radius-sm);
    font-size: 20px;
}

.area-menu {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    border: none;
    color: var(--gray-500);
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s;
}

.area-menu:hover {
    background: var(--gray-100);
    color: var(--gray-700);
}

.area-content h3 {
    font-size: 18px;
    font-weight: 600;
    color: var(--gray-900);
    margin-bottom: 12px;
}

.area-stats {
    display: flex;
    align-items: baseline;
    gap: 4px;
    margin-bottom: 12px;
}

.current-count {
    font-size: 24px;
    font-weight: 700;
    color: var(--gray-900);
}

.separator {
    font-size: 18px;
    color: var(--gray-400);
}

.max-count {
    font-size: 18px;
    color: var(--gray-600);
}

.progress-bar {
    height: 6px;
    background: var(--gray-200);
    border-radius: 3px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: var(--gray-900);
    border-radius: 3px;
    transition: width 0.3s;
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

.table-area-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.table-area-icon {
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

.area-name {
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-900);
}

.area-id {
    font-size: 12px;
    color: var(--gray-500);
    font-family: 'Courier New', monospace;
}

.capacity-info {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.capacity-text {
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-700);
}

.mini-progress {
    width: 80px;
    height: 4px;
    background: var(--gray-200);
    border-radius: 2px;
    overflow: hidden;
}

.mini-progress-fill {
    height: 100%;
    background: var(--gray-700);
    border-radius: 2px;
}

.status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.status-badge.available {
    background: rgba(55, 178, 77, 0.1);
    color: var(--green);
}

.status-badge.high {
    background: rgba(253, 126, 20, 0.1);
    color: var(--orange);
}

.status-badge.full {
    background: rgba(240, 62, 62, 0.1);
    color: var(--red);
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

.input-group {
    position: relative;
}

.input-group .form-input {
    padding-right: 50px;
}

.input-suffix {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 14px;
    font-weight: 500;
    color: var(--gray-500);
}

.form-hint {
    display: block;
    margin-top: 6px;
    font-size: 12px;
    color: var(--gray-500);
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

    .charts-grid {
        grid-template-columns: 1fr;
    }

    .areas-grid {
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    }
}

@media (max-width: 640px) {
    .area-dashboard {
        padding: 24px 16px;
    }

    .stats-container {
        grid-template-columns: 1fr;
    }

    .areas-grid {
        grid-template-columns: 1fr;
    }

    .modal-container {
        width: 95%;
        margin: 16px;
    }
}
</style>

<script>
// Prepare chart data
const areaData = <?php echo json_encode(array_map(function($a){
    $cap = (int)preg_replace('/[^0-9]/', '', $a['area_desc']);
    return ['id' => $a['id'], 'name' => $a['area_name'], 'cap' => ($cap ?: 0)];
}, $areas)); ?>;

const labels = areaData.map(a => a.name);
const caps = areaData.map(a => a.cap);

// Donut Chart
const ctxDonut = document.getElementById('capacityDonut').getContext('2d');
new Chart(ctxDonut, {
    type: 'doughnut',
    data: {
        labels: labels,
        datasets: [{
            data: caps,
            backgroundColor: [
                '#212529',
                '#495057',
                '#868e96',
                '#adb5bd',
                '#ced4da',
                '#dee2e6',
                '#f1f3f5'
            ],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    font: {
                        size: 12,
                        family: '-apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif'
                    },
                    usePointStyle: true,
                    pointStyle: 'circle'
                }
            }
        }
    }
});

// Bar Chart
const ctxBar = document.getElementById('capacityBar').getContext('2d');
new Chart(ctxBar, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Sức chứa (xe)',
            data: caps,
            backgroundColor: '#212529',
            borderRadius: 6,
            borderSkipped: false
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        indexAxis: 'y',
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            x: {
                beginAtZero: true,
                grid: {
                    color: '#f1f3f5'
                },
                ticks: {
                    font: {
                        size: 11
                    }
                }
            },
            y: {
                grid: {
                    display: false
                },
                ticks: {
                    font: {
                        size: 11
                    }
                }
            }
        }
    }
});

// Modal Functions
const areaModal = document.getElementById('areaModal');
const deleteModal = document.getElementById('deleteModal');
let deleteAreaId = null;

function openAreaModal(mode = 'create', data = {}) {
    areaModal.classList.add('show');
    const title = document.getElementById('areaModalTitle');
    const formAction = document.getElementById('form_action');
    const formId = document.getElementById('form_id');
    
    if (mode === 'create') {
        title.textContent = 'Thêm khu vực mới';
        formAction.value = 'create';
        formId.value = '';
        document.getElementById('class_name').value = '';
        document.getElementById('school_year').value = '';
        document.getElementById('current_vehicles').value = '';
    } else {
        title.textContent = 'Chỉnh sửa khu vực';
        formAction.value = 'edit';
        formId.value = data.id || '';
        document.getElementById('class_name').value = data.name || '';
        const cap = (data.desc || '').replace(/[^0-9]/g, '');
        document.getElementById('school_year').value = cap;
        document.getElementById('current_vehicles').value = data.current || '0';
    }
}

function closeAreaModal() {
    areaModal.classList.remove('show');
    document.getElementById('areaForm').reset();
}

function openDeleteModal() {
    deleteModal.classList.add('show');
}

function closeDeleteModal() {
    deleteModal.classList.remove('show');
    deleteAreaId = null;
}

function editArea(id, name, desc, current) {
    openAreaModal('edit', { id: id, name: name, desc: desc, current: current });
}

function deleteArea(id, name) {
    deleteAreaId = id;
    document.getElementById('delete_area_name').textContent = name;
    openDeleteModal();
}

// Form Submit
document.getElementById('areaForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const capacity = formData.get('school_year');
    const currentVehicles = parseInt(formData.get('current_vehicles')) || 0;
    
    formData.set('school_year', capacity + ' xe');
    formData.set('current_vehicles', currentVehicles);
    
    fetch('../handle/area_process.php', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(r => r.json())
    .then(json => {
        if (json && json.success) {
            closeAreaModal();
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
    if (!deleteAreaId) return;
    
    const formData = new FormData();
    formData.append('action', 'delete');
    formData.append('id', deleteAreaId);
    
    fetch('../handle/area_process.php', {
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
            alert(json.message || 'Có lỗi xảy ra khi xóa khu vực');
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