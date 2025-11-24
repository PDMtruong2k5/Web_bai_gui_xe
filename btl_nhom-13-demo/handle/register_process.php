<?php
session_start();
require_once __DIR__ . '/../functions/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../views/auth/register.php?error=Phương thức không hợp lệ');
    exit();
}

// Lấy dữ liệu từ form
$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
$password_confirm = trim($_POST['password_confirm'] ?? '');
$full_name = trim($_POST['full_name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$license_plate = trim($_POST['license_plate'] ?? '');

// Validation
$errors = [];

// Kiểm tra các trường bắt buộc
if (empty($full_name)) {
    $errors[] = "Vui lòng nhập họ tên";
}

if (empty($username)) {
    $errors[] = "Vui lòng nhập tên đăng nhập";
}

if (empty($password)) {
    $errors[] = "Vui lòng nhập mật khẩu";
}

if (empty($password_confirm)) {
    $errors[] = "Vui lòng xác nhận mật khẩu";
}

// Kiểm tra độ dài
if (!empty($full_name) && (strlen($full_name) < 3 || strlen($full_name) > 100)) {
    $errors[] = "Họ tên phải từ 3-100 ký tự";
}

if (!empty($username) && (strlen($username) < 4 || strlen($username) > 45)) {
    $errors[] = "Tên đăng nhập phải từ 4-45 ký tự";
}

if (!empty($password) && strlen($password) < 6) {
    $errors[] = "Mật khẩu phải ít nhất 6 ký tự";
}

// Kiểm tra mật khẩu trùng khớp
if (!empty($password) && !empty($password_confirm) && $password !== $password_confirm) {
    $errors[] = "Mật khẩu không trùng khớp";
}

// Kiểm tra số điện thoại
if (!empty($phone) && !preg_match('/^0\d{9}$/', $phone)) {
    $errors[] = "Số điện thoại phải có định dạng 0xxxxxxxxx (10 số)";
}

// Nếu có lỗi, redirect về form
if (!empty($errors)) {
    header('Location: ../views/auth/register.php?error=' . urlencode(implode('; ', $errors)));
    exit();
}

// Kết nối DB
$conn = getDbConnection();

try {
    // Bắt đầu transaction
    mysqli_begin_transaction($conn);

    // Kiểm tra username đã tồn tại
    $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ? LIMIT 1");
    if (!$stmt) {
        throw new Exception("Lỗi chuẩn bị câu lệnh: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        throw new Exception("Tên đăng nhập đã tồn tại");
    }
    mysqli_stmt_close($stmt);

    // Mã hóa mật khẩu
    $hash = password_hash($password, PASSWORD_DEFAULT);

    // Thêm vào bảng users trước
    $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password, role, full_name) VALUES (?, ?, 'customer', ?)");
    if (!$stmt) {
        throw new Exception("Lỗi chuẩn bị câu lệnh: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "sss", $username, $hash, $full_name);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Lỗi khi tạo tài khoản: " . mysqli_error($conn));
    }
    
    $user_id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    // Sau đó thêm vào bảng customers với user_id
    $stmt = mysqli_prepare($conn, "INSERT INTO customers (user_id, name, phone, license_plate) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Lỗi chuẩn bị câu lệnh: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "isss", $user_id, $full_name, $phone, $license_plate);
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception("Lỗi khi tạo thông tin khách: " . mysqli_error($conn));
    }
    
    mysqli_stmt_close($stmt);

    // Commit transaction
    mysqli_commit($conn);
    
    // Đặt thông báo thành công và chuyển hướng
    $_SESSION['success'] = 'Đăng ký thành công! Vui lòng đăng nhập để tiếp tục.';
    header('Location: ../index.php?success=1');
    exit();

} catch (Exception $e) {
    // Rollback transaction nếu có lỗi
    mysqli_rollback($conn);
    
    error_log("Registration error: " . $e->getMessage());
    
    header('Location: ../views/auth/register.php?error=' . urlencode($e->getMessage()));
    exit();
    
} finally {
    if (isset($conn)) {
        mysqli_close($conn);
    }
}
?>