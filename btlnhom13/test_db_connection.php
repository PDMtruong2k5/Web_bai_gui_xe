<?php
// File thử nghiệm để kiểm tra kết nối DB nhanh
require_once __DIR__ . '/functions/db_connection.php';

// Tắt buffering để thấy output ngay
if (ob_get_level()) ob_end_flush();

try {
    $conn = getDbConnection();
    if ($conn) {
        echo "Kết nối database thành công.<br>";
        echo "Host info: " . htmlspecialchars(mysqli_get_host_info($conn)) . "<br>";
        echo "DB client version: " . mysqli_get_client_info() . "<br>";
        mysqli_close($conn);
    } else {
        echo "Kết nối trả về false";
    }
} catch (Throwable $e) {
    echo "Exception: " . htmlspecialchars($e->getMessage());
}

echo "<p>Ghi chú: nếu thấy thông báo 'Kết nối database thất bại' (die), kiểm tra lại thông tin trong <code>functions/db_connection.php</code> (host, username, password, dbname, port) và đảm bảo MySQL đang chạy.</p>";
