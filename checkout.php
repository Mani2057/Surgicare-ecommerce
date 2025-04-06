<?php
session_start();
include 'config.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login first!'); window.location='login.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch cart items from the database
$query = "SELECT cart.id, products.id AS product_id, products.name, products.price, cart.quantity 
          FROM cart 
          JOIN products ON cart.product_id = products.id 
          WHERE cart.user_id='$user_id'";

$result = $conn->query($query);

// Check if cart is empty
if ($result->num_rows == 0) {
    echo "<script>alert('Your cart is empty!'); window.location='view_products.php';</script>";
    exit();
}

// Calculate total price
$total_price = 0;
$cart_items = [];
while ($row = $result->fetch_assoc()) {
    $subtotal = $row['price'] * $row['quantity'];
    $total_price += $subtotal;

    $cart_items[] = [
        'id' => $row['id'], 
        'product_id' => $row['product_id'], // Store product_id separately
        'name' => $row['name'],
        'price' => $row['price'],
        'quantity' => $row['quantity']
    ];
}

// Handle Order Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['address'])) {
        die("All fields are required!");
    }

    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $address = $conn->real_escape_string($_POST['address']);

    // Store order in database
    $order_query = "INSERT INTO orders (user_id, name, email, address, total_price) 
                    VALUES ('$user_id', '$name', '$email', '$address', '$total_price')";

    if ($conn->query($order_query) === TRUE) {
        $order_id = $conn->insert_id;

        foreach ($cart_items as $item) {
            $product_id = $item['product_id'];
            $quantity = $item['quantity'];

            // Debugging: Check if product exists in `products` table before inserting
            $check_product = $conn->query("SELECT id FROM products WHERE id = '$product_id'");
            if ($check_product->num_rows == 0) {
                die("Error: Product ID $product_id does not exist in the products table.");
            }

            // Insert order items
            $insert_query = "INSERT INTO order_items (order_id, product_id, quantity) 
                             VALUES ('$order_id', '$product_id', '$quantity')";
            
            if (!$conn->query($insert_query)) {
                die("Error inserting order items: " . $conn->error);
            }
        }

        // Clear the cart
        $conn->query("DELETE FROM cart WHERE user_id='$user_id'");

        // Store order details in session
        $_SESSION['order_id'] = $order_id;
        $_SESSION['total_price'] = $total_price;

        // Redirect to payment page
        echo "<script>
        alert('Waiting for Payment!! Proceed to Payment');
        window.location='payment.php';
        </script>";
    } else {
        die("Error placing order: " . $conn->error);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- External Custom CSS -->
    <link rel="stylesheet" href="checkout.css">
</head>
<body>

    <div class="container">
        <div class="checkout-container">
            <h2>Checkout</h2>
            
            <form action="" method="POST">
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter your full name" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Shipping Address</label>
                    <input type="text" name="address" class="form-control" placeholder="Enter your shipping address" required>
                </div>

                <div class="mb-3">
                    <p class="text-center"><strong>Total Price: ₹<?php echo number_format($total_price, 2); ?></strong></p>
                </div>

                <button type="submit" class="btn btn-custom">Place Order</button>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
