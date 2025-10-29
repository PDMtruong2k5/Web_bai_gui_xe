<?php
require_once 'db_connection.php';

// Area functions (uses `areas` table)

function getAllAreas() {
    $conn = getDbConnection();
    $sql = "SELECT id, area_name, area_desc FROM areas ORDER BY id";
    $result = mysqli_query($conn, $sql);
    $areas = [];
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $areas[] = $row;
        }
    }
    mysqli_close($conn);
    return $areas;
}

function addArea($area_name, $area_desc) {
    $conn = getDbConnection();
    $sql = "INSERT INTO areas (area_name, area_desc) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $area_name, $area_desc);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    mysqli_close($conn);
    return false;
}

function getAreaById($id) {
    $conn = getDbConnection();
    $sql = "SELECT id, area_name, area_desc FROM areas WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result && mysqli_num_rows($result) > 0) {
            $area = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $area;
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
    return null;
}

function updateArea($id, $area_name, $area_desc) {
    $conn = getDbConnection();
    $sql = "UPDATE areas SET area_name = ?, area_desc = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssi", $area_name, $area_desc, $id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    mysqli_close($conn);
    return false;
}

function deleteArea($id) {
    $conn = getDbConnection();
    $sql = "DELETE FROM areas WHERE id = ?";
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
?>


