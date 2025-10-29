<?php
require_once 'db_connection.php';

/**
 * Lấy tất cả danh sách services từ database
 * @return array Danh sách services
 */
function getAllServices() {
    $conn = getDbConnection();
    
    // Sử dụng bảng subjects hiện có và alias sang service_*
    $sql = "SELECT id, service_code, service_name FROM services ORDER BY id";
    $result = mysqli_query($conn, $sql);

    $services = [];
    if ($result && mysqli_num_rows($result) > 0) {
        // Lặp qua từng dòng trong kết quả truy vấn $result
        while ($row = mysqli_fetch_assoc($result)) { 
            $services[] = $row; // Thêm mảng $row vào cuối mảng $services
        }
    }
    
    mysqli_close($conn);
    return $services;
}

/**
 * Thêm service mới
 * @param string $service_code Mã dịch vụ
 * @param string $service_name Tên dịch vụ
 * @return bool True nếu thành công, False nếu thất bại
 */
function addService($service_code, $service_name) {
    $conn = getDbConnection();

    // Ghi vào bảng subjects, cột subject_*
    $sql = "INSERT INTO services (service_code, service_name) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $service_code, $service_name);
        $success = mysqli_stmt_execute($stmt);
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    
    mysqli_close($conn);
    return false;
}

/**
 * Lấy thông tin một service theo ID
 * @param int $id ID của service
 * @return array|null Thông tin service hoặc null nếu không tìm thấy
 */
function getServiceById($id) {
    $conn = getDbConnection();

    $sql = "SELECT id, service_code, service_name FROM services WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            $service = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $service;
        }
        
        mysqli_stmt_close($stmt);
    }
    
    mysqli_close($conn);
    return null;
}

/**
 * Cập nhật thông tin service
 * @param int $id ID của service
 * @param string $service_code Mã dịch vụ mới
 * @param string $service_name Tên dịch vụ mới
 * @return bool True nếu thành công, False nếu thất bại
 */
function updateService($id, $service_code, $service_name) {
    $conn = getDbConnection();
    
    $sql = "UPDATE services SET service_code = ?, service_name = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssi", $service_code, $service_name, $id);
        $success = mysqli_stmt_execute($stmt);
        
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    
    mysqli_close($conn);
    return false;
}

/**
 * Xóa service theo ID
 * @param int $id ID của service cần xóa
 * @return bool True nếu thành công, False nếu thất bại
 */
function deleteService($id) {
    $conn = getDbConnection();
    
    $sql = "DELETE FROM services WHERE id = ?";
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

function serviceHasTransactions($serviceId) {
    $conn = getDbConnection();
    // Kiểm tra bảng transactions để biết service có liên quan hay không
    $sql = "SELECT 1 FROM transactions WHERE service_id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $serviceId);
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
?>