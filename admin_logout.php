<?php
session_start();
session_destroy();
echo "<script>alert('Admin Logged Out Successfully!'); window.location='admin_login.php';</script>";
?>
