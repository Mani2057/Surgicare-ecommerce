<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user'])) {
    echo "<script>alert('Please login first!'); window.location='login.php';</script>";
    exit();
}

$cart_id = $_GET['id'];
$conn->query("DELETE FROM cart WHERE id='$cart_id'");

echo "<script>alert('Item removed from cart!'); window.location='cart.php';</script>";
?>
