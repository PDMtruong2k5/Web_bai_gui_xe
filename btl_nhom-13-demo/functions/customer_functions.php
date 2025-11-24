<?php
require_once 'db_connection.php';

/**
 * Lấy tất cả khách hàng từ database
 */
function getAllCustomers() {
    $conn = getDbConnection();
    
    // Debug: Log connection info
    error_log("DB Connection: " . ($conn ? "success" : "failed"));
    
    $sql = "SELECT id, name, phone, license_plate, created_at 
            FROM customers 
            ORDER BY created_at DESC";
    
    // Debug: Log SQL query
    error_log("SQL Query: " . $sql);
    
    $result = mysqli_query($conn, $sql);
    
    // Debug: Log query result
    error_log("Query result: " . ($result ? "success" : "failed: " . mysqli_error($conn)));
    
    $customers = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $customers[] = $row;
        }
        // Debug: Log found customers
        error_log("Found customers: " . count($customers));
        error_log("First customer: " . print_r($customers[0], true));
    } else {
        error_log("No customers found. Num rows: " . ($result ? mysqli_num_rows($result) : "query failed"));
    }
    
    mysqli_close($conn);
    return $customers;
}

/**
 * Thêm khách hàng mới
 */
function addCustomer($name, $phone, $license_plate, $user_id = null, $role = 'customer') {
    $conn = getDbConnection();

    // If user_id provided, include it in the insert so the customer is linked to the user account
    if ($user_id !== null && is_numeric($user_id)) {
        $sql = "INSERT INTO customers (name, phone, license_plate, role, user_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            $err = mysqli_error($conn);
            error_log('addCustomer prepare failed: ' . $err);
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['db_error'] = $err;
            mysqli_close($conn);
            return false;
        }
        mysqli_stmt_bind_param($stmt, "ssssi", $name, $phone, $license_plate, $role, $user_id);
    } else {
        $sql = "INSERT INTO customers (name, phone, license_plate, role) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        if (!$stmt) {
            $err = mysqli_error($conn);
            error_log('addCustomer prepare failed: ' . $err);
            if (session_status() === PHP_SESSION_NONE) session_start();
            $_SESSION['db_error'] = $err;
            mysqli_close($conn);
            return false;
        }
        mysqli_stmt_bind_param($stmt, "ssss", $name, $phone, $license_plate, $role);
    }

    $success = mysqli_stmt_execute($stmt);
    if ($success === false) {
        $err = mysqli_error($conn);
        error_log('addCustomer execute failed: ' . $err);
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['db_error'] = $err;
    }
    $insertId = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $insertId ? $insertId : $success;
}

/**
 * Lấy khách hàng theo ID
 */
function getCustomerById($id) {
    $conn = getDbConnection();
    
    $sql = "SELECT id, name, phone, license_plate, created_at 
            FROM customers 
            WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result && mysqli_num_rows($result) > 0) {
            $customer = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $customer;
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
    return null;
}

/**
 * Cập nhật thông tin khách hàng
 */
function updateCustomer($id, $name, $phone, $license_plate) {
    $conn = getDbConnection();
    
    $sql = "UPDATE customers 
        SET name = ?, phone = ?, license_plate = ?
            WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssi", $name, $phone, $license_plate, $id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    mysqli_close($conn);
    return false;
}

/**
 * Xóa khách hàng
 */
function deleteCustomer($id) {
    $conn = getDbConnection();
    
    $sql = "DELETE FROM customers WHERE id = ?";
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

/**
 * Kiểm tra khách hàng có vé không
 */
function customerHasTickets($customerId) {
    $conn = getDbConnection();
    $sql = "SELECT 1 FROM tickets WHERE customer_id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $customerId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $has = $result && mysqli_num_rows($result) > 0;
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $has;
    }
    mysqli_close($conn);
    return false;
}

/**
 * Tìm kiếm khách hàng theo tên, số điện thoại hoặc biển số
 */
function searchCustomers($keyword) {
    $conn = getDbConnection();
    
    $like = "%" . $keyword . "%";
    $sql = "SELECT id, name, phone, license_plate, created_at 
        FROM customers 
        WHERE name LIKE ? OR phone LIKE ? OR license_plate LIKE ?
        ORDER BY created_at DESC";
    
    $stmt = mysqli_prepare($conn, $sql);
    $customers = [];
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sss", $like, $like, $like);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $customers[] = $row;
            }
        }
        mysqli_stmt_close($stmt);
    }
    
    mysqli_close($conn);
    return $customers;
}

/**
 * Lấy danh sách khách hàng cho dropdown
 */
function getAllCustomersForDropdown() {
    $conn = getDbConnection();
    $sql = "SELECT id, name as name, license_plate FROM customers ORDER BY name";
    $result = mysqli_query($conn, $sql);
    $customers = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $customers[] = $row;
        }
    }
    mysqli_close($conn);
    return $customers;
}

/**
 * Lấy khách hàng theo `user_id` (liên kết tới bảng users)
 *
 * @param int $userId
 * @return array|null
 */
function getCustomerByUserId($userId) {
    $conn = getDbConnection();
    $sql = "SELECT id, name, phone, license_plate, user_id FROM customers WHERE user_id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $userId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result && mysqli_num_rows($result) > 0) {
            $customer = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $customer;
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
    return null;
}
?>


