<?php
require_once __DIR__ . '/../functions/db_connection.php';

$conn = getDbConnection();

// Check if `customers` table exists
$res = mysqli_query($conn, "SHOW TABLES LIKE 'customers'");
if (!$res || mysqli_num_rows($res) === 0) {
    // Create customers table if it doesn't exist
    $createSql = "CREATE TABLE IF NOT EXISTS `customers` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `name` VARCHAR(255) NOT NULL,
        `phone` VARCHAR(20) NOT NULL,
        `license_plate` VARCHAR(20) NOT NULL,
        `role` VARCHAR(50) DEFAULT 'customer',
        `user_id` INT UNSIGNED,
        `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

    if (mysqli_query($conn, $createSql)) {
        echo "Table `customers` created successfully.<br>";
    } else {
        echo "Error creating `customers` table: " . mysqli_error($conn) . "<br>";
        mysqli_close($conn);
        exit;
    }
}

// Sample data to insert
$customers = [
    ['name' => 'Phạm Đình Minh Trường', 'phone' => '0353971156', 'license_plate' => '99H-95D9'],
    ['name' => 'Nguyễn Văn A', 'phone' => '0912345678', 'license_plate' => '30A-12345'],
    ['name' => 'Trần Thị B', 'phone' => '0987654321', 'license_plate' => '51B-54321'],
    ['name' => 'Lê Văn C', 'phone' => '0934567890', 'license_plate' => '29C-98765'],
    ['name' => 'Hoàng Thị D', 'phone' => '0945678901', 'license_plate' => '36D-11111'],
];

$inserted = 0;
$failed = 0;

foreach ($customers as $customer) {
    $name = $customer['name'];
    $phone = $customer['phone'];
    $license_plate = $customer['license_plate'];
    
    // Check if customer already exists
    $checkSql = "SELECT id FROM customers WHERE phone = ? OR license_plate = ? LIMIT 1";
    $checkStmt = mysqli_prepare($conn, $checkSql);
    if ($checkStmt) {
        mysqli_stmt_bind_param($checkStmt, "ss", $phone, $license_plate);
        mysqli_stmt_execute($checkStmt);
        $checkRes = mysqli_stmt_get_result($checkStmt);
        
        if (mysqli_num_rows($checkRes) > 0) {
            echo "Khách hàng '$name' đã tồn tại (số điện thoại: $phone). Bỏ qua.<br>";
            mysqli_stmt_close($checkStmt);
            continue;
        }
        mysqli_stmt_close($checkStmt);
    }
    
    // Insert new customer
    $insertSql = "INSERT INTO customers (name, phone, license_plate) VALUES (?, ?, ?)";
    $insertStmt = mysqli_prepare($conn, $insertSql);
    
    if ($insertStmt) {
        mysqli_stmt_bind_param($insertStmt, "sss", $name, $phone, $license_plate);
        if (mysqli_stmt_execute($insertStmt)) {
            echo "✓ Thêm khách hàng: <b>$name</b> (Điện thoại: $phone, BKS: $license_plate)<br>";
            $inserted++;
        } else {
            echo "✗ Lỗi thêm khách hàng '$name': " . mysqli_error($conn) . "<br>";
            $failed++;
        }
        mysqli_stmt_close($insertStmt);
    } else {
        echo "✗ Lỗi prepare SQL cho '$name': " . mysqli_error($conn) . "<br>";
        $failed++;
    }
}

echo "<hr>";
echo "<b>Kết quả:</b><br>";
echo "✓ Thêm thành công: <b>$inserted</b> khách hàng<br>";
if ($failed > 0) {
    echo "✗ Thất bại: <b>$failed</b> khách hàng<br>";
}

mysqli_close($conn);

?>
