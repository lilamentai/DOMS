<?php
session_start();

// hapus semua session
session_unset();
session_destroy();

// Set logout success message in session
$_SESSION['logout_success'] = "Anda sudah logout!";

// Redirect to login page
header("Location: login.php");
exit;
