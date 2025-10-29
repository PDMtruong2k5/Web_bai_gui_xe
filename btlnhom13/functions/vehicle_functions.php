<?php
require_once 'db_connection.php';

function getAllVehicles() {
    $conn = getDbConnection();
    $sql = "SELECT id, vehicle_plate, vehicle_owner FROM vehicles ORDER BY id";
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

function addVehicle($vehicle_plate, $vehicle_owner) {
    $conn = getDbConnection();
    $sql = "INSERT INTO vehicles (vehicle_plate, vehicle_owner) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ss", $vehicle_plate, $vehicle_owner);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    mysqli_close($conn);
    return false;
}

function getVehicleById($id) {
    $conn = getDbConnection();
    $sql = "SELECT id, vehicle_plate, vehicle_owner FROM vehicles WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) > 0) {
            $vehicle = mysqli_fetch_assoc($result);
            mysqli_stmt_close($stmt);
            mysqli_close($conn);
            return $vehicle;
        }
        mysqli_stmt_close($stmt);
    }
    mysqli_close($conn);
    return null;
}

function updateVehicle($id, $vehicle_plate, $vehicle_owner) {
    $conn = getDbConnection();
    $sql = "UPDATE vehicles SET vehicle_plate = ?, vehicle_owner = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "ssi", $vehicle_plate, $vehicle_owner, $id);
        $success = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $success;
    }
    mysqli_close($conn);
    return false;
}

function deleteVehicle($id) {
    $conn = getDbConnection();
    $sql = "DELETE FROM vehicles WHERE id = ?";
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

function vehicleHasTransactions($vehicleId) {
    $conn = getDbConnection();
    $sql = "SELECT 1 FROM transactions WHERE vehicle_id = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $vehicleId);
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


