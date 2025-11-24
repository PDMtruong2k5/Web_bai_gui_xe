<?php
require_once __DIR__ . '/../functions/customer_functions.php';

$action = '';
if (isset($_GET['action'])) {
    $action = $_GET['action'];
} elseif (isset($_POST['action'])) {
    $action = $_POST['action'];
}

switch ($action) {
    case 'create':
        handleCreateCustomer();
        break;
    case 'edit':
        handleEditCustomer();
        break;
    case 'delete':
        handleDeleteCustomer();
        break;
}

function handleGetAllCustomers() { 
    return getAllCustomers(); 
}

function handleGetCustomerById($id) { 
    return getCustomerById($id); 
}

function handleCreateCustomer() {
    $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        if ($isAjax) {
            echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
            exit();
        }
        header("Location: ../views/customer.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    if (!isset($_POST['name']) || !isset($_POST['phone']) || !isset($_POST['license_plate'])) {
        if ($isAjax) {
            echo json_encode(['success' => false, 'message' => 'Thiếu thông tin cần thiết']);
            exit();
        }
        header("Location: ../views/customer/create_customer.php?error=Thiếu thông tin cần thiết");
        exit();
    }
    
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $license_plate = trim($_POST['license_plate']);
    $user_id = isset($_POST['user_id']) ? (int)$_POST['user_id'] : null;
    
    if (empty($name) || empty($phone) || empty($license_plate)) {
        if ($isAjax) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin']);
            exit();
        }
        header("Location: ../views/customer/create_customer.php?error=Vui lòng điền đầy đủ thông tin");
        exit();
    }
    
    // Pass user_id if present so the new customer is linked to the logged-in user
    $result = addCustomer($name, $phone, $license_plate, $user_id);
    if ($result) {
        if ($isAjax) {
            $newCustomer = getCustomerById($result);
            echo json_encode(['success' => true, 'customer' => $newCustomer]);
            exit();
        }
        header("Location: ../views/customer.php?success=Thêm khách hàng thành công");
    } else {
        // Nếu có lỗi DB chi tiết, hiển thị tạm để debug (chỉ dev)
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $dbErr = !empty($_SESSION['db_error']) ? $_SESSION['db_error'] : '';
        // Nếu có lỗi DB, show trực tiếp để developer biết nguyên nhân
        if (!empty($dbErr)) {
            // Xóa session lỗi sau khi lấy
            unset($_SESSION['db_error']);
            header('Content-Type: text/html; charset=utf-8');
            echo '<h2>Có lỗi khi thêm khách hàng</h2>';
            echo '<pre style="background:#f8d7da;padding:12px;border-radius:6px;color:#721c24;">' . htmlspecialchars($dbErr) . '</pre>';
            echo '<p><a href="../views/customer/create_customer.php">Quay lại</a></p>';
            exit();
        }
        // Nếu không có lỗi chi tiết, redirect như trước
        if ($isAjax) {
            echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra khi thêm khách hàng']);
            exit();
        }
        header("Location: ../views/customer/create_customer.php?error=Có lỗi xảy ra khi thêm khách hàng");
    }
    exit();
}

function handleEditCustomer() {
    $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        if ($isAjax) {
            echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
            exit();
        }
        header("Location: ../views/customer.php?error=Phương thức không hợp lệ");
        exit();
    }
    
    if (!isset($_POST['id']) || !isset($_POST['name']) || !isset($_POST['phone']) || 
        !isset($_POST['license_plate'])) {
        if ($isAjax) {
            echo json_encode(['success' => false, 'message' => 'Thiếu thông tin cần thiết']);
            exit();
        }
        header("Location: ../views/customer.php?error=Thiếu thông tin cần thiết");
        exit();
    }
    
    $id = (int)$_POST['id'];
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $license_plate = trim($_POST['license_plate']);
    
    if (empty($name) || empty($phone) || empty($license_plate)) {
        if ($isAjax) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin']);
            exit();
        }
        header("Location: ../views/customer/edit_customer.php?id=" . $id . "&error=Vui lòng điền đầy đủ thông tin");
        exit();
    }
    
    $result = updateCustomer($id, $name, $phone, $license_plate);
    if ($result) {
        if ($isAjax) {
            $updated = getCustomerById($id);
            echo json_encode(['success' => true, 'customer' => $updated]);
            exit();
        }
        header("Location: ../views/customer.php?success=Cập nhật khách hàng thành công");
    } else {
        if ($isAjax) {
            echo json_encode(['success' => false, 'message' => 'Cập nhật khách hàng thất bại']);
            exit();
        }
        header("Location: ../views/customer/edit_customer.php?id=" . $id . "&error=Cập nhật khách hàng thất bại");
    }
    exit();
}

function handleDeleteCustomer() {
    // Support both AJAX (POST) and normal GET delete for backward compatibility
    $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

    $id = null;
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    } else {
        if ($isAjax) {
            echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
            exit();
        }
        header("Location: ../views/customer.php?error=Phương thức không hợp lệ");
        exit();
    }

    if (empty($id) || !is_numeric($id)) {
        if ($isAjax) {
            echo json_encode(['success' => false, 'message' => 'ID khách hàng không hợp lệ']);
            exit();
        }
        header("Location: ../views/customer.php?error=ID khách hàng không hợp lệ");
        exit();
    }

    if (customerHasTickets($id)) {
        if ($isAjax) {
            echo json_encode(['success' => false, 'message' => 'Không thể xóa: Khách hàng vẫn còn vé liên quan']);
            exit();
        }
        header("Location: ../views/customer.php?error=Không thể xóa: Khách hàng vẫn còn vé liên quan");
        exit();
    }
    
    $result = deleteCustomer($id);
    if ($result) {
        if ($isAjax) {
            echo json_encode(['success' => true]);
            exit();
        }
        header("Location: ../views/customer.php?success=Xóa khách hàng thành công");
    } else {
        if ($isAjax) {
            echo json_encode(['success' => false, 'message' => 'Xóa khách hàng thất bại']);
            exit();
        }
        header("Location: ../views/customer.php?error=Xóa khách hàng thất bại");
    }
    exit();
}
?>


