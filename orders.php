<?php
session_start();
include 'config.php'; // Ensure this file contains your database connection

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

// Check if the database connection exists
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Fetch orders from the database
$sql = "SELECT id, total_price, status FROM orders WHERE user_id = ? ORDER BY id DESC";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - SurgiCare</title>
    <link rel="stylesheet" href="orders.css">
</head>
<body>
    <h2>My Orders</h2>
    
    <?php if ($result->num_rows > 0) { ?>
        <table border="1">
            <tr>
                <th>Order ID</th>
                <th>Total Price</th>
                <th>Status</th>
                <th>Details</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td>$<?php echo htmlspecialchars($row['total_price']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td>
                        <a href="order_details.php?order_id=<?php echo urlencode($row['id']); ?>">View</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    <?php } else { ?>
        <p>No orders found.</p>
    <?php } ?>
    
    <a href="index.php">Back to Home</a>
</body>
</html>
