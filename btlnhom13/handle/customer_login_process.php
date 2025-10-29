<?php
session_start();
require_once __DIR__ . '/../functions/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $conn = getDbConnection();
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin.';
        header('Location: /btlnhom13/views/customer/login.php');
        exit();
    }

    $sql = "SELECT id, username, password, role FROM users WHERE username = ? AND role = 'customer' LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            if ($password === $user['password']) { // replace with password_verify if hashed
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['success'] = 'Đăng nhập thành công!';
                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                header('Location: /btlnhom13/views/customer/index.php');
                exit();
            }
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
    $_SESSION['error'] = 'Tên đăng nhập hoặc mật khẩu không đúng.';
    header('Location: /btlnhom13/views/customer/login.php');
    exit();
}

header('Location: /btlnhom13/views/customer/login.php');
exit();



