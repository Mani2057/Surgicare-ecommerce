<?php
session_start();
include 'config.php';

if (!isset($_SESSION['admin'])) {
    echo "<script>alert('Access Denied! Please login first.'); window.location='admin_login.php';</script>";
    exit();
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $query = "SELECT * FROM products WHERE id='$id'";
    $result = $conn->query($query);
    $product = $result->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $stock = $_POST['stock'];

    if ($_FILES["image"]["name"] != "") {
        $target_dir = "uploads/";
        $image = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image);
        $query = "UPDATE products SET name='$name', description='$description', price='$price', category='$category', image='$image', stock='$stock' WHERE id='$id'";
    } else {
        $query = "UPDATE products SET name='$name', description='$description', price='$price', category='$category', stock='$stock' WHERE id='$id'";
    }

    if ($conn->query($query) === TRUE) {
        echo "<script>alert('Product Updated Successfully!'); window.location='view_products.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
</head>
<body>
    <h2>Edit Product</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
        <input type="text" name="name" value="<?php echo $product['name']; ?>" required><br><br>
        <textarea name="description"><?php echo $product['description']; ?></textarea><br><br>
        <input type="number" name="price" value="<?php echo $product['price']; ?>" step="0.01" required><br><br>
        <input type="text" name="category" value="<?php echo $product['category']; ?>" required><br><br>
        <input type="number" name="stock" value="<?php echo $product['stock']; ?>" required><br><br>
        <input type="file" name="image"><br><br>
        <img src="<?php echo $product['image']; ?>" width="100"><br><br>
        <button type="submit">Update Product</button>
    </form>
</body>
</html>
