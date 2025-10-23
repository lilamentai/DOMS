<?php
session_start();
include 'koneksi.php';

// pastikan user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'];
mysqli_query($koneksi, "DELETE FROM donations WHERE id='$id'");

header("Location: dashboard_admin.php");
exit();
