<?php
/**
 * Script trợ giúp chạy file SQL tạo bảng (create_missing_tables.sql)
 * Cách dùng: mở http://localhost/btl_nhom-13-demo/tools/run_create_tables.php trong trình duyệt
 * Hoặc chạy từ CLI: php tools/run_create_tables.php
 */
require_once __DIR__ . '/../functions/db_connection.php';

$sqlFile = __DIR__ . '/../sql/create_missing_tables.sql';
if (!file_exists($sqlFile)) {
    echo "File SQL không tồn tại: $sqlFile";
    exit;
}

$sql = file_get_contents($sqlFile);
$conn = getDbConnection();

// Sử dụng multi_query để chạy nhiều lệnh SQL trong file
if (mysqli_multi_query($conn, $sql)) {
    // Đọc tất cả kết quả để đảm bảo lệnh chạy hết
    do {
        if ($result = mysqli_store_result($conn)) {
            mysqli_free_result($result);
        }
    } while (mysqli_more_results($conn) && mysqli_next_result($conn));

    echo "Đã chạy file SQL thành công. Kiểm tra trong MySQL Workbench hoặc dùng SHOW TABLES;";
} else {
    echo "Lỗi khi chạy SQL: " . mysqli_error($conn);
}

mysqli_close($conn);

?>
