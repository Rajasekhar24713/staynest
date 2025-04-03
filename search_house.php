<?php
// Database Connection
$conn = new mysqli("localhost", "root", "", "staynest");
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Get search parameters
$search_landmark = isset($_GET['landmark']) ? trim($_GET['landmark']) : "";
$max_distance = isset($_GET['distance']) ? floatval($_GET['distance']) : 0;

// Use prepared statements to prevent SQL injection
$sql = "SELECT id, owner_id, title, location, price, house_type, landmark, distance_from_landmark, phone, images, created_at 
        FROM house WHERE 1=1";
$params = [];
$types = "";

// Search by landmark using partial match (LIKE)
if (!empty($search_landmark)) {
    $sql .= " AND LOWER(landmark) LIKE LOWER(CONCAT('%', ?, '%'))";
    $params[] = $search_landmark;
    $types .= "s";
}

// Search by distance range
if ($max_distance > 0) {
    $sql .= " AND distance_from_landmark IS NOT NULL AND distance_from_landmark <= ?";
    $params[] = $max_distance;
    $types .= "d";
}

$sql .= " ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$houses = [];
while ($row = $result->fetch_assoc()) {
    $houses[] = $row;
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($houses, JSON_PRETTY_PRINT);

$stmt->close();
$conn->close();
?>
