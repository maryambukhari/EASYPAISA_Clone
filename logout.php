<?php
// logout.php - Logout Page

session_start();
session_destroy();
echo "<script>location.href = 'index.php';</script>";
?>
