<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "staynest");
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Get house ID from URL
$house_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql = "SELECT * FROM houses WHERE house_id = $house_id";
$result = $conn->query($sql);
$house = $result->fetch_assoc();

if (!$house) {
    die("House not found!");
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StayNest - House Details</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>StayNest - House Details</h1>
    </header>

    <main>
        <h2><?php echo $house['title']; ?></h2>
        <img src="<?php echo explode(',', $house['images'])[0]; ?>" alt="House Image">
        <p><strong>Location:</strong> <?php echo $house['location']; ?></p>
        <p><strong>Price:</strong> ₹<?php echo number_format($house['price'], 2); ?>/month</p>
        <p><strong>Type:</strong> <?php echo ucfirst($house['house_type']); ?></p>
        <p><strong>Landmark:</strong> <?php echo $house['landmark']; ?> (<?php echo $house['distance_from_landmark']; ?> km away)</p>
    </main>
</body>
</html>
