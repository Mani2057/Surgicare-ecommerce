<?php
include 'config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            echo "<script>alert('Login Successful!'); window.location='index.php';</script>";
        } else {
            echo "<script>alert('Incorrect password!');</script>";
        }
    } else {
        echo "<script>alert('User not found! Please register first.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SurgiCare</title>
    <link rel="stylesheet" href="login.css"> 
</head>
<body>
    <div class="container">
        <div class="left-panel">
            <h1>Hello SurgiCare! 👋</h1>
            <p>Skip repetitive tasks and focus on what matters. Automate your workflow with ease.</p>
        </div>
        <div class="right-panel">
            <h2>Welcome Back!</h2>
            <p>Don’t have an account? <a href="register.php">Create a new account</a></p>
            <form action="" method="POST">
                <input type="email" name="email" placeholder="Enter your email" required>
                <input type="password" name="password" placeholder="Enter your password" required>
                <button type="submit">Login Now</button>
            </form>
            <p class="or">or sign in with</p>
            <div class="social-buttons">
                <button class="google-btn">Login with Google</button>
            </div>
            <p><a href="forgot-password.php">Forgot Password?</a></p>
        </div>
    </div>
</body>
</html>
