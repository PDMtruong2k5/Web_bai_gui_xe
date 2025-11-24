<?php
require_once 'db_connection.php';

/**
 * Lấy tất cả vé từ database
 */
function getAllTickets() {
    $conn = getDbConnection();
    
    $sql = "SELECT t.id,
             t.customer_id,
             t.ticket_type,
             t.start_date,
             t.end_date,
             t.price,
             t.status,
             t.created_at,
             c.name AS customer_name,
             c.phone,
             c.license_plate
         FROM tickets t
         LEFT JOIN customers c ON t.customer_id = c.id
         ORDER BY t.created_at DESC";
    
    $result = mysqli_query($conn, $sql);
    
    $tickets = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $tickets[] = $row;
        }
    }
    
    mysqli_close($conn);
    return $tickets;
}

/**
 * Thêm vé mới
 */
function addTicket($customer_id, $ticket_type, $start_date, $end_date, $price) {
    $conn = getDbConnection();
    
        // New tickets start as 'pending_payment' until admin confirms payment
        $sql = "INSERT INTO tickets (customer_id, ticket_type, start_date, end_date, price, status) 
            VALUES (?, ?, ?, ?, ?, 'pending_payment')";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "isssd", $customer_id, $ticket_type, $start_date, $end_date, $price);
        $success = mysqli_stmt_execute($stmt);
        if ($success) {
            $insertId = mysqli_insert_id($conn);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $insertId; // return inserted ticket id on success
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
    return false;
}

/**
 * Lấy vé theo ID
 */
function getTicketById($id) {
    $conn = getDbConnection();
    
    $sql = "SELECT t.id,
             t.customer_id,
             t.ticket_type,
             t.start_date,
             t.end_date,
             t.price,
             t.status,
             t.created_at,
             c.name AS customer_name,
             c.phone,
             c.license_plate
         FROM tickets t
         LEFT JOIN customers c ON t.customer_id = c.id
         WHERE t.id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $ticket = null;
        if ($result && mysqli_num_rows($result) > 0) {
            $ticket = mysqli_fetch_assoc($result);
        }
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $ticket;
    }
    mysqli_close($conn);
    return null;
}

/**
 * Cập nhật vé
 */
function updateTicket($id, $customer_id, $ticket_type, $start_date, $end_date, $price, $status) {
    $conn = getDbConnection();
    
    $sql = "UPDATE tickets 
            SET customer_id = ?, ticket_type = ?, start_date = ?, end_date = ?, price = ?, status = ?
            WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "isssdsi", $customer_id, $ticket_type, $start_date, $end_date, $price, $status, $id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    mysqli_close($conn);
    return false;
}

/**
 * Gia hạn vé (extend ticket)
 */
function extendTicket($id, $new_end_date) {
    $conn = getDbConnection();
    
    $sql = "UPDATE tickets SET end_date = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "si", $new_end_date, $id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    mysqli_close($conn);
    return false;
}

/**
 * Hủy vé (cancel ticket)
 */
function cancelTicket($id) {
    $conn = getDbConnection();
    
    $sql = "UPDATE tickets SET status = 'cancelled' WHERE id = ?";
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
 * Xóa vé
 */
function deleteTicket($id) {
    $conn = getDbConnection();
    
    $sql = "DELETE FROM tickets WHERE id = ?";
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
 * Lấy giá vé theo loại (từ bảng ticket_prices hoặc giá mặc định)
 */
function getTicketPrice($ticket_type) {
    $conn = getDbConnection();
    
    $sql = "SELECT price FROM ticket_prices WHERE ticket_type = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $ticket_type);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $row['price'];
        }
        mysqli_stmt_close($stmt);
    }
    
    // Giá mặc định nếu không có trong database
    $defaultPrices = [
        'day' => 10000,
        'month' => 200000,
        'year' => 2000000
    ];
    
    mysqli_close($conn);
    return isset($defaultPrices[$ticket_type]) ? $defaultPrices[$ticket_type] : 0;
}

/**
 * Cập nhật giá vé
 */
