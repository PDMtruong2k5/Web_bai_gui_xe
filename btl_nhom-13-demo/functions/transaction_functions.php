<?php
require_once 'db_connection.php';

/**
 * Lấy danh sách giao dịch chưa thanh toán
 */
function getPendingTransactions() {
    $conn = getDbConnection();
    $sql = "SELECT t.id, t.vehicle_id, t.service_id, t.transaction AS amount, t.status, v.vehicle_owner AS customer_name 
            FROM transactions t 
            LEFT JOIN vehicles v ON t.vehicle_id = v.id 
            WHERE t.status = 'pending' 
            ORDER BY t.id DESC";
    $result = mysqli_query($conn, $sql);
    $transactions = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $transactions[] = $row;
        }
    }
    mysqli_close($conn);
    return $transactions;
}

/**
 * Xác nhận thanh toán giao dịch
 */
function confirmTransactionPayment($transaction_id) {
    $conn = getDbConnection();
    $sql = "UPDATE transactions SET status = 'paid' WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $transaction_id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    mysqli_close($conn);
    return false;
}

/**
 * Lấy tất cả giao dịch (transactions) từ database với thông tin xe và loại dịch vụ
 */
function getAllTransactions() {
    $conn = getDbConnection();

    $sql = "SELECT t.id,
             t.vehicle_id,
             t.service_id,
             t.`transaction` AS transaction,
             v.vehicle_plate,
             v.vehicle_owner,
             sv.service_code,
             sv.service_name
         FROM transactions t
         LEFT JOIN vehicles v ON t.vehicle_id = v.id
         LEFT JOIN services sv ON t.service_id = sv.id
         ORDER BY t.id";

    $result = mysqli_query($conn, $sql);

    $transactions = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $transactions[] = $row;
        }
    }

    mysqli_close($conn);
    return $transactions;
}

/**
 * Lấy danh sách giao dịch đã thanh toán
 */
function getPaidTransactions() {
    $conn = getDbConnection();
    $sql = "SELECT t.id, t.vehicle_id, t.service_id, t.`transaction` AS amount, t.status, v.vehicle_owner AS customer_name 
            FROM transactions t 
            LEFT JOIN vehicles v ON t.vehicle_id = v.id 
            WHERE t.status = 'paid' 
            ORDER BY t.id DESC";
    $result = mysqli_query($conn, $sql);
    $transactions = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $transactions[] = $row;
        }
    }
    mysqli_close($conn);
    return $transactions;
}

function addTransaction($vehicle_id, $service_id, $transaction) {
    $conn = getDbConnection();

    $sql = "INSERT INTO transactions (vehicle_id, service_id, `transaction`) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        // vehicle_id (i), service_id (i), transaction (s)
        mysqli_stmt_bind_param($stmt, "iis", $vehicle_id, $service_id, $transaction);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    mysqli_close($conn);
    return false;
}

function getTransactionById($id) {
    $conn = getDbConnection();

    $sql = "SELECT t.id,
             t.vehicle_id,
             t.service_id,
             t.`transaction` AS transaction,
             v.vehicle_plate,
             v.vehicle_owner,
             sv.service_code,
             sv.service_name
         FROM transactions t
         LEFT JOIN vehicles v ON t.vehicle_id = v.id
         LEFT JOIN services sv ON t.service_id = sv.id
         WHERE t.id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $transaction = null;
        if ($result && mysqli_num_rows($result) > 0) {
            $transaction = mysqli_fetch_assoc($result);
        }
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $transaction;
    }
    mysqli_close($conn);
    return null;
}

function updateTransaction($id, $vehicle_id, $service_id, $transaction) {
    $conn = getDbConnection();
    $sql = "UPDATE transactions SET vehicle_id = ?, service_id = ?, `transaction` = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        // vehicle_id (i), service_id (i), transaction (s), id (i)
        mysqli_stmt_bind_param($stmt, "iisi", $vehicle_id, $service_id, $transaction, $id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    mysqli_close($conn);
    return false;
}

function deleteTransaction($id) {
    $conn = getDbConnection();

    $sql = "DELETE FROM transactions WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    mysqli_close($conn);
    return false;
}

function checkTransactionExists($vehicle_id, $service_id) {
    $conn = getDbConnection();

    $sql = "SELECT id FROM transactions WHERE vehicle_id = ? AND service_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $vehicle_id, $service_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $exists = mysqli_num_rows($result) > 0;
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $exists;
    }
    mysqli_close($conn);
    return false;
}

function getAllVehiclesForDropdown() {
    $conn = getDbConnection();
    $sql = "SELECT id, vehicle_plate, vehicle_owner FROM vehicles ORDER BY vehicle_owner";
    $result = mysqli_query($conn, $sql);
    $vehicles = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $vehicles[] = $row;
        }
    }
    mysqli_close($conn);
    return $vehicles;
}

function getAllServiceTypesForDropdown() {
    $conn = getDbConnection();
    // Dùng bảng services như là loại dịch vụ
    $sql = "SELECT id, service_code, service_name FROM services ORDER BY service_name";
    $result = mysqli_query($conn, $sql);
    $services = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $services[] = $row;
        }
    }
    mysqli_close($conn);
    return $services;
}

/**
 * Tìm kiếm giao dịch theo biển số xe (student_code)
 * @param string $plateKeyword Từ khóa biển số (hỗ trợ LIKE)
 * @return array Danh sách giao dịch phù hợp
 */
function findTransactionsByPlate($plateKeyword) {
    $conn = getDbConnection();

    $like = "%" . $plateKeyword . "%";
    $sql = "SELECT t.id,
             t.vehicle_id,
             t.service_id,
             t.`transaction` AS transaction,
             v.vehicle_plate,
             v.vehicle_owner,
             sv.service_code,
             sv.service_name
         FROM transactions t
         LEFT JOIN vehicles v ON t.vehicle_id = v.id
         LEFT JOIN services sv ON t.service_id = sv.id
         WHERE v.vehicle_plate LIKE ?
         ORDER BY t.id DESC";

    $stmt = mysqli_prepare($conn, $sql);
    $transactions = [];
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $like);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $transactions[] = $row;
            }
        }
        mysqli_stmt_close($stmt);
    }

    mysqli_close($conn);
    return $transactions;
}
?>


