<?php
/**
 * Hàm kiểm tra xem user đã đăng nhập chưa
 * Nếu chưa đăng nhập, chuyển hướng về trang login
 * 
 * @param string $redirectPath Đường dẫn để chuyển hướng về trang login (mặc định: '../index.php')
 */
function checkLogin($redirectPath = '../index.php') {
    // Khởi tạo session nếu chưa có
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Kiểm tra xem user đã đăng nhập chưa
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
        // Nếu chưa đăng nhập, set thông báo lỗi và chuyển hướng
        $_SESSION['error'] = 'Bạn cần đăng nhập để truy cập trang này!';
        // Nếu $redirectPath là một đường dẫn tới file trên server (ví dụ __DIR__ . '/../index.php'),
        // chuyển nó thành đường dẫn URL bằng cách loại bỏ $_SERVER['DOCUMENT_ROOT']
        $finalRedirect = $redirectPath;
        if (strlen($redirectPath) > 0 && (DIRECTORY_SEPARATOR === '\\' ? preg_match('#^[A-Za-z]:\\\\#', $redirectPath) : strpos($redirectPath, DIRECTORY_SEPARATOR) === 0)) {
            // đường dẫn file hệ thống, cố gắng chuyển thành URL
            $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'], "\\/");
                $normalized = str_replace('\\\\', '/', $redirectPath);
                $docRootNorm = str_replace('\\\\', '/', $docRoot);
            if (stripos($normalized, $docRootNorm) === 0) {
                $finalRedirect = substr($normalized, strlen($docRootNorm));
                if ($finalRedirect === '') $finalRedirect = '/';
            }
        }

        header('Location: ' . $finalRedirect);
        exit();
    }
}

/**
 * Hàm đăng xuất user
 * Xóa tất cả session và chuyển hướng về trang login
 * 
 * @param string $redirectPath Đường dẫn để chuyển hướng sau khi logout (mặc định: '../index.php')
 */
function logout($redirectPath = '../index.php') {
    // Khởi tạo session nếu chưa có
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Hủy tất cả session
    session_unset();
    session_destroy();
    
    // Khởi tạo session mới để lưu thông báo
    session_start();
    $_SESSION['success'] = 'Đăng xuất thành công!';
    
    // Chuyển hướng về trang đăng nhập
    header('Location: ' . $redirectPath);
    exit();
}

/**
 * Hàm lấy thông tin user hiện tại
 * 
 * @return array|null Trả về thông tin user nếu đã đăng nhập, null nếu chưa đăng nhập
 */
function getCurrentUser() {
    // Khởi tạo session nếu chưa có
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'role' => $_SESSION['role'] ?? null
        ];
    }
    
    return null;
}

/**
 * Hàm kiểm tra xem user đã đăng nhập chưa (không redirect)
 * 
 * @return bool True nếu đã đăng nhập, False nếu chưa
 */
function isLoggedIn() {
    // Khởi tạo session nếu chưa có
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

/**
 * Kiểm tra xem path hiện tại có phải là path admin không
 * @return bool
 */
function isAdminPath() {
    $path = $_SERVER['PHP_SELF'];
    // Danh sách các đường dẫn chỉ dành cho admin
    $adminPaths = [
        '/area.php',
        '/ticket.php',
        '/customer.php',
        '/views/area/',
        '/views/ticket/create_ticket.php',
        '/views/customer/create_customer.php',
        '/views/customer/edit_customer.php'
    ];

    foreach ($adminPaths as $adminPath) {
        if (strpos($path, $adminPath) !== false) {
            return true;
        }
    }
    return false;
}

/**
 * Yêu cầu user có role cụ thể, nếu không => redirect
 * @param string $role
 * @param string $redirectPath
 */
function requireRole($role, $redirectPath = '../index.php') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Nếu đây là trang admin và user không phải admin
    if (isAdminPath() && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin')) {
        $_SESSION['error'] = 'Bạn không có quyền truy cập trang này.';
        // đảm bảo redirect là URL (không phải đường dẫn file hệ thống)
        $redirect = '/btl_nhom-13-demo/views/customer_home.php';
        header('Location: ' . $redirect);
        exit();
    }

    // Kiểm tra role cụ thể nếu có yêu cầu
    if ($role !== null && (!isset($_SESSION['role']) || $_SESSION['role'] !== $role)) {
        $_SESSION['error'] = 'Bạn không có quyền truy cập trang này.';
        $finalRedirect = $redirectPath;
        // nếu redirectPath là file path, chuyển về URL tương ứng
        if (strlen($redirectPath) > 0 && (DIRECTORY_SEPARATOR === '\\' ? preg_match('#^[A-Za-z]:\\\\#', $redirectPath) : strpos($redirectPath, DIRECTORY_SEPARATOR) === 0)) {
            $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'], "\\/");
            $normalized = str_replace('\\\\', '/', $redirectPath);
            $docRootNorm = str_replace('\\\\', '/', $docRoot);
            if (stripos($normalized, $docRootNorm) === 0) {
                $finalRedirect = substr($normalized, strlen($docRootNorm));
                if ($finalRedirect === '') $finalRedirect = '/';
            }
        }
        header('Location: ' . $finalRedirect);
        exit();
    }
}

/**
 * Hàm xác thực đăng nhập
 * @param mysqli $conn
 * @param string $username
 * @param string $password
 * @return array|false Trả về thông tin user nếu đúng, false nếu sai
 */
function authenticateUser($conn, $username, $password) {
    $sql = "SELECT id, username, password, role FROM users WHERE username = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        // Prepare thất bại — ghi log lỗi để debug
        error_log('MySQL prepare failed: ' . mysqli_error($conn));
        return false;
    }
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        // Hỗ trợ cả mật khẩu hash (password_hash) và plaintext cũ
        if (password_verify($password, $user['password']) || $password === $user['password']) {
            mysqli_stmt_close($stmt);
            return $user;
        }
    }
    if ($stmt) mysqli_stmt_close($stmt);
    return false;
}

?>


