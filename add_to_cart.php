<?php
session_start();
include 'config.php';

// Enable Error Reporting for Debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "Please login first!"]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Validate product ID
if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
    echo json_encode(["error" => "Invalid Product!"]);
    exit();
}

$product_id = intval($_POST['product_id']);

// Check if product exists and fetch stock info
$check_product = $conn->prepare("SELECT id, stock FROM products WHERE id=?");
$check_product->bind_param("i", $product_id);
$check_product->execute();
$product_result = $check_product->get_result();
$product = $product_result->fetch_assoc();

if (!$product) {
    echo json_encode(["error" => "Product not found!"]);
    exit();
}

if ($product['stock'] <= 0) {
    echo json_encode(["error" => "Out of stock!"]);
    exit();
}

// Check if product is already in cart
$query = "SELECT * FROM cart WHERE user_id=? AND product_id=?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Increase quantity in cart
    $update_query = "UPDATE cart SET quantity = quantity + 1 WHERE user_id=? AND product_id=?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("ii", $user_id, $product_id);
    $update_stmt->execute();
} else {
    // Insert new product into cart
    $insert_query = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)";
    $insert_stmt = $conn->prepare($insert_query);
    $insert_stmt->bind_param("ii", $user_id, $product_id);
    $insert_stmt->execute();
}

// Decrease stock
$newStock = $product['stock'] - 1;
$updateStockQuery = "UPDATE products SET stock = ? WHERE id = ?";
$updateStockStmt = $conn->prepare($updateStockQuery);
$updateStockStmt->bind_param("ii", $newStock, $product_id);
$updateStockStmt->execute();

// Update session cart count
$_SESSION['cart'][$product_id] = ($_SESSION['cart'][$product_id] ?? 0) + 1;

// Return updated cart count as JSON
echo json_encode(["cart_count" => count($_SESSION['cart'])]);
?>
