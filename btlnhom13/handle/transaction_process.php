<?php
require_once __DIR__ . '/../functions/transaction_functions.php';

$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif (isset($_POST['action'])) {
    $action = $_POST['action'];
}

switch ($action) {
    case 'create':
        handleCreateTransaction();
        break;
    case 'edit':
        handleEditTransaction();
        break;
    case 'delete':
        handleDeleteTransaction();
        break;
}

function handleGetAllTransactions() {
    return getAllTransactions();
}

function handleGetTransactionById($id) {
    return getTransactionById($id);
}

function handleCreateTransaction() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/transaction.php?error=Phương thức không hợp lệ");
        exit();
    }

    if (!isset($_POST['vehicle_id']) || !isset($_POST['service_id']) || !isset($_POST['amount'])) {
        header("Location: ../views/transaction/create_transaction.php?error=Thiếu thông tin cần thiết");
        exit();
    }

    $vehicle_id = trim($_POST['vehicle_id']);
    $service_id = trim($_POST['service_id']);
    $amount = trim($_POST['amount']);

    if (empty($vehicle_id) || empty($service_id) || $amount === '') {
        header("Location: ../views/transaction/create_transaction.php?error=Vui lòng điền đầy đủ thông tin");
        exit();
    }

    if (!is_numeric($amount) || $amount < 0) {
        header("Location: ../views/transaction/create_transaction.php?error=Số tiền phải là số dương");
        exit();
    }

    if (checkTransactionExists($vehicle_id, $service_id)) {
        header("Location: ../views/transaction/create_transaction.php?error=Xe đã có giao dịch loại dịch vụ này");
        exit();
    }

    $success = addTransaction($vehicle_id, $service_id, $amount);

    if ($success) {
        header("Location: ../views/transaction.php?success=Thêm giao dịch thành công");
    } else {
        header("Location: ../views/transaction/create_transaction.php?error=Có lỗi xảy ra khi thêm giao dịch");
    }
    exit();
}

function handleEditTransaction() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header("Location: ../views/transaction.php?error=Phương thức không hợp lệ");
        exit();
    }

    if (!isset($_POST['id']) || !isset($_POST['vehicle_id']) || !isset($_POST['service_id']) || !isset($_POST['amount'])) {
        header("Location: ../views/transaction.php?error=Thiếu thông tin cần thiết");
        exit();
    }

    $id = (int)$_POST['id'];
    $vehicle_id = trim($_POST['vehicle_id']);
    $service_id = trim($_POST['service_id']);
    $amount = trim($_POST['amount']);

    if (empty($vehicle_id) || empty($service_id) || $amount === '') {
        header("Location: ../views/transaction/edit_transaction.php?id=$id&error=Vui lòng điền đầy đủ thông tin");
        exit();
    }

    if (!is_numeric($amount) || $amount < 0) {
        header("Location: ../views/transaction/edit_transaction.php?id=$id&error=Số tiền phải là số dương");
        exit();
    }

    $currentTransaction = getTransactionById($id);
    if (!$currentTransaction) {
        header("Location: ../views/transaction.php?error=Giao dịch không tồn tại");
        exit();
    }

    if ($currentTransaction['vehicle_id'] != $vehicle_id || $currentTransaction['service_id'] != $service_id) {
        if (checkTransactionExists($vehicle_id, $service_id)) {
            header("Location: ../views/transaction/edit_transaction.php?id=$id&error=Xe đã có giao dịch loại dịch vụ này");
            exit();
        }
    }

    $success = updateTransaction($id, $vehicle_id, $service_id, $amount);

    if ($success) {
        header("Location: ../views/transaction.php?success=Cập nhật giao dịch thành công");
    } else {
        header("Location: ../views/transaction/edit_transaction.php?id=$id&error=Có lỗi xảy ra khi cập nhật giao dịch");
    }
    exit();
}

function handleDeleteTransaction() {
    if (!isset($_GET['id'])) {
        header("Location: ../views/transaction.php?error=Thiếu ID giao dịch");
        exit();
    }

    $id = (int)$_GET['id'];

    $transaction = getTransactionById($id);
    if (!$transaction) {
        header("Location: ../views/transaction.php?error=Giao dịch không tồn tại");
        exit();
    }

    $success = deleteTransaction($id);

    if ($success) {
        header("Location: ../views/transaction.php?success=Xóa giao dịch thành công");
    } else {
        header("Location: ../views/transaction.php?error=Có lỗi xảy ra khi xóa giao dịch");
    }
    exit();
}
?>


