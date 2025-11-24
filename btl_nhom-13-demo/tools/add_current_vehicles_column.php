<?php
require_once __DIR__ . '/../functions/db_connection.php';

$conn = getDbConnection();

// Check if `areas` table exists
$res = mysqli_query($conn, "SHOW TABLES LIKE 'areas'");
if (!$res || mysqli_num_rows($res) === 0) {
    // Create minimal areas table with current_vehicles
    $createSql = "CREATE TABLE IF NOT EXISTS `areas` (
        `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        `area_name` VARCHAR(255) NOT NULL,
        `area_desc` TEXT,
        `current_vehicles` INT NOT NULL DEFAULT 0,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

    if (mysqli_query($conn, $createSql)) {
        echo "Table `areas` created with column `current_vehicles`.";
    } else {
        echo "Error creating `areas` table: " . mysqli_error($conn);
    }
    mysqli_close($conn);
    exit;
}

// Check if column current_vehicles exists
$colRes = mysqli_query($conn, "SHOW COLUMNS FROM `areas` LIKE 'current_vehicles'");
if ($colRes && mysqli_num_rows($colRes) > 0) {
    echo "Column `current_vehicles` already exists in `areas`.";
    mysqli_close($conn);
    exit;
}

$alterSql = "ALTER TABLE `areas` ADD COLUMN `current_vehicles` INT NOT NULL DEFAULT 0";
if (mysqli_query($conn, $alterSql)) {
    echo "Column `current_vehicles` added to `areas`.";
} else {
    echo "Error altering `areas` table: " . mysqli_error($conn);
}

mysqli_close($conn);

?>
