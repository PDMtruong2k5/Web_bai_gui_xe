    <?php
    session_start();
    require_once '../functions/db_connection.php';
    require_once '../functions/auth.php'; // Thêm dòng này

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
        handleLogin();
    }

    function handleLogin() {
        $conn = getDbConnection();
        // Trim inputs to avoid stray spaces
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($username) || empty($password)) {
            $_SESSION['error'] = 'Vui lòng nhập đầy đủ username và password!';
            header('Location: ../index.php');
            exit();
        }

        $user = authenticateUser($conn, $username, $password);
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['success'] = 'Đăng nhập thành công!';
            mysqli_close($conn);
            
            // Chuyển hướng dựa trên role
            if ($user['role'] === 'admin') {
                header('Location: ../views/area.php');
            } else {
                header('Location: ../views/customer_home.php');
            }
            exit();
        }

        $_SESSION['error'] = 'Tên đăng nhập hoặc mật khẩu không đúng!';
        mysqli_close($conn);
        header('Location: ../index.php');
        exit();

        
    }
    ?>