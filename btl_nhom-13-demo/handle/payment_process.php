<?php
// Tệp xử lý các yêu cầu liên quan đến thanh toán
require_once __DIR__ . '/../functions/transaction_functions.php';
require_once __DIR__ . '/../functions/auth.php';

// Kiểm tra người dùng đã đăng nhập và là admin
session_start();
$currentUser = getCurrentUser();
if (!$currentUser || $currentUser['role'] !== 'admin') {
    header("Location: ../index.php?error=Không có quyền truy cập");
    exit();
}

$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif (isset($_POST['action'])) {
    $action = $_POST['action'];
}

switch ($action) {
    case 'confirm_payment':
        handleConfirmPayment();
        break;
    case 'refund_payment':
        handleRefundPayment();
        break;
    case 'extend_due_date':
        handleExtendDueDate();
        break;
    case 'record_monthly_payment':
        handleRecordMonthlyPayment();
        break;
    case 'send_payment_reminder':
        handleSendPaymentReminder();
        break;
    case 'toggle_payment_method':
        handleTogglePaymentMethod();
        break;
    case 'add_payment_method':
        handleAddPaymentMethod();
        break;
    case 'edit_payment_method':
        handleEditPaymentMethod();
        break;
    case 'delete_payment_method':
        handleDeletePaymentMethod();
        break;
    default:
        header("Location: ../views/transaction/manage_transactions.php?error=Hành động không hợp lệ");
        exit();
}

function handleConfirmPayment() {
    if (!isset($_POST['transaction_id'])) {
        header("Location: ../views/transaction/manage_transactions.php?error=Thiếu ID giao dịch");
        exit();
    }

    $transaction_id = (int)$_POST['transaction_id'];
    $success = updateTransactionStatus($transaction_id, 'paid', 'Xác nhận thanh toán bởi admin');

    if ($success) {
        // Ghi log
        logPaymentActivity($transaction_id, $_SESSION['user_id'], 'confirm_payment', 'Xác nhận thanh toán');
        header("Location: ../views/transaction/manage_transactions.php?success=Xác nhận thanh toán thành công");
    } else {
        header("Location: ../views/transaction/manage_transactions.php?error=Có lỗi xảy ra");
    }
    exit();
}

function handleRefundPayment() {
    if (!isset($_POST['transaction_id'])) {
        header("Location: ../views/transaction/manage_transactions.php?error=Thiếu ID giao dịch");
        exit();
    }

    $transaction_id = (int)$_POST['transaction_id'];
    $success = updateTransactionStatus($transaction_id, 'refunded', 'Hoàn tiền bởi admin');

    if ($success) {
        logPaymentActivity($transaction_id, $_SESSION['user_id'], 'refund_payment', 'Hoàn tiền giao dịch');
        header("Location: ../views/transaction/manage_transactions.php?success=Hoàn tiền thành công");
    } else {
        header("Location: ../views/transaction/manage_transactions.php?error=Có lỗi xảy ra");
    }
    exit();
}

function handleExtendDueDate() {
    if (!isset($_POST['transaction_id']) || !isset($_POST['new_due_date']) || !isset($_POST['reason'])) {
        header("Location: ../views/transaction/monthly_debts.php?error=Thiếu thông tin");
        exit();
    }

    $transaction_id = (int)$_POST['transaction_id'];
    $new_due_date = $_POST['new_due_date'];
    $reason = $_POST['reason'];

    $conn = getDbConnection();
    $sql = "UPDATE transactions SET due_date = ?, notes = CONCAT(IFNULL(notes,''), '\nGia hạn đến: ', ?, '\nLý do: ', ?) WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssi", $new_due_date, $new_due_date, $reason, $transaction_id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        if ($success) {
            logPaymentActivity($transaction_id, $_SESSION['user_id'], 'extend_due_date', "Gia hạn đến $new_due_date");
            header("Location: ../views/transaction/monthly_debts.php?success=Gia hạn thành công");
        } else {
            header("Location: ../views/transaction/monthly_debts.php?error=Có lỗi xảy ra");
        }
    } else {
        header("Location: ../views/transaction/monthly_debts.php?error=Lỗi cập nhật");
    }
    mysqli_close($conn);
    exit();
}

