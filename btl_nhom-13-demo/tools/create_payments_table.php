<?php
require_once __DIR__ . '/../functions/db_connection.php';

$conn = getDbConnection();

// Check if `payments` table exists
$res = mysqli_query($conn, "SHOW TABLES LIKE 'payments'");
if ($res && mysqli_num_rows($res) > 0) {
    echo "Table `payments` already exists.";
    mysqli_close($conn);
    exit;
}

// Create payments table
$createSql = "CREATE TABLE IF NOT EXISTS `payments` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `ticket_id` INT NOT NULL,
    `amount` DECIMAL(10, 2) NOT NULL,
    `note` TEXT,
    `status` VARCHAR(50) NOT NULL DEFAULT 'pending',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `paid_at` TIMESTAMP NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

if (mysqli_query($conn, $createSql)) {
    echo "Table `payments` created successfully.";
} else {
    echo "Error creating `payments` table: " . mysqli_error($conn);
}

mysqli_close($conn);

?>
