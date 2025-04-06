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
    <!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

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
  <div class="container py-5">
    <div class="row align-items-center">
      <!-- Left Side: Text + Form -->
      <div class="col-md-6 mb-4 mb-md-0">
        <h1 class="mb-4">
          Sana Commerce helps the<br>medical and healthcare<br>industry
        </h1>
        <p class="mb-3">
          Take care of your online presence with an e-commerce solution that knows the ins and outs of your industry.
        </p>
        <p>Prefer to speak directly to an expert?</p>
        <a href="#" class="btn btn-warning mt-2">Request a demo →</a>

        <!-- Search Form -->
        <form action="index.php" method="GET" class="mt-4">
          <div class="mb-3">
            <input type="text" name="search" class="form-control" placeholder="Search products..." value="<?php echo htmlspecialchars($search); ?>">
          </div>
          <div class="mb-3">
            <select name="category" class="form-select">
              <option value="">All Categories</option>
              <?php while ($cat = $category_result->fetch_assoc()): ?>
                <option value="<?php echo htmlspecialchars($cat['category']); ?>" <?php echo ($category_filter == $cat['category']) ? 'selected' : ''; ?>>
                  <?php echo htmlspecialchars($cat['category']); ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>
          <button type="submit" class="btn btn-primary">Search</button>
        </form>
      </div>

      <!-- Right Side: Image -->
      <div class="col-md-6 text-center">
        <img src="https://www.sana-commerce.com/wp-content/uploads/Single-ind-Top_imgs-770x640_0003_medical-supplies-1340x892-c-default.webp" alt="Pharmacist" class="img-fluid rounded shadow">
      </div>
    </div>
  </div>
</div>


<!-- Image Slider Section -->
<div class="container my-5">
  <div class="swiper mySwiper">
    <div class="swiper-wrapper">
      <!-- Slide 1 -->
      <div class="swiper-slide">
        <img src="https://cdn01.pharmeasy.in/dam/banner/banner/ab23617cc1b-SAVE24__HP.jpg" alt="Full Body Checkup" class="img-fluid rounded shadow" />
      </div>
      <!-- Slide 2 -->
      <div class="swiper-slide">
        <img src="https://cdn01.pharmeasy.in/dam/banner/banner/55b39c6f18c-634x274.jpg" alt="Enterogermina" class="img-fluid rounded shadow" />
      </div>
      <!-- Slide 3 -->
      <div class="swiper-slide">
        <img src="https://cdn01.pharmeasy.in/dam/banner/banner/7313463a2e2-Enterogerminaprimarynew.jpg" alt="Prohance" class="img-fluid rounded shadow" />
      </div>

      <!-- Slide 4 -->
      <div class="swiper-slide">
        <img src="https://cdn01.pharmeasy.in/dam/banner/banner/7fae3cdeb52-PROHANCE_Ban_KB.jpg" alt="Prohance" class="img-fluid rounded shadow" />
      </div>
    </div>

    <!-- Navigation buttons -->
    <div class="swiper-button-next"></div>
    <div class="swiper-button-prev"></div>

    <!-- Pagination dots -->
    <div class="swiper-pagination"></div>
  </div>
</div>

<script>
  var swiper = new Swiper(".mySwiper", {
    slidesPerView: 1.2,
    spaceBetween: 20,
    loop: true,
    autoplay: {
      delay: 3000, // 3 seconds between slides
      disableOnInteraction: false, // keeps autoplay running after user interaction
    },
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    breakpoints: {
      768: {
        slidesPerView: 2.5
      },
      1024: {
        slidesPerView: 3
      }
    }
  });
</script>


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
