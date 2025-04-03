<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "staynest");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure the 'owners' table exists with 'id' as PRIMARY KEY
$createTableQuery = "CREATE TABLE IF NOT EXISTS owners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
)";
$conn->query($createTableQuery);

// Get form data
$name = isset($_POST['owner_name']) ? trim($_POST['owner_name']) : '';
$email = isset($_POST['owner_email']) ? trim($_POST['owner_email']) : '';
$password = isset($_POST['owner_password']) ? $_POST['owner_password'] : '';

// Validate inputs
if (empty($name) || empty($email) || empty($password)) {
    echo "All fields are required.";
    exit();
}

// Hash password for security
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Use Prepared Statements to prevent SQL injection
$sql = $conn->prepare("INSERT INTO owners (name, email, password) VALUES (?, ?, ?)");
$sql->bind_param("sss", $name, $email, $hashedPassword);

if ($sql->execute()) {
    echo "success"; // JavaScript will detect this and redirect
} else {
    echo "Error: " . $sql->error;
}

$sql->close();
$conn->close();
?>
