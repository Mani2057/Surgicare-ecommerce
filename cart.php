<?php
session_start();
include 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {  
    echo "<script>alert('Please login first!'); window.location='login.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id']; 
$query = "SELECT cart.id, products.name, products.price, cart.quantity 
          FROM cart 
          JOIN products ON cart.product_id = products.id 
          WHERE cart.user_id='$user_id'";

$result = $conn->query($query);

// Reset session cart
$_SESSION['cart'] = [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>

<div class="container">
    <div class="cart-container">
        <h2 class="text-center mb-4">Your Shopping Cart 🛒</h2>
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price (₹)</th>
                    <th>Quantity</th>
                    <th>Total (₹)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                while ($row = $result->fetch_assoc()) {
                    $subtotal = $row['price'] * $row['quantity'];
                    $total += $subtotal;

                    // Store cart data in session
                    $_SESSION['cart'][] = [
                        'id' => $row['id'],
                        'name' => $row['name'],
                        'price' => $row['price'],
                        'quantity' => $row['quantity']
                    ];

                    echo "<tr>
                        <td>{$row['name']}</td>
                        <td>₹{$row['price']}</td>
                        <td>{$row['quantity']}</td>
                        <td>₹$subtotal</td>
                        <td><a href='remove_from_cart.php?id={$row['id']}' class='btn btn-remove'>Remove</a></td>
                    </tr>";
                }
                ?>
                <tr class="table-warning">
                    <td colspan="3"><strong>Grand Total</strong></td>
                    <td><strong>₹<?php echo $total; ?></strong></td>
                    <td><a href="checkout.php" class="btn btn-checkout">Proceed to Checkout</a></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<a href="index.php">Back to Home</a>
</body>
</html>
