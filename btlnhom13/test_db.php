<?php
require_once 'functions/db_connection.php';

$conn = getDbConnection();

if ($conn) {
    echo "✅ Kết nối thành công!";
} else {
    echo "❌ Kết nối thất bại!";
}
?>
