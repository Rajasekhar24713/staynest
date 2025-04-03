<?php
session_start();
header('Content-Type: application/json'); // Ensure JSON response

// Check if the owner is logged in
if (!isset($_SESSION['owner_id'])) {
    echo json_encode(["error" => "Unauthorized access. Please log in."]);
    exit();
}

// Database Connection
$conn = new mysqli("localhost", "root", "", "staynest");
if ($conn->connect_error) {
    die(json_encode(["error" => "Database Connection Failed: " . $conn->connect_error]));
}

$owner_id = $_SESSION['owner_id']; // Get the logged-in owner ID

// Fetch all houses listed by this owner
$sql = "SELECT id, title, location, price, house_type, landmark, images FROM houses WHERE owner_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$result = $stmt->get_result();

$houses = [];
while ($row = $result->fetch_assoc()) {
    // Extract first image from the stored comma-separated list
    $images = explode(",", $row['images']);
    $row['image'] = count($images) > 0 ? $images[0] : "no-image.jpg"; // Default if no image
    unset($row['images']); // Remove full image list
    $houses[] = $row;
}

// Debugging: Output result count
if (empty($houses)) {
    echo json_encode(["error" => "No houses found for this owner."]);
} else {
    echo json_encode($houses);
}

// Close connection
$stmt->close();
$conn->close();
?>
