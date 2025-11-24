<?php

require_once 'db_connection.php';

// Get all notifications
function getAllNotifications() {
    $conn = getDbConnection();
    $sql = "SELECT * FROM notifications ORDER BY created_at DESC";
    $result = mysqli_query($conn, $sql);
    $notifications = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $notifications[] = $row;
        }
    }
    mysqli_close($conn);
    return $notifications;
}

// Get active notifications only
function getActiveNotifications() {
    $conn = getDbConnection();
    $sql = "SELECT * FROM notifications WHERE is_active = TRUE ORDER BY created_at DESC";
    $result = mysqli_query($conn, $sql);
    $notifications = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $notifications[] = $row;
        }
    }
    mysqli_close($conn);
    return $notifications;
}

// Get notification by ID
function getNotificationById($id) {
    $conn = getDbConnection();
    $sql = "SELECT * FROM notifications WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result && mysqli_num_rows($result) > 0) {
            $notification = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $notification;
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
    return null;
}

// Add new notification
function addNotification($title, $message, $type = 'info', $is_active = true) {
    $conn = getDbConnection();
    $sql = "INSERT INTO notifications (title, message, type, is_active) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssi", $title, $message, $type, $is_active);
        $success = mysqli_stmt_execute($stmt);
        $notification_id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success ? $notification_id : false;
    }
    mysqli_close($conn);
    return false;
}

// Update notification
function updateNotification($id, $title, $message, $type, $is_active) {
    $conn = getDbConnection();
    $sql = "UPDATE notifications SET title = ?, message = ?, type = ?, is_active = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "sssii", $title, $message, $type, $is_active, $id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    mysqli_close($conn);
    return false;
}

// Delete notification
function deleteNotification($id) {
    $conn = getDbConnection();
    $sql = "DELETE FROM notifications WHERE id = ?";
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

// Toggle notification active status
function toggleNotification($id, $is_active) {
    $conn = getDbConnection();
    $sql = "UPDATE notifications SET is_active = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ii", $is_active, $id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    mysqli_close($conn);
    return false;
}

?>
