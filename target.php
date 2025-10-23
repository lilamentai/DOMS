<?php
session_start();
include 'koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// === Pastikan tabel targets ada ===
$cekTable = mysqli_query($koneksi, "SHOW TABLES LIKE 'targets'");
if (mysqli_num_rows($cekTable) == 0) {
    mysqli_query($koneksi, "CREATE TABLE targets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(255) NOT NULL,
        tujuan_donasi VARCHAR(255) NOT NULL,
        target_amount INT DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
}

// === Ambil daftar kategori donasi user (langsung dari donations) ===
$kategoriList = [];
$tujuan_query = mysqli_query($koneksi, "
    SELECT DISTINCT UPPER(tujuan_donasi) AS tujuan_donasi 
    FROM donations 
    WHERE username='$username' 
      AND tujuan_donasi IS NOT NULL 
      AND tujuan_donasi <> '' 
      AND UPPER(tujuan_donasi) NOT IN ('PALSTINA','UMUM') 
      AND tujuan_donasi NOT LIKE '%_%'
    ORDER BY tujuan_donasi
");
while ($row = mysqli_fetch_assoc($tujuan_query)) {
    $kategoriList[$row['tujuan_donasi']] = $row['tujuan_donasi'];
}

// Tambahkan kategori default jika belum ada
$defaults = ['BANTUAN BENCANA', 'BANTUAN PENDIDIKAN', 'BANTUAN KESEHATAN', 'BANTUAN SOSIAL', 'ANAK YATIM', 'PANTI ASUHAN', 'PEMBANGUNAN MASJID'];
foreach ($defaults as $d) {
    if (!in_array($d, $kategoriList)) {
        $kategoriList[$d] = $d;
    }
}

// === Ambil target donasi dari tabel targets ===
$targetData = [];
$tq = mysqli_query($koneksi, "
    SELECT tujuan_donasi, MAX(target_amount) AS target
    FROM targets
    WHERE username='$username'
    GROUP BY tujuan_donasi
");
while ($row = mysqli_fetch_assoc($tq)) {
    $targetData[$row['tujuan_donasi']] = $row['target'];
}

// === Simpan target baru ===
if (isset($_POST['simpan_target'])) {
    foreach ($_POST['target'] as $kategori => $nilai) {
        $nilai = (int)$nilai;
        $kategori = mysqli_real_escape_string($koneksi, $kategori);

        $cek = mysqli_query($koneksi, "SELECT * FROM targets WHERE username='$username' AND tujuan_donasi='$kategori' LIMIT 1");
        if (mysqli_num_rows($cek) > 0) {
            mysqli_query($koneksi, "
                UPDATE targets SET target_amount = $nilai 
                WHERE username='$username' AND tujuan_donasi='$kategori'
            ");
        } else {
            mysqli_query($koneksi, "
                INSERT INTO targets (username, tujuan_donasi, target_amount)
                VALUES ('$username', '$kategori', $nilai)
            ");
        }
    }
    header("Location: target.php");
    exit;
}

$donasiData = [];
$dq = mysqli_query($koneksi, "
    SELECT tujuan_donasi, SUM(amount) AS total 
    FROM donations 
    WHERE username='$username' 
      AND (status='confirmed' OR status='pending')
    GROUP BY tujuan_donasi
");
while ($row = mysqli_fetch_assoc($dq)) {
    $donasiData[$row['tujuan_donasi']] = $row['total'];
}

// Default 0 jika belum ada donasi atau target
foreach ($kategoriList as $key => $kategori) {
    if (!isset($donasiData[$key])) $donasiData[$key] = 0;
    if (!isset($targetData[$key])) $targetData[$key] = 0;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Target Donasi</title>
<style>
*{box-sizing:border-box;margin:0;padding:0;font-family:'Segoe UI',sans-serif;}
body{background:linear-gradient(135deg,#f8f1f1 0%,#ffe6f0 100%);min-height:100vh;color:#3a3a3a;}
.navbar{position:fixed;top:0;left:0;right:0;background:#fff;display:flex;justify-content:space-between;align-items:center;padding:14px 30px;box-shadow:0 4px 15px rgba(226,117,159,0.15);z-index:1000;}
.navbar .brand{font-size:22px;font-weight:700;color:#e2759f;}
.menu-center{flex:1;display:flex;justify-content:center;gap:25px;}
.menu-center a{color:#444;text-decoration:none;font-weight:500;padding:8px 15px;border-radius:10px;}
.menu-center a.active{color:#e2759f;}
.logout a{color:#fff;background:linear-gradient(45deg,#e2759f,#c25a8a);padding:10px 22px;border-radius:25px;text-decoration:none;font-size:14px;font-weight:600;}
.container{max-width:1200px;margin:120px auto 30px;padding:20px;}
.card{background:#fff;border-radius:10px;padding:20px;margin-bottom:20px;box-shadow:0 2px 6px rgba(0,0,0,0.1);}
.progress{height:25px;border-radius:10px;overflow:hidden;background:#eee;}
.progress-bar{height:100%;color:white;font-weight:bold;text-align:center;line-height:25px;}
.red{background:#e74c3c;}
.yellow{background:#f1c40f;color:#333;}
.green{background:#2ecc71;}
form{margin-top:30px;background:#fff;padding:20px;border-radius:10px;box-shadow:0 2px 6px rgba(0,0,0,0.1);}
input[type=number]{padding:5px;width:100%;border:1px solid #ccc;border-radius:5px;}
button{margin-top:10px;padding:10px 20px;background:#e2759f;color:#fff;border:none;border-radius:8px;cursor:pointer;}
button:hover{background:#c25a8a;}
</style>
</head>
<body>

<div class="navbar">
    <div class="brand"><b>DOMS</b></div>
    <div class="menu-center">
        <a href="dashboard.php">Home</a>
        <a href="tambah_donasi.php">Tambah Donasi</a>
        <a href="data_donasi.php">Data Donasi</a>
        <a href="target.php" class="active">Target</a>
        <a href="tentang.php">Tentang Kami</a>
    </div>
    <div class="logout"><a href="logout.php" onclick="return confirm('Yakin ingin logout?')">Logout</a></div>
</div>

<div class="container">
    <div style="text-align:center;margin-bottom:30px;">
        <h1 style="background:#fff;color:#e2759f;padding:18px 40px;border-radius:18px;font-size:2.2rem;font-weight:700;box-shadow:0 2px 6px rgba(0,0,0,0.08);display:inline-block;">
            Target Donasi Kamu
        </h1>
    </div>

    <?php foreach ($kategoriList as $key => $kategori):
        $target = $targetData[$key];
        $donasi = $donasiData[$key];
        $persen = ($target > 0) ? min(100, round(($donasi / $target) * 100)) : 0;
        if ($persen < 40) $warna = "red";
        elseif ($persen < 70) $warna = "yellow";
        else $warna = "green";
    ?>
    <div class="card">
        <h2><?= htmlspecialchars($kategori) ?></h2>
        <div class="progress">
            <div class="progress-bar <?= $warna ?>" style="width:<?= $persen ?>%;"><?= $persen ?>%</div>
        </div>
        <p>Donasi Terkumpul: Rp<?= number_format($donasi,0,',','.') ?> dari Target Rp<?= number_format($target,0,',','.') ?></p>
    </div>
    <?php endforeach; ?>

    <form method="POST">
        <h2>Atur Target Donasi</h2>
        <?php foreach ($kategoriList as $key => $kategori): ?>
            <label><?= htmlspecialchars($kategori) ?>:</label>
            <input type="number" name="target[<?= $key ?>]" value="<?= $targetData[$key] ?>" min="0">
        <?php endforeach; ?>
        <button type="submit" name="simpan_target">Simpan Target</button>
    </form>
</div>
</body>
</html>
