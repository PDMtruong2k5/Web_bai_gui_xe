<?php
require_once __DIR__ . '/../functions/vehicle_functions.php';

$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif (isset($_POST['action'])) {
    $action = $_POST['action'];
}

switch ($action) {
    case 'create':
        handleCreateVehicle();
        break;
    case 'edit':
        handleEditVehicle();
        break;
    case 'delete':
        handleDeleteVehicle();
        break;
}

function handleGetAllVehicles() { return getAllVehicles(); }
function handleGetVehicleById($id) { return getVehicleById($id); }

function handleCreateVehicle() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/vehicle.php?error=Phương thức không hợp lệ");
        exit();
    }
    if (!isset($_POST['vehicle_plate']) || !isset($_POST['vehicle_owner'])) {
        header("Location: ../views/vehicle/create_vehicle.php?error=Thiếu thông tin cần thiết");
        exit();
    }
    $vehicle_plate = trim($_POST['vehicle_plate']);
    $vehicle_owner = trim($_POST['vehicle_owner']);
    if (empty($vehicle_plate) || empty($vehicle_owner)) {
        header("Location: ../views/vehicle/create_vehicle.php?error=Vui lòng điền đầy đủ thông tin");
        exit();
    }
    $result = addVehicle($vehicle_plate, $vehicle_owner);
    if ($result) {
        header("Location: ../views/vehicle.php?success=Thêm xe thành công");
    } else {
        header("Location: ../views/vehicle/create_vehicle.php?error=Có lỗi xảy ra khi thêm xe");
    }
    exit();
}

function handleEditVehicle() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/vehicle.php?error=Phương thức không hợp lệ");
        exit();
    }
    if (!isset($_POST['id']) || !isset($_POST['vehicle_plate']) || !isset($_POST['vehicle_owner'])) {
        header("Location: ../views/vehicle.php?error=Thiếu thông tin cần thiết");
        exit();
    }
    $id = $_POST['id'];
    $vehicle_plate = trim($_POST['vehicle_plate']);
    $vehicle_owner = trim($_POST['vehicle_owner']);
    if (empty($vehicle_plate) || empty($vehicle_owner)) {
        header("Location: ../views/vehicle/edit_vehicle.php?id=" . $id . "&error=Vui lòng điền đầy đủ thông tin");
        exit();
    }
    $result = updateVehicle($id, $vehicle_plate, $vehicle_owner);
    if ($result) {
        header("Location: ../views/vehicle.php?success=Cập nhật xe thành công");
    } else {
        header("Location: ../views/vehicle/edit_vehicle.php?id=" . $id . "&error=Cập nhật xe thất bại");
    }
    exit();
}

function handleDeleteVehicle() {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        header("Location: ../views/vehicle.php?error=Phương thức không hợp lệ");
        exit();
    }
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        header("Location: ../views/vehicle.php?error=Không tìm thấy ID xe");
        exit();
    }
    $id = $_GET['id'];
    if (!is_numeric($id)) {
        header("Location: ../views/vehicle.php?error=ID xe không hợp lệ");
        exit();
    }
    if (vehicleHasTransactions((int)$id)) {
        header("Location: ../views/vehicle.php?error=Không thể xóa: Xe vẫn còn giao dịch liên quan");
        exit();
    }

    $result = deleteVehicle($id);
    if ($result) {
        header("Location: ../views/vehicle.php?success=Xóa xe thành công");
    } else {
        header("Location: ../views/vehicle.php?error=Xóa xe thất bại");
    }
    exit();
}
?>


