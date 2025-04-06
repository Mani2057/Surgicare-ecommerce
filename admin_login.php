<?php
session_start();
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // MD5 Encryption

    $query = "SELECT * FROM admin WHERE username='$username' AND password='$password'";
    $result = $conn->query($query);

    if ($result->num_rows == 1) {
        $_SESSION['admin'] = $username;
        echo "<script>alert('Login successful!'); window.location='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Invalid credentials!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="admin_login.css"> <!-- Linking the external CSS -->
</head>
<body>
    <div class="container">
        <!-- Left Side (Welcome Message) -->
        <div class="left-panel">
            <div class="content">
                <h1>Welcome, <span>Admin!</span></h1>
                <p>Access your dashboard to manage users, settings, and system configurations.</p>
            </div>
        </div>

        <!-- Right Side (Login Form) -->
        <div class="right-panel">
            <div class="login-box">
                <h2>Admin Login</h2>
                <form action="" method="POST">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit">Login</button>
                </form>
                <p class="forgot-password"><a href="#">Forgot password?</a></p>
            </div>
        </div>
    </div>
</body>
</html>
