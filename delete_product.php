<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include 'config.php';

// Log errors to a file
function logError($error) {
    file_put_contents('error_log.txt', $error . PHP_EOL, FILE_APPEND);
}

if (!isset($_SESSION['admin'])) {
    die("Access Denied. Please login first.");
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    if ($id <= 0) {
        die("Invalid Product ID.");
    }

    // Fetch image path
    $query = "SELECT image FROM products WHERE id = ?";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        logError("SQL Prepare Error: " . $conn->error);
        die("Database Error.");
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($image);
    $stmt->fetch();
    $stmt->close();

    // STEP 1: Delete related cart entries
    $deleteCartQuery = "DELETE FROM cart WHERE product_id = ?";
    $cartStmt = $conn->prepare($deleteCartQuery);
    $cartStmt->bind_param("i", $id);
    if (!$cartStmt->execute()) {
        logError("Failed to delete from cart: " . $cartStmt->error);
        die("Error deleting cart entries.");
    }
    $cartStmt->close();

    // STEP 2: Delete product
    $deleteQuery = "DELETE FROM products WHERE id = ?";
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->bind_param("i", $id);

    if ($deleteStmt->execute()) {
        if (!empty($image) && file_exists($image)) {
            unlink($image); // Delete image file
        }
        echo "<script>alert('Product deleted successfully!'); window.location='view_products.php';</script>";
    } else {
        logError("Delete Query Failed: " . $deleteStmt->error);
        die("Error deleting product.");
    }

    $deleteStmt->close();
} else {
    die("Invalid Request.");
}

$conn->close();
?>
