<?php
session_start();
if (!isset($_SESSION['owner_id'])) {
    echo json_encode(["error" => "Unauthorized action."]);
    exit();
}

$conn = new mysqli("localhost", "root", "", "staynest");
if ($conn->connect_error) {
    die(json_encode(["error" => "Database Connection Failed"]));
}

if (!isset($_GET['id'])) {
    echo json_encode(["error" => "No house ID provided."]);
    exit();
}

$house_id = intval($_GET['id']);
$owner_id = $_SESSION['owner_id']; // Ensure owner can delete only their houses

// Verify if the house belongs to the logged-in owner
$check_sql = "SELECT images FROM houses WHERE id = ? AND owner_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $house_id, $owner_id);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows === 0) {
    echo json_encode(["error" => "Unauthorized access."]);
    exit();
}

// Delete the house
$delete_sql = "DELETE FROM houses WHERE id = ? AND owner_id = ?";
$delete_stmt = $conn->prepare($delete_sql);
$delete_stmt->bind_param("ii", $house_id, $owner_id);

if ($delete_stmt->execute()) {
    echo json_encode(["message" => "House deleted successfully."]);
} else {
    echo json_encode(["error" => "Failed to delete house."]);
}

$delete_stmt->close();
$conn->close();
?>
