<?php
// session_start();
require_once __DIR__ . '/../functions/service_functions.php';

// Kiểm tra action được truyền qua URL hoặc POST
$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif (isset($_POST['action'])) {
    $action = $_POST['action'];
}

switch ($action) {
    case 'create':
        handleCreateService();
        break;
    case 'edit':
        handleEditService();
        break;
    case 'delete':
        handleDeleteService();
        break;
    // default:
    //     header("Location: ../views/service.php?error=Hành động không hợp lệ");
    //     exit();
}

function handleGetAllServices() {
    return getAllServices();
    // Xử lý hiển thị danh sách services
}
function handleGetServiceById($id) {
    return getServiceById($id);
}

/**
 * Xử lý tạo dịch vụ mới
 */
function handleCreateService () {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/service.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    if (!isset($_POST['service_code']) || !isset($_POST['service_name'])) {
        header("Location: ../views/service/create_service.php?error=Thiếu thông tin cần thiết");
        exit();
    }
    
    $service_code = trim($_POST['service_code']);
    $service_name = trim($_POST['service_name']);
    
    // Validate dữ liệu
    if (empty($service_code) || empty($service_name)) {
        header("Location: ../views/service/create_service.php?error=Vui lòng điền đầy đủ thông tin");
        exit();
    }

    // Gọi hàm thêm dịch vụ
    $result = addService($service_code, $service_name);

    if ($result) {
        header("Location: ../views/service.php?success=Thêm loại dịch vụ thành công");
    } else {
        header("Location: ../views/service/create_service.php?error=Có lỗi xảy ra khi thêm loại dịch vụ");
    }
    exit();
}

/**
 * Xử lý chỉnh sửa dịch vụ
 */
function handleEditService() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/service.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    if (!isset($_POST['id']) || !isset($_POST['service_code']) || !isset($_POST['service_name'])) {
        header("Location: ../views/service.php?error=Thiếu thông tin cần thiết");
        exit();
    }
    
    $id = $_POST['id'];
    $service_code = trim($_POST['service_code']);
    $service_name = trim($_POST['service_name']);
    
    // Validate dữ liệu
    if (empty($service_code) || empty($service_name)) {
        header("Location: ../views/service/edit_service.php?id=" . $id . "&error=Vui lòng điền đầy đủ thông tin");
        exit();
    }

    // Gọi function để cập nhật dịch vụ
    $result = updateService($id, $service_code, $service_name);
    
    if ($result) {
        header("Location: ../views/service.php?success=Cập nhật loại dịch vụ thành công");
    } else {
        header("Location: ../views/service/edit_service.php?id=" . $id . "&error=Cập nhật loại dịch vụ thất bại");
    }
    exit();
}

/**
 * Xử lý xóa dịch vụ
 */
function handleDeleteService() {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        header("Location: ../views/service.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        header("Location: ../views/service.php?error=Không tìm thấy ID dịch vụ");
        exit();
    }
    
    $id = $_GET['id'];
    
    // Validate ID là số
    if (!is_numeric($id)) {
        header("Location: ../views/service.php?error=ID dịch vụ không hợp lệ");
        exit();
    }
    
    // Chặn xóa nếu còn giao dịch
    if (serviceHasTransactions((int)$id)) {
        header("Location: ../views/service.php?error=Không thể xóa: Dịch vụ đang được sử dụng trong giao dịch");
        exit();
    }
    $result = deleteService($id);

    if ($result) {
        header("Location: ../views/service.php?success=Xóa loại dịch vụ thành công");
    } else {
        header("Location: ../views/service.php?error=Xóa loại dịch vụ thất bại");
    }
    exit();
}
?>