function updateTicketPrice($ticket_type, $price) {
    $conn = getDbConnection();
    
    // Kiểm tra xem đã có giá chưa
    $checkSql = "SELECT id FROM ticket_prices WHERE ticket_type = ?";
    $stmt = mysqli_prepare($conn, $checkSql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $ticket_type);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $exists = mysqli_num_rows($result) > 0;
        mysqli_stmt_close($stmt);
        
        if ($exists) {
            // Cập nhật (order parameters: price, ticket_type)
            $sql = "UPDATE ticket_prices SET price = ? WHERE ticket_type = ?";
            $stmt = mysqli_prepare($conn, $sql);
            if ($stmt) {
                // bind double then string
                mysqli_stmt_bind_param($stmt, "ds", $price, $ticket_type);
                $success = mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                return $success;
            }
        } else {
            // Thêm mới (order: ticket_type, price)
            $sql = "INSERT INTO ticket_prices (ticket_type, price) VALUES (?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "sd", $ticket_type, $price);
                $success = mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                mysqli_close($conn);
                return $success;
            }
        }
    }
    mysqli_close($conn);
    return false;
}

/**
 * Tính ngày kết thúc dựa trên loại vé và ngày bắt đầu
 */
function calculateEndDate($start_date, $ticket_type) {
    $start = new DateTime($start_date);
    
    switch ($ticket_type) {
        case 'day':
            $start->modify('+1 day');
            break;
        case 'month':
            $start->modify('+1 month');
            break;
        case 'year':
            $start->modify('+1 year');
            break;
        default:
            $start->modify('+1 day');
    }
    
    return $start->format('Y-m-d');
}

/**
 * Tìm kiếm vé theo biển số hoặc tên khách hàng
 */
function searchTickets($keyword) {
    $conn = getDbConnection();
    
    $like = "%" . $keyword . "%";
    $sql = "SELECT t.id,
             t.customer_id,
             t.ticket_type,
             t.start_date,
             t.end_date,
             t.price,
             t.status,
             t.created_at,
             c.name AS customer_name,
             c.phone,
             c.license_plate
         FROM tickets t
         LEFT JOIN customers c ON t.customer_id = c.id
         WHERE c.name LIKE ? OR c.license_plate LIKE ? OR c.phone LIKE ?
         ORDER BY t.created_at DESC";
    
    $stmt = mysqli_prepare($conn, $sql);
    $tickets = [];
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sss", $like, $like, $like);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $tickets[] = $row;
            }
        }
        mysqli_stmt_close($stmt);
    }
    
    mysqli_close($conn);
    return $tickets;
}

/**
 * Lấy danh sách vé của một khách hàng theo customer_id
 */
function getTicketsByCustomerId($customerId) {
    $conn = getDbConnection();
    $sql = "SELECT t.id,
             t.customer_id,
             t.ticket_type,
             t.start_date,
             t.end_date,
             t.price,
             t.status,
             t.created_at,
             c.name AS customer_name,
             c.phone,
             c.license_plate
         FROM tickets t
         LEFT JOIN customers c ON t.customer_id = c.id
         WHERE t.customer_id = ?
         ORDER BY t.created_at DESC";
    $stmt = mysqli_prepare($conn, $sql);
    $tickets = [];
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $customerId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result && mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $tickets[] = $row;
            }
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
    return $tickets;
}

/**
 * Lấy tất cả vé với chi tiết khách hàng (alias cho getAllTickets)
 */
function getAllTicketsWithDetails() {
    return getAllTickets();
}

/**
 * Lấy tổng số vé
 */
function getTotalTickets() {
    $conn = getDbConnection();
    $sql = "SELECT COUNT(*) as count FROM tickets";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        mysqli_close($conn);
        return $row['count'];
    }
    mysqli_close($conn);
    return 0;
}

/**
 * Lấy số vé đang hoạt động
 */
function getActiveTickets() {
    $conn = getDbConnection();
    $sql = "SELECT COUNT(*) as count FROM tickets WHERE status = 'active'";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        mysqli_close($conn);
        return $row['count'];
    }
    mysqli_close($conn);
    return 0;
}

/**
 * Lấy số vé tháng
 */
function getMonthlyTickets() {
    $conn = getDbConnection();
    $sql = "SELECT COUNT(*) as count FROM tickets WHERE ticket_type = 'month'";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        mysqli_close($conn);
        return $row['count'];
    }
    mysqli_close($conn);
    return 0;
}

/**
 * Lấy tổng doanh thu
 */
function getTotalRevenue() {
    $conn = getDbConnection();
    $sql = "SELECT SUM(price) as total FROM tickets WHERE status = 'active'";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        mysqli_close($conn);
        return $row['total'] ?? 0;
    }
    mysqli_close($conn);
    return 0;
}
?>

