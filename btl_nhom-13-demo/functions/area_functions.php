<?php

require_once 'db_connection.php';
require_once __DIR__ . '/vehicle_functions.php';

// Area functions (uses `areas` table)

function getAllAreas() {
    $conn = getDbConnection();
    $sql = "SELECT id, area_name, area_desc, COALESCE(current_vehicles, 0) AS current_vehicles FROM areas ORDER BY id";
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

// Get current vehicle count for an area
function getCurrentVehicleCount($area_id) {
    $conn = getDbConnection();
    $sql = "SELECT COUNT(*) AS count FROM vehicles WHERE area_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    $count = 0;
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $area_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $count = (int)$row['count'];
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
    return $count;
}

function addArea($area_name, $area_desc, $current_vehicles = 0) {
    $conn = getDbConnection();
    $sql = "INSERT INTO areas (area_name, area_desc, current_vehicles) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssi", $area_name, $area_desc, $current_vehicles);
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
    $sql = "SELECT id, area_name, area_desc, COALESCE(current_vehicles, 0) AS current_vehicles FROM areas WHERE id = ? LIMIT 1";
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

function updateArea($id, $area_name, $area_desc, $current_vehicles = null) {
    $conn = getDbConnection();
    
    // If current_vehicles is provided, update it; otherwise keep the existing value
    if ($current_vehicles !== null) {
        $sql = "UPDATE areas SET area_name = ?, area_desc = ?, current_vehicles = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssii", $area_name, $area_desc, $current_vehicles, $id);
            $success = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $success;
        }
    } else {
        $sql = "UPDATE areas SET area_name = ?, area_desc = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssi", $area_name, $area_desc, $id);
            $success = mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $success;
        }
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


