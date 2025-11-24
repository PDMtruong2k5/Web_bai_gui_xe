<?php
session_start();
require_once __DIR__ . '/../functions/db_connection.php';

// 1. Validate input
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
$full_name = trim($_POST['full_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$license_plate = trim($_POST['license_plate'] ?? '');

if (empty($username) || empty($password) || empty($full_name) || empty($phone) || empty($license_plate)) {
    header('Location: ../index.php?register_error=' . urlencode('Vui lòng điền đầy đủ thông tin'));
    exit();
}

// 2. Kết nối DB và bắt đầu transaction
$conn = getDbConnection();
mysqli_begin_transaction($conn);

try {
    // 3. Kiểm tra username đã tồn tại
    $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        throw new Exception("Tên đăng nhập đã tồn tại");
    }
    mysqli_stmt_close($stmt);

    // 4. Thêm vào bảng customers
    $stmt = mysqli_prepare($conn, "INSERT INTO customers (name, phone, license_plate) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sss", $full_name, $phone, $license_plate);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Lỗi khi thêm thông tin khách hàng: " . mysqli_error($conn));
    }
    
    $customer_id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    // 5. Thêm vào bảng users
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password, role, full_name) VALUES (?, ?, 'customer', ?)");
    mysqli_stmt_bind_param($stmt, "sss", $username, $hash, $full_name);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Lỗi khi tạo tài khoản: " . mysqli_error($conn));
    }
    
    $user_id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    // 6. Commit transaction nếu mọi thứ OK
    mysqli_commit($conn);

    // 7. Set session và chuyển hướng
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;
    $_SESSION['role'] = 'customer';
    $_SESSION['success'] = 'Đăng ký thành công!';

    header('Location: ../views/customer_home.php');
    exit();

} catch (Exception $e) {
    // Rollback nếu có lỗi
    mysqli_rollback($conn);
    header('Location: ../index.php?register_error=' . urlencode($e->getMessage()));
    exit();
} finally {
    mysqli_close($conn);
}