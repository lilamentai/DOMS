<?php
session_start();
include 'koneksi.php';

// Debug: cek koneksi database
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = md5($_POST['password']); // harus sama seperti di daftar

    // Debug: tampilkan query untuk debugging (hapus ini setelah berhasil)
    $debug_query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    echo "<!-- Debug Query: $debug_query -->";

    $query = mysqli_query($koneksi, $debug_query);

    if (!$query) {
        die("Query error: " . mysqli_error($koneksi));
    }

    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);

        // Debug: tampilkan data user (hapus ini setelah berhasil)
        echo "<!-- Debug User Data: " . print_r($data, true) . " -->";

        // simpan session data yang diperlukan
        $_SESSION['user_id'] = $data['id'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['nama'] = $data['nama'];
        $_SESSION['role'] = $data['role'] ?? 'user'; // default ke 'user' jika tidak ada field role

        // Debug: cek session (hapus ini setelah berhasil)
        echo "<!-- Debug Session: " . print_r($_SESSION, true) . " -->";

        header("Location: dashboard.php");
        exit();
    } else {
        echo "<script>alert('Username atau password salah!');</script>";
        // Debug: tampilkan info login yang dicoba (hapus ini setelah berhasil)
        echo "<!-- Debug: Username=$username, Password Hash=" . $password . " -->";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h2 align="center">Login</h2>
    <form method="POST" align="center">
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Login</button>
        <a href="daftar.php">Daftar</a>
    </form>
</body>
</html>
