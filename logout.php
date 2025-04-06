<?php
session_start();
session_destroy(); // Destroy user session
echo "<script>alert('Logged out successfully!'); window.location='login.php';</script>";
?>
