<?php
require_once __DIR__ . '/../functions/db_connection.php';

$conn = getDbConnection();

$sql = "CREATE TABLE IF NOT EXISTS `notifications` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `message` TEXT,
  `type` VARCHAR(50) DEFAULT 'info',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

if (mysqli_query($conn, $sql)) {
    echo "Table `notifications` created or already exists.";
} else {
    echo "Error creating table: " . mysqli_error($conn);
}

mysqli_close($conn);

?>