function handleRecordMonthlyPayment() {
    if (!isset($_POST['transaction_id']) || !isset($_POST['amount']) || !isset($_POST['payment_method'])) {
        header("Location: ../views/transaction/monthly_debts.php?error=Thiếu thông tin");
        exit();
    }

    $transaction_id = (int)$_POST['transaction_id'];
    $amount = (float)$_POST['amount'];
    $payment_method = $_POST['payment_method'];
    $notes = $_POST['notes'] ?? '';

    $conn = getDbConnection();
    $sql = "UPDATE transactions SET 
            status = 'paid',
            payment_method = ?,
            payment_time = NOW(),
            notes = CONCAT(IFNULL(notes,''), '\nThanh toán: ', ?, ' VND\nGhi chú: ', ?)
            WHERE id = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sdsi", $payment_method, $amount, $notes, $transaction_id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        if ($success) {
            logPaymentActivity($transaction_id, $_SESSION['user_id'], 'record_payment', "Ghi nhận thanh toán $amount VND");
            header("Location: ../views/transaction/monthly_debts.php?success=Ghi nhận thanh toán thành công");
        } else {
            header("Location: ../views/transaction/monthly_debts.php?error=Có lỗi xảy ra");
        }
    } else {
        header("Location: ../views/transaction/monthly_debts.php?error=Lỗi cập nhật");
    }
    mysqli_close($conn);
    exit();
}

function handleSendPaymentReminder() {
    if (!isset($_POST['transaction_id'])) {
        echo json_encode(['success' => false, 'message' => 'Thiếu ID giao dịch']);
        exit();
    }

    $transaction_id = (int)$_POST['transaction_id'];
    
    // TODO: Implement actual reminder sending logic here
    // For now, just log the activity
    $success = logPaymentActivity($transaction_id, $_SESSION['user_id'], 'send_reminder', 'Gửi nhắc nhở thanh toán');
    
    echo json_encode(['success' => $success]);
    exit();
}

function handleTogglePaymentMethod() {
    if (!isset($_POST['id']) || !isset($_POST['status'])) {
        echo json_encode(['success' => false, 'message' => 'Thiếu thông tin']);
        exit();
    }

    $method_id = (int)$_POST['id'];
    $status = (int)$_POST['status'];

    $conn = getDbConnection();
    $sql = "UPDATE payment_methods SET is_active = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $status, $method_id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        
        echo json_encode(['success' => $success]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi cập nhật']);
    }
    exit();
}

function handleAddPaymentMethod() {
    if (!isset($_POST['method_name']) || !isset($_POST['method_code'])) {
        header("Location: ../views/transaction/payment_methods.php?error=Thiếu thông tin");
        exit();
    }

    $method_name = $_POST['method_name'];
    $method_code = $_POST['method_code'];
    $config = $_POST['config'] ?? null;

    $conn = getDbConnection();
    $sql = "INSERT INTO payment_methods (method_name, method_code, config) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sss", $method_name, $method_code, $config);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        
        if ($success) {
            header("Location: ../views/transaction/payment_methods.php?success=Thêm phương thức thanh toán thành công");
        } else {
            header("Location: ../views/transaction/payment_methods.php?error=Có lỗi xảy ra");
        }
    } else {
        header("Location: ../views/transaction/payment_methods.php?error=Lỗi thêm mới");
    }
    exit();
}

function handleEditPaymentMethod() {
    // TODO: Implement edit payment method functionality
    header("Location: ../views/transaction/payment_methods.php?error=Chức năng đang được phát triển");
    exit();
}

function handleDeletePaymentMethod() {
    // TODO: Implement delete payment method functionality
    header("Location: ../views/transaction/payment_methods.php?error=Chức năng đang được phát triển");
    exit();
}
?>