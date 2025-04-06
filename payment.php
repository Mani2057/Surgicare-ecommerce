<?php
session_start();
include 'config.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login first!'); window.location='login.php';</script>";
    exit();
}

// Retrieve order details from session (Set in checkout.php)
if (!isset($_SESSION['order_id']) || !isset($_SESSION['total_price'])) {
    echo "<script>alert('No order found!'); window.location='cart.php';</script>";
    exit();
}

$order_id = $_SESSION['order_id'];
$total_price = $_SESSION['total_price'];

// Simulating a payment process
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo "<script>
        alert('Payment Successful! Your Order ID is $order_id');
        window.location='index.php';
    </script>";

    // Clear session after successful payment
    unset($_SESSION['order_id']);
    unset($_SESSION['total_price']);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
</head>
<body>
    <h2>Complete Payment</h2>
    <p>Total Amount to Pay: <strong><?php echo $total_price; ?></strong></p>
    <form method="POST">
        <button type="submit">Confirm Payment</button>
    </form>
</body>
</html>
