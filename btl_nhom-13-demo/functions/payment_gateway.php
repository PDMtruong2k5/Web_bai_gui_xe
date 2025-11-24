<?php
require_once __DIR__ . '/db_connection.php';

/**
 * Payment records stored in `payments` table.
 * SQL migration provided in `sql/create_payments_table.sql`.
 */

function create_payment_record($ticket_id, $amount, $note = '') {
    $conn = getDbConnection();
    $sql = "INSERT INTO payments (ticket_id, amount, note, status, created_at) VALUES (?, ?, ?, 'pending', NOW())";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) { mysqli_close($conn); return false; }
    mysqli_stmt_bind_param($stmt, 'ids', $ticket_id, $amount, $note);
    $ok = mysqli_stmt_execute($stmt);
    if ($ok) {
        $id = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return get_payment_by_id($id);
    }
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return false;
}

function get_payment_by_id($id) {
    $conn = getDbConnection();
    $sql = "SELECT id, ticket_id, amount, note, status, created_at, paid_at FROM payments WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) { mysqli_close($conn); return null; }
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = $res ? mysqli_fetch_assoc($res) : null;
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $row;
}

function get_payment_by_ticket_id($ticket_id) {
    $conn = getDbConnection();
    $sql = "SELECT id, ticket_id, amount, note, status, created_at, paid_at FROM payments WHERE ticket_id = ? ORDER BY id DESC LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) { mysqli_close($conn); return null; }
    mysqli_stmt_bind_param($stmt, 'i', $ticket_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = $res ? mysqli_fetch_assoc($res) : null;
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $row;
}

function load_all_payments() {
    $conn = getDbConnection();
    $sql = "SELECT id, ticket_id, amount, note, status, created_at, paid_at FROM payments ORDER BY id DESC";
    $result = mysqli_query($conn, $sql);
    $rows = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($r = mysqli_fetch_assoc($result)) $rows[] = $r;
    }
    mysqli_close($conn);
    return $rows;
}

function mark_payment_paid($id) {
    $conn = getDbConnection();
    $sql = "UPDATE payments SET status='paid', paid_at = NOW() WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) { mysqli_close($conn); return false; }
    mysqli_stmt_bind_param($stmt, 'i', $id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $ok;
}

function mark_payment_awaiting($id) {
    $conn = getDbConnection();
    $sql = "UPDATE payments SET status='awaiting_confirmation' WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) { mysqli_close($conn); return false; }
    mysqli_stmt_bind_param($stmt, 'i', $id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    return $ok;
}
