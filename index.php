<?php
session_start();
include 'config.php';

// Fetch categories
$category_query = "SELECT DISTINCT category FROM products";
$category_result = $conn->query($category_query);

$search = isset($_GET['search']) ? $_GET['search'] : "";
$category_filter = isset($_GET['category']) ? $_GET['category'] : "";

// Fetch products (excluding out-of-stock)
$sql = "SELECT * FROM products WHERE stock > 0 AND name LIKE ?";
$params = ["%$search%"];
$types = "s";

if ($category_filter) {
    $sql .= " AND category = ?";
    $params[] = $category_filter;
    $types .= "s";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SurgiCare - Home</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>

<!-- Navbar -->
<nav>
    <div class="logo">
        <span class="logo-half">Surgi</span><span class="logo-rest">Care</span>
    </div>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="cart.php">Cart (<span id="cart-count"><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : '0'; ?></span>)</a></li>
        <?php if (isset($_SESSION['user_id'])): ?>
            <li><a href="orders.php">My Orders</a></li>
            <li><a href="logout.php">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
        <?php endif; ?>
    </ul>
</nav>

<!-- Toast Notification -->
<div class="position-fixed bottom-0 end-0 p-3">
    <div id="toast-success" class="toast text-bg-success border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">✅ Product added to cart successfully!</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<!-- Hero Section -->
<div class="hero-section">
    <div class="overlay">
        <form action="index.php" method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
            <select name="category">
                <option value="">All Categories</option>
                <?php while ($cat = $category_result->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($cat['category']); ?>" <?php echo ($category_filter == $cat['category']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['category']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <button type="submit">Search</button>
        </form>
    </div>
</div>

<!-- Product List -->
<div class="product-container">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="product">
            <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Product Image">
            <h3><?php echo htmlspecialchars($row['name']); ?></h3>
            <p>Category: <?php echo htmlspecialchars($row['category']); ?></p>
            <p>Price: ₹<?php echo number_format($row['price'], 2); ?></p>
            <p>Stock: <?php echo $row['stock']; ?> left</p>

            <button class="add-to-cart-btn" data-product-id="<?php echo $row['id']; ?>" <?php echo ($row['stock'] <= 0) ? 'disabled' : ''; ?>>
                Add to Cart
            </button>
        </div>
    <?php endwhile; ?>
</div>

<!-- Footer Section -->
<footer class="footer bg-dark text-light text-center py-3 mt-5">
    <div class="container">
        <div class="row">
            <!-- Company Info -->
            <div class="col-md-4">
                <h5>About SurgiCare</h5>
                <p>Providing high-quality surgical and healthcare products. Your health is our priority.</p>
            </div>

            <!-- Quick Links -->
            <div class="col-md-4">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="index.php" class="text-light">Home</a></li>
                    <li><a href="cart.php" class="text-light">Cart</a></li>
                    <li><a href="orders.php" class="text-light">My Orders</a></li>
                    <li><a href="contact.php" class="text-light">Contact Us</a></li>
                </ul>
            </div>

            <!-- Contact Information -->
            <div class="col-md-4">
                <h5>Contact Us</h5>
                <p>Email: support@surgicare.com</p>
                <p>Phone: +91 98765 43210</p>
                <p>Address: 123, Healthcare Street, New Delhi, India</p>
            </div>
        </div>

        <!-- Copyright Notice -->
        <div class="mt-3">
            <p>&copy; 2025 SurgiCare. All Rights Reserved.</p>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const cartCountElement = document.getElementById("cart-count");
    const toastElement = document.getElementById("toast-success");
    const toast = new bootstrap.Toast(toastElement);

    document.querySelectorAll(".add-to-cart-btn").forEach(button => {
        button.addEventListener("click", function () {
            let productId = this.dataset.productId;

            fetch("add_to_cart.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "product_id=" + encodeURIComponent(productId)
            })
            .then(response => response.json())
            .then(data => {
                if (data.cart_count !== undefined) {
                    cartCountElement.textContent = data.cart_count;
                    toast.show();
                } else {
                    alert("Failed to add product to cart.");
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("An error occurred while adding to cart.");
            });
        });
    });
});
</script>

</body>
</html>
