<?php
session_start();
require_once __DIR__ . '/../functions/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $conn = getDbConnection();
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm = trim($_POST['confirm'] ?? '');

    if ($username === '' || $password === '' || $confirm === '') {
        $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin.';
        header('Location: /btlnhom13/views/customer/register.php');
        exit();
    }
    if ($password !== $confirm) {
        $_SESSION['error'] = 'Mật khẩu xác nhận không khớp.';
        header('Location: /btlnhom13/views/customer/register.php');
        exit();
    }

    // Check duplicate username (any role)
    $check = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ? LIMIT 1");
    if ($check) {
        mysqli_stmt_bind_param($check, 's', $username);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);
        if (mysqli_stmt_num_rows($check) > 0) {
            mysqli_stmt_close($check);
            mysqli_close($conn);
            $_SESSION['error'] = 'Tên đăng nhập đã tồn tại.';
            header('Location: /btlnhom13/views/customer/register.php');
            exit();
        }
        mysqli_stmt_close($check);
    }

    // Insert as role customer (plain password for now)
    $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password, role) VALUES (?, ?, 'customer')");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ss', $username, $password);
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = 'Đăng ký thành công. Vui lòng đăng nhập!';
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            header('Location: /btlnhom13/views/customer/login.php');
            exit();
        }
        mysqli_stmt_close($stmt);
    }

    mysqli_close($conn);
    $_SESSION['error'] = 'Không thể đăng ký. Vui lòng thử lại sau.';
    header('Location: /btlnhom13/views/customer/register.php');
    exit();
}

header('Location: /btlnhom13/views/customer/register.php');
exit();



