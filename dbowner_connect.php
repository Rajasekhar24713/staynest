<?php
$host = "localhost";
$username = "root"; // Change as per your DB user
$password = ""; // Change as per your DB password
$database = "staynest";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
?>
