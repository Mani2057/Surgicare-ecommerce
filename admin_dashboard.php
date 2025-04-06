<?php
session_start();
if (!isset($_SESSION['admin'])) {
    echo "<script>alert('Access Denied! Please login first.'); window.location='admin_login.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand">Admin Dashboard</a>
            <a href="admin_logout.php" class="btn btn-danger">Logout</a>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="text-center">Welcome, Admin</h2>
        <div class="row">
            <div class="col-md-4">
                <a href="add_product.php" class="btn btn-primary w-100">Add Product</a>
            </div>
            <div class="col-md-4">
                <a href="view_products.php" class="btn btn-warning w-100">View Products</a>
            </div>
            <div class="col-md-4">
                <a href="view_orders.php" class="btn btn-info w-100">View Orders</a>
            </div>
        </div>
    </div>

</body>
</html>
