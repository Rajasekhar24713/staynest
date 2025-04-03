<?php
session_start();
if (!isset($_SESSION['owner_id'])) {
    echo "<script>alert('Please login first!'); window.location.href='owners_signin.html';</script>";
    exit();
}

// Database Connection
$conn = new mysqli("localhost", "root", "", "staynest");
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Get logged-in owner's ID
$owner_id = $_SESSION['owner_id']; // This should be set at login

// Fetch form data
$title = trim($_POST['house_title']);
$location = trim($_POST['house_location']);
$price = floatval($_POST['house_price']); // Ensure it's a number
$houseType = $_POST['house_type'];
$landmark = trim($_POST['house_landmark']);
$distance = floatval($_POST['distance_from_landmark']); // Store in KM

// File Upload Handling
$imagePaths = [];
$uploadDir = "uploads/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true); // Create directory if not exists
}

foreach ($_FILES['house_image']['tmp_name'] as $key => $tmpName) {
    $imageName = time() . "_" . basename($_FILES['house_image']['name'][$key]); // Unique file name
    $targetFile = $uploadDir . $imageName;

    if (move_uploaded_file($tmpName, $targetFile)) {
        $imagePaths[] = $targetFile;
    }
}

// Convert image paths to a comma-separated string
$imagePathsString = implode(",", $imagePaths);

// Insert data into the `houses` table using Prepared Statement
$sql = $conn->prepare("INSERT INTO houses (owner_id, title, location, price, house_type, landmark, distance_from_landmark, images) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$sql->bind_param("ississds", $owner_id, $title, $location, $price, $houseType, $landmark, $distance, $imagePathsString);

if ($sql->execute()) {
    echo "<script>alert('House added successfully!'); window.location.href='owners_dashboard.html';</script>";
} else {
    echo "<script>alert('Error adding house: " . $sql->error . "'); window.history.back();</script>";
}

// Close database connection
$sql->close();
$conn->close();
?>
