<?php
session_start();
include 'config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Get order ID from the URL
if (!isset($_GET['order_id']) || empty($_GET['order_id'])) {
    header("Location: orders.php");
    exit();
}

$order_id = intval($_GET['order_id']);
$user_id = $_SESSION['user_id'];

// Fetch order details
$sql = "SELECT id, total_price, status FROM orders WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Query preparation failed: " . $conn->error);
}

$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    echo "Order not found.";
    exit();
}

// Fetch ordered products with product details
$product_sql = "SELECT p.name, p.image, oi.quantity 
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?";
$product_stmt = $conn->prepare($product_sql);
if (!$product_stmt) {
    die("Query preparation failed: " . $conn->error);
}

$product_stmt->bind_param("i", $order_id);
$product_stmt->execute();
$product_result = $product_stmt->get_result();
$products = $product_result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - SurgiCare</title>
    <link rel="stylesheet" href="order_details.css">
</head>
<body>
    <h2>Order Details</h2>
    <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order['id']); ?></p>
    <p><strong>Total Price:</strong> $<?php echo htmlspecialchars($order['total_price']); ?></p>
    <p><strong>Status:</strong> <?php echo htmlspecialchars($order['status']); ?></p>

    <h3>Products</h3>
    <?php if (!empty($products)) { ?>
        <table border="1">
            <tr>
                <th>Product Image</th>
                <th>Product Name</th>
                <th>Quantity</th>
            </tr>
            <?php foreach ($products as $product) { 
                $imageFile = htmlspecialchars($product['image']);

                // Ensure "uploads/" is not duplicated
                $imagePath = (strpos($imageFile, "uploads/") === 0) ? $imageFile : "uploads/" . $imageFile;

                // Debugging output
                echo "<!-- Debug: Raw Image Filename → " . $imageFile . " -->";
                echo "<!-- Debug: Final Image Path → " . $imagePath . " -->";

                // Check if the image file exists
                if (!file_exists($imagePath) || empty($imageFile)) {
                    $imagePath = "uploads/default.png"; // Fallback image
                    echo "<!-- Debug: Image not found, using default.png -->";
                }
            ?>
                <tr>
                    <td>
                        <img src="<?php echo $imagePath; ?>" alt="Product Image" width="50">
                    </td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['quantity']); ?></td>
                </tr>
            <?php } ?>
        </table>
    <?php } else { ?>
        <p>No products found for this order.</p>
    <?php } ?>

    <a href="orders.php">Back to Orders</a>
</body>
</html>
