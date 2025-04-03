<?php
session_start();
include 'dbowner_connect.php'; // Ensure database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['owner_email_signin']);
    $password = trim($_POST['owner_password_signin']);

    // Check if the email exists in the database
    $stmt = $conn->prepare("SELECT id, password FROM owners WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        
        // Verify the hashed password
        if (password_verify($password, $row['password'])) { // FIXED COLUMN NAME
            $_SESSION['owner_id'] = $row['id']; // FIXED SESSION VARIABLE
            $_SESSION['owner_email'] = $email;
            
            // Redirect to Add House Page
            header("Location: add_house.html");
            exit();
        } else {
            echo "<script>alert('Incorrect Password!'); window.location.href='owners_signin.html';</script>";
        }
    } else {
        echo "<script>alert('Email not found!'); window.location.href='owners_signin.html';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
