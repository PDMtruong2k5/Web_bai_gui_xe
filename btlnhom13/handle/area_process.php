<?php
// Area process using area_functions, backed by legacy classs table
require_once __DIR__ . '/../functions/area_functions.php';

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
        header("Location: ../views/area.php?error=Phương thức không hợp lệ");
        exit();
    }
    if (!isset($_POST['class_name']) || !isset($_POST['school_year'])) {
        header("Location: ../views/area/create_area.php?error=Thiếu thông tin cần thiết");
        exit();
    }
    $area_name = trim($_POST['class_name']);
    $area_desc = trim($_POST['school_year']);
    if (empty($area_name) || empty($area_desc)) {
        header("Location: ../views/area/create_area.php?error=Vui lòng điền đầy đủ thông tin");
        exit();
    }
    $result = addArea($area_name, $area_desc);
    if ($result) {
        header("Location: ../views/area.php?success=Thêm khu vực thành công");
    } else {
        header("Location: ../views/area/create_area.php?error=Có lỗi xảy ra khi thêm khu vực");
    }
    exit();
}

function handleEditArea() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/area.php?error=Phương thức không hợp lệ");
        exit();
    }
    if (!isset($_POST['id']) || !isset($_POST['class_name']) || !isset($_POST['school_year'])) {
        header("Location: ../views/area.php?error=Thiếu thông tin cần thiết");
        exit();
    }
    $id = $_POST['id'];
    $area_name = trim($_POST['class_name']);
    $area_desc = trim($_POST['school_year']);
    if (empty($area_name) || empty($area_desc)) {
        header("Location: ../views/area/edit_area.php?id=" . $id . "&error=Vui lòng điền đầy đủ thông tin");
        exit();
    }
    $result = updateArea($id, $area_name, $area_desc);
    if ($result) {
        header("Location: ../views/area.php?success=Cập nhật khu vực thành công");
    } else {
        header("Location: ../views/area/edit_area.php?id=" . $id . "&error=Cập nhật khu vực thất bại");
    }
    exit();
}

function handleDeleteArea() {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        header("Location: ../views/area.php?error=Phương thức không hợp lệ");
        exit();
    }
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        header("Location: ../views/area.php?error=Không tìm thấy ID khu vực");
        exit();
    }
    $id = $_GET['id'];
    if (!is_numeric($id)) {
        header("Location: ../views/area.php?error=ID khu vực không hợp lệ");
        exit();
    }
    $result = deleteArea($id);
    if ($result) {
        header("Location: ../views/area.php?success=Xóa khu vực thành công");
    } else {
        header("Location: ../views/area.php?error=Xóa khu vực thất bại");
    }
    exit();
}

?>


