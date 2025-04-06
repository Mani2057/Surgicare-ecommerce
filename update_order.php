<?php
session_start();
include 'db.php'; // Include database connection

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Check if order ID and status are set
if (isset($_POST['order_id']) && isset($_POST['status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    // Update order status in database
    $sql = "UPDATE orders SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $order_id);

    if ($stmt->execute()) {
        header("Location: admin_orders.php?success=Order updated");
    } else {
        header("Location: admin_orders.php?error=Failed to update");
    }
} else {
    header("Location: admin_orders.php");
}
exit();
