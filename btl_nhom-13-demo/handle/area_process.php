<?php
// Area process using area_functions, backed by legacy classs table
require_once __DIR__ . '/../functions/area_functions.php';

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
        handleCreateArea();
        break;
    case 'edit':
        handleEditArea();
        break;
    case 'delete':
        handleDeleteArea();
        break;
}

function handleGetAllAreas() { return getAllAreas(); }
function handleGetAreaById($id) { return getAreaById($id); }

function handleCreateArea() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendResponse(false, 'Phương thức không hợp lệ');
    }
    if (!isset($_POST['class_name']) || !isset($_POST['school_year'])) {
        sendResponse(false, 'Thiếu thông tin cần thiết');
    }
    $area_name = trim($_POST['class_name']);
    $area_desc = trim($_POST['school_year']);
    $current_vehicles = isset($_POST['current_vehicles']) ? (int)$_POST['current_vehicles'] : 0;
    
    if (empty($area_name) || empty($area_desc)) {
        sendResponse(false, 'Vui lòng điền đầy đủ thông tin');
    }
    $result = addArea($area_name, $area_desc, $current_vehicles);
    if ($result) {
        sendResponse(true, 'Thêm khu vực thành công', ['redirect' => '../views/area.php']);
    } else {
        sendResponse(false, 'Có lỗi xảy ra khi thêm khu vực');
    }
}

function handleEditArea() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendResponse(false, 'Phương thức không hợp lệ');
    }
    if (!isset($_POST['id']) || !isset($_POST['class_name']) || !isset($_POST['school_year'])) {
        sendResponse(false, 'Thiếu thông tin cần thiết');
    }
    $id = $_POST['id'];
    $area_name = trim($_POST['class_name']);
    $area_desc = trim($_POST['school_year']);
    $current_vehicles = isset($_POST['current_vehicles']) ? (int)$_POST['current_vehicles'] : null;
    
    if (empty($area_name) || empty($area_desc)) {
        sendResponse(false, 'Vui lòng điền đầy đủ thông tin');
    }
    $result = updateArea($id, $area_name, $area_desc, $current_vehicles);
    if ($result) {
        sendResponse(true, 'Cập nhật khu vực thành công', ['redirect' => '../views/area.php']);
    } else {
        sendResponse(false, 'Cập nhật khu vực thất bại');
    }
}

function handleDeleteArea() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendResponse(false, 'Phương thức không hợp lệ');
    }
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        sendResponse(false, 'Không tìm thấy ID khu vực');
    }
    $id = $_POST['id'];
    if (!is_numeric($id)) {
        sendResponse(false, 'ID khu vực không hợp lệ');
    }
    $result = deleteArea($id);
    if ($result) {
        sendResponse(true, 'Xóa khu vực thành công');
    } else {
        sendResponse(false, 'Xóa khu vực thất bại');
    }
}

?>


