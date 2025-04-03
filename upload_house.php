<?php
session_start(); // Start session to access owner_id

// Database connection
$conn = new mysqli("localhost", "root", "", "staynest");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure owner is logged in
if (!isset($_SESSION['owner_id'])) {
    die("Error: Owner not logged in.");
}

$owner_id = $_SESSION['owner_id']; // Get logged-in owner ID

// Validate form fields
$required_fields = ['title', 'location', 'price', 'house_type', 'landmark', 'distance_from_landmark', 'phone'];

foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
        die("Error: Missing or empty required form field: $field");
    }
}

// Get form data
$title = $conn->real_escape_string($_POST['title']);
$location = $conn->real_escape_string($_POST['location']);
$price = floatval($_POST['price']);
$house_type = $conn->real_escape_string($_POST['house_type']);
$landmark = $conn->real_escape_string($_POST['landmark']);
$distance_from_landmark = floatval($_POST['distance_from_landmark']);
$phone = preg_replace('/\D/', '', $_POST['phone']); // Remove non-numeric characters

// Validate phone number (should be 10 digits)
if (strlen($phone) !== 10) {
    die("Error: Invalid phone number. Must be 10 digits.");
}

// Ensure 'uploads/' directory exists
$upload_dir = "uploads/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true); // Create directory with full permissions
}

// Handle image uploads
$image_paths = [];
$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

if (!empty($_FILES['images']['name'][0])) {
    foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
        $image_name = basename($_FILES['images']['name'][$key]);
        $target_file = $upload_dir . $image_name;
        $file_extension = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validate file type
        if (!in_array($file_extension, $allowed_extensions)) {
            echo "Error: Invalid file type for $image_name (Allowed: JPG, JPEG, PNG, GIF)<br>";
            continue;
        }

        // Move file and check if successful
        if (move_uploaded_file($tmp_name, $target_file)) {
            $image_paths[] = $target_file;
        } else {
            echo "Error: Failed to upload " . $image_name . "<br>";
        }
    }
}

// Convert image array to comma-separated string
$image_paths_str = implode(",", $image_paths);

// Insert into database using prepared statement
$sql = "INSERT INTO house (owner_id, title, location, price, house_type, landmark, distance_from_landmark, phone, images) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("issdssdss", $owner_id, $title, $location, $price, $house_type, $landmark, $distance_from_landmark, $phone, $image_paths_str);

if ($stmt->execute()) {
    echo "House listed successfully!";
} else {
    echo "Error: " . $stmt->error;
}

// Close connections
$stmt->close();
$conn->close();
?>
