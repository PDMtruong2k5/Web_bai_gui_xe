<?php

require_once 'db_connection.php';

// Get all ticket prices
function getAllTicketPrices() {
    $conn = getDbConnection();
    $sql = "SELECT * FROM ticket_prices ORDER BY id";
    $result = mysqli_query($conn, $sql);
    $prices = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $prices[] = $row;
        }
    }
    mysqli_close($conn);
    return $prices;
}

// Get ticket price by ID
function getTicketPriceById($id) {
    $conn = getDbConnection();
    $sql = "SELECT * FROM ticket_prices WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result && mysqli_num_rows($result) > 0) {
            $price = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $price;
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
    return null;
}

// Add new ticket price
function addTicketPrice($name, $price, $duration_minutes = 60, $description = '') {
    $conn = getDbConnection();
    $sql = "INSERT INTO ticket_prices (name, price, duration_minutes, description) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sdi s", $name, $price, $duration_minutes, $description);
        $success = mysqli_stmt_execute($stmt);
        $price_id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success ? $price_id : false;
    }
    mysqli_close($conn);
    return false;
}

// Update ticket price
function updateTicketPrice($id, $name, $price, $duration_minutes = 60, $description = '') {
    $conn = getDbConnection();
    $sql = "UPDATE ticket_prices SET name = ?, price = ?, duration_minutes = ?, description = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sdis i", $name, $price, $duration_minutes, $description, $id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    mysqli_close($conn);
    return false;
}

// Delete ticket price
function deleteTicketPrice($id) {
    $conn = getDbConnection();
    $sql = "DELETE FROM ticket_prices WHERE id = ?";
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

// Get ticket price by name
function getTicketPriceByName($name) {
    $conn = getDbConnection();
    $sql = "SELECT * FROM ticket_prices WHERE name = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $name);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result && mysqli_num_rows($result) > 0) {
            $price = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $price;
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
    return null;
}

?>
