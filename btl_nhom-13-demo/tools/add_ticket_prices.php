<?php
require_once __DIR__ . '/../functions/db_connection.php';

$conn = getDbConnection();

// SQL để thêm các loại vé
$sql = "INSERT INTO ticket_prices (ticket_type, base_price, description, created_at, updated_at) 
VALUES 
('day', 10000, 'Vé gửi xe theo ngày', NOW(), NOW()),
('month', 200000, 'Vé gửi xe theo tháng', NOW(), NOW()),
('year', 2000000, 'Vé gửi xe theo năm', NOW(), NOW())
ON DUPLICATE KEY UPDATE 
base_price = VALUES(base_price),
description = VALUES(description),
updated_at = NOW()";

if (mysqli_multi_query($conn, $sql)) {
    echo "✓ Thêm các loại vé thành công!<br>";
    echo "<br>Danh sách loại vé:<br>";
    
    // Lấy danh sách vé vừa thêm
    $result = mysqli_query($conn, "SELECT * FROM ticket_prices ORDER BY id ASC");
    while ($row = mysqli_fetch_assoc($result)) {
        echo "- " . ucfirst($row['ticket_type']) . ": " . number_format($row['base_price']) . "đ<br>";
    }
} else {
    echo "✗ Lỗi: " . mysqli_error($conn);
}

mysqli_close($conn);
?>
