<?php
require_once __DIR__ . '/db_connection.php';

// ===== TICKET PRICES FUNCTIONS =====

function getTicketPriceByType($ticket_type) {
    $conn = getDbConnection();
    
    if (!$conn) return null;
    
    $stmt = mysqli_prepare($conn, "SELECT * FROM ticket_prices WHERE ticket_type = ?");
    mysqli_stmt_bind_param($stmt, "s", $ticket_type);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $data;
}

function getAllTicketPrices() {
    $conn = getDbConnection();
    
    if (!$conn) return [];
    
    $result = mysqli_query($conn, "SELECT * FROM ticket_prices ORDER BY id ASC");
    $prices = [];
    
    if (!$result) {
        mysqli_close($conn);
        return [];
    }
    
    while ($row = mysqli_fetch_assoc($result)) {
        $prices[] = $row;
    }
    
    mysqli_close($conn);
    return $prices;
}

function updateTicketPrice($ticket_type, $base_price, $description) {
    $conn = getDbConnection();
    
    if (!$conn) return false;
    
    $stmt = mysqli_prepare($conn, "UPDATE ticket_prices SET base_price = ?, description = ?, updated_at = NOW() WHERE ticket_type = ?");
    mysqli_stmt_bind_param($stmt, "dss", $base_price, $description, $ticket_type);
    $success = mysqli_stmt_execute($stmt);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $success;
}

// ===== PROMOTIONS FUNCTIONS =====

function getAllPromotions() {
    $conn = getDbConnection();
    
    if (!$conn) return [];
    
    $result = mysqli_query($conn, "SELECT * FROM promotions ORDER BY created_at DESC");
    $promotions = [];
    
    if (!$result) {
        mysqli_close($conn);
        return [];
    }
    
    while ($row = mysqli_fetch_assoc($result)) {
        $promotions[] = $row;
    }
    
    mysqli_close($conn);
    return $promotions;
}

function getActivePromotions() {
    $conn = getDbConnection();
    
    if (!$conn) return [];
    
    $today = date('Y-m-d');
    $query = "SELECT * FROM promotions WHERE is_active = 1 AND start_date <= '$today' AND end_date >= '$today' ORDER BY created_at DESC";
    $result = mysqli_query($conn, $query);
    $promotions = [];
    
    if (!$result) {
        mysqli_close($conn);
        return [];
    }
    
    while ($row = mysqli_fetch_assoc($result)) {
        $promotions[] = $row;
    }
    
    mysqli_close($conn);
    return $promotions;
}

function addPromotion($name, $ticket_type, $discount_percent, $discount_amount, $start_date, $end_date) {
    $conn = getDbConnection();
    
    if (!$conn) return false;
    
    $stmt = mysqli_prepare($conn, "INSERT INTO promotions (name, ticket_type, discount_percent, discount_amount, start_date, end_date, is_active) VALUES (?, ?, ?, ?, ?, ?, 1)");
    mysqli_stmt_bind_param($stmt, "ssidss", $name, $ticket_type, $discount_percent, $discount_amount, $start_date, $end_date);
    $success = mysqli_stmt_execute($stmt);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $success;
}

function updatePromotion($id, $name, $ticket_type, $discount_percent, $discount_amount, $start_date, $end_date, $is_active) {
    $conn = getDbConnection();
    
    if (!$conn) return false;
    
    $stmt = mysqli_prepare($conn, "UPDATE promotions SET name = ?, ticket_type = ?, discount_percent = ?, discount_amount = ?, start_date = ?, end_date = ?, is_active = ?, updated_at = NOW() WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ssidssii", $name, $ticket_type, $discount_percent, $discount_amount, $start_date, $end_date, $is_active, $id);
    $success = mysqli_stmt_execute($stmt);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $success;
}

function deletePromotion($id) {
    $conn = getDbConnection();
    
    if (!$conn) return false;
    
    $stmt = mysqli_prepare($conn, "DELETE FROM promotions WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    $success = mysqli_stmt_execute($stmt);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $success;
}

function togglePromotionStatus($id) {
    $conn = getDbConnection();
    
    if (!$conn) return false;
    
    $stmt = mysqli_prepare($conn, "UPDATE promotions SET is_active = !is_active, updated_at = NOW() WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    $success = mysqli_stmt_execute($stmt);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $success;
}

function getPromotionById($id) {
    $conn = getDbConnection();
    
    if (!$conn) return null;
    
    $stmt = mysqli_prepare($conn, "SELECT * FROM promotions WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data = mysqli_fetch_assoc($result);
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    
    return $data;
}

// Calculate final price with promotion
function calculateFinalPrice($ticket_type, $base_price = null) {
    if (!$base_price) {
        $priceData = getTicketPriceByType($ticket_type);
        if (!$priceData) return null;
        $base_price = $priceData['base_price'];
    }
    
    $promotions = getActivePromotions();
    
    foreach ($promotions as $promo) {
        if ($promo['ticket_type'] === $ticket_type) {
            if ($promo['discount_percent'] > 0) {
                return $base_price * (1 - $promo['discount_percent'] / 100);
            } elseif ($promo['discount_amount'] > 0) {
                return max(0, $base_price - $promo['discount_amount']);
            }
        }
    }
    
    return $base_price;
}
?>
