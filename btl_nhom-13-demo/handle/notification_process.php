<?php
require_once __DIR__ . '/../functions/notification_functions.php';

function sendResponse($success, $message, $data = []) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}

$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif (isset($_POST['action'])) {
    $action = $_POST['action'];
}

switch ($action) {
    case 'create':
        handleCreateNotification();
        break;
    case 'edit':
        handleEditNotification();
        break;
    case 'delete':
        handleDeleteNotification();
        break;
    case 'toggle':
        handleToggleNotification();
        break;
}

function handleCreateNotification() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendResponse(false, 'Phương thức không hợp lệ');
    }
    if (!isset($_POST['title']) || !isset($_POST['message'])) {
        sendResponse(false, 'Thiếu thông tin cần thiết');
    }
    
    $title = trim($_POST['title']);
    $message = trim($_POST['message']);
    $type = isset($_POST['type']) ? $_POST['type'] : 'info';
    $is_active = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;
    
    if (empty($title) || empty($message)) {
        sendResponse(false, 'Vui lòng điền đầy đủ thông tin');
    }
    
    $result = addNotification($title, $message, $type, $is_active);
    if ($result) {
        sendResponse(true, 'Thêm thông báo thành công');
    } else {
        sendResponse(false, 'Có lỗi xảy ra khi thêm thông báo');
    }
}

function handleEditNotification() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendResponse(false, 'Phương thức không hợp lệ');
    }
    if (!isset($_POST['id']) || !isset($_POST['title']) || !isset($_POST['message'])) {
        sendResponse(false, 'Thiếu thông tin cần thiết');
    }
    
    $id = (int)$_POST['id'];
    $title = trim($_POST['title']);
    $message = trim($_POST['message']);
    $type = isset($_POST['type']) ? $_POST['type'] : 'info';
    $is_active = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;
    
    if (empty($title) || empty($message)) {
        sendResponse(false, 'Vui lòng điền đầy đủ thông tin');
    }
    
    $result = updateNotification($id, $title, $message, $type, $is_active);
    if ($result) {
        sendResponse(true, 'Cập nhật thông báo thành công');
    } else {
        sendResponse(false, 'Cập nhật thông báo thất bại');
    }
}

function handleDeleteNotification() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendResponse(false, 'Phương thức không hợp lệ');
    }
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        sendResponse(false, 'Không tìm thấy ID thông báo');
    }
    
    $id = (int)$_POST['id'];
    $result = deleteNotification($id);
    if ($result) {
        sendResponse(true, 'Xóa thông báo thành công');
    } else {
        sendResponse(false, 'Xóa thông báo thất bại');
    }
}

function handleToggleNotification() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendResponse(false, 'Phương thức không hợp lệ');
    }
    if (!isset($_POST['id']) || !isset($_POST['is_active'])) {
        sendResponse(false, 'Thiếu thông tin cần thiết');
    }
    
    $id = (int)$_POST['id'];
    $is_active = (int)$_POST['is_active'];
    $result = toggleNotification($id, $is_active);
    if ($result) {
        sendResponse(true, 'Cập nhật trạng thái thành công');
    } else {
        sendResponse(false, 'Cập nhật trạng thái thất bại');
    }
}

?>
