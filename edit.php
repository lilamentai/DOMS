<?php
session_start();
include 'koneksi.php';

// Cek koneksi
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Pastikan sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// Cek admin
if ($username != 'kokwet') {
    header("Location: dashboard.php");
    exit;
}

// Ambil data rekening dari database
$rek_query = mysqli_query($koneksi, "SELECT nama_bank, rekening_number FROM donations");
$nomor_rekening = [];
while ($row = mysqli_fetch_assoc($rek_query)) {
    $nomor_rekening[$row['nama_bank']] = $row['rekening_number'];
}

// Default rekening
$default_rekening = [
    'BRI' => '102938475610',
    'BNI' => '987654321098',
    'BCA' => '456749854448',
    'Mandiri' => '123456789012'
];

// Pastikan semua bank ada di database
foreach ($default_rekening as $bank => $rek) {
    if (!isset($nomor_rekening[$bank])) {
        $nomor_rekening[$bank] = $rek;
        mysqli_query($koneksi, "INSERT INTO donations (nama_bank, rekening_number, donor_name, tujuan_donasi, created_at) VALUES ('$bank', '$rek', '', '', NOW())");
    }
}

// Pastikan BNI dan BRI selalu muncul
if (!isset($nomor_rekening['BNI'])) $nomor_rekening['BNI'] = '987654321098';
if (!isset($nomor_rekening['BRI'])) $nomor_rekening['BRI'] = '102938475610';

// Proses update rekening
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_rekening'])) {
    foreach ($nomor_rekening as $bank => $old_rek) {
        $new_rek = trim($_POST[$bank] ?? '');
        if ($new_rek !== '') {
            $check = mysqli_query($koneksi, "SELECT id FROM donations WHERE nama_bank='$bank'");
            if (mysqli_num_rows($check) > 0) {
                mysqli_query($koneksi, "UPDATE donations SET rekening_number='$new_rek' WHERE nama_bank='$bank'");
            } else {
                mysqli_query($koneksi, "INSERT INTO donations (nama_bank, rekening_number, donor_name, tujuan_donasi, created_at) VALUES ('$bank', '$new_rek', '', '', NOW())");
            }
        }
    }
    header("Location: edit.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Rekening Bank</title>
<style>
body {
    font-family: 'Segoe UI', Tahoma, sans-serif;
    background: linear-gradient(135deg,#f8f1f1 0%,#ffe6f0 100%);
    margin: 0;
    display: flex;
    min-height: 100vh;
    color: #3a3a3a;
}

/* Sidebar */
.sidebar {
    width: 220px;
    background: #fff;
    color: #e2759f;
    min-height: 100vh;
    padding: 20px 0.5px;
    box-shadow: 0 2px 10px rgba(226, 117, 159, 0.15);
    position: fixed;
    left: 0;
    top: 0;
}
.sidebar h2 {
    font-size: 20px;
    margin-bottom: 30px;
    text-align: center;
    color: #e2759f;
    background: linear-gradient(135deg, #ffe6f0, #f8f1f1);
    padding: 15px 10px;
    border-radius: 10px;
    margin: 0 10px 30px 10px;
    box-shadow: 0 2px 5px rgba(226, 117, 159, 0.2);
    text-shadow: 0 3px 5px rgba(226, 117, 159, 0.53);
}
.sidebar a {
    display: block;
    padding: 12px 15px;
    margin-bottom: 10px;
    text-decoration: none;
    color: #444;
    font-weight: 500;
    text-align: center;
    position: relative;
    transition: 0.3s;
}
.sidebar a::after {
    content: "";
    position: absolute;
    bottom: 0; left: 50%;
    transform: translateX(-50%);
    width: 0%;
    height: 2px;
    background: linear-gradient(90deg, #ff99aa, #e2759f);
    transition: 0.3s ease;
}
.sidebar a:hover {
    color: #e2759f;
}
.sidebar a:hover::after {
    width: 60%;
}
.sidebar a.active {
    color: #e2759f;
}
.sidebar a.active::after {
    width: 60%;
}

/* Konten */
.content {
    margin-left: 240px;
    padding: 30px;
    flex: 1;
}
.container {
    max-width: 700px;
    margin: auto;
    background: #fff;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.08);
}
h2 {
    text-align: center;
    color: #e2759f;
    margin-bottom: 25px;
}
.form-group {
    margin-bottom: 20px;
}
label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #e2759f;
}
input[type="text"] {
    width: 100%;
    padding: 12px;
    border: 2px solid #f8bbd9;
    border-radius: 10px;
    font-size: 15px;
}
input[type="text"]:focus {
    outline: none;
    border-color: #e2759f;
    box-shadow: 0 0 0 3px rgba(226,117,159,0.15);
}
.btn {
    background: linear-gradient(45deg, #e2759f, #c25a8a);
    color: white;
    padding: 12px 30px;
    border: none;
    border-radius: 25px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 8px 20px rgba(226, 117, 159, 0.3);
}
.btn:hover {
    transform: translateY(-2px);
    background: linear-gradient(45deg, #c25a8a, #a0446f);
    box-shadow: 0 12px 25px rgba(226,117,159,0.4);
}
</style>
</head>

<body>
<div class="sidebar">
    <h2>Selamat Datang, Admin</h2>
    <a href="admin.php">Dashboard</a>
    <a href="kelola_donasi.php">Kelola</a>
    <a href="laporan_donasi.php">Laporan</a>
    <a href="edit.php" class="active">Edit Rekening</a>
    <a href="logout.php" onclick="return confirm('Yakin ingin logout?');">Logout</a>
</div>

<div class="content">
    <div class="container">
        <h2>Edit Nomor Rekening Bank</h2>

        <form method="POST">
            <?php
            // tampilkan kolom edit BRI, BNI, BCA, Mandiri
            $urutan = ['BRI', 'BNI', 'BCA', 'Mandiri'];
            foreach ($urutan as $bank):
                $rek = htmlspecialchars($nomor_rekening[$bank] ?? '');
            ?>
            <div class="form-group">
                <label for="<?= $bank ?>"><?= $bank ?></label>
                <input type="text" id="<?= $bank ?>" name="<?= $bank ?>" value="<?= $rek ?>" required>
            </div>
            <?php endforeach; ?>
            
            <button type="submit" name="update_rekening" class="btn">Simpan Perubahan</button>
        </form>
    </div>
</div>
</body>
</html>
