<?php
session_start();
include 'koneksi.php';

// pastikan sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$nama = $_SESSION['nama'] ?? '';

// jika login sebagai admin (sesuai logika mu sebelumnya)
if ($username === 'kokwet' && isset($_SESSION['password']) && $_SESSION['password'] === 'admin') {
    $_SESSION['admin'] = $username;
    header("Location: dashboard_admin.php");
    exit();
}

// Map tujuan donasi
$map = [
    'palestina' => 'Palestina',
    'papua' => 'Papua',
    'anak_yatim' => 'Anak Yatim',
    'bencana_alam' => 'Bencana Alam',
    'pendidikan' => 'Pendidikan',
    'kesehatan' => 'Kesehatan',
    'panti_asuhan' => 'Panti Asuhan',
    'masjid' => 'Masjid',
    'umum' => 'Umum'
];

// Ambil SEMUA donasi (urut terbaru)
$query = "
    SELECT id, donor_name, amount, phone, created_at, 
           IF(status IS NULL OR status = '', 'Pending', status) AS status, 
           tujuan_donasi, is_anonim, bukti_transfer
    FROM donations 
    ORDER BY created_at DESC
";
$result = mysqli_query($koneksi, $query);

$donations = [];
while ($row = mysqli_fetch_assoc($result)) {
    $donations[] = $row;
}

// Ambil donatur terbaru (terserta apa pun statusnya) — last inserted donation
$latest_query = mysqli_query($koneksi, "
    SELECT donor_name, amount, created_at, is_anonim 
    FROM donations 
    ORDER BY created_at DESC LIMIT 1
");

$latest = null;
if ($latest_query && mysqli_num_rows($latest_query) > 0) {
    $latest_row = mysqli_fetch_assoc($latest_query);
    $donorNameForDisplay = $latest_row['donor_name'];
    if ($latest_row['is_anonim'] == 1 || strtolower($donorNameForDisplay) == 'anonymous' || strtolower($donorNameForDisplay) == 'donatur anonim') {
        $donorNameForDisplay = 'Nono Yaa';
    }
    $latest = [
        'donor_name' => $donorNameForDisplay,
        'amount' => $latest_row['amount'],
        'created_at' => date('d F Y', strtotime($latest_row['created_at']))
    ];
}

// Hitung total donasi yang sudah dikonfirmasi
$total_query = mysqli_query($koneksi, "SELECT SUM(amount) as total FROM donations WHERE status='confirmed'");
$total_row = mysqli_fetch_assoc($total_query);
$total_donasi = !empty($total_row['total']) ? $total_row['total'] : 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Donasi - DOMS</title>
    <style>
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;background:linear-gradient(135deg,#f8f1f1 0%,#ffe6f0 100%);min-height:100vh;color:#3a3a3a;}
        .navbar{position:fixed;top:0;left:0;right:0;background:#fff;display:flex;justify-content:space-between;align-items:center;padding:14px 30px;box-shadow:0 4px 15px rgba(226,117,159,0.15);border-bottom:2px solid rgba(226,117,159,0.15);z-index:1000;}
        .navbar .brand{font-size:22px;font-weight:700;color:#e2759f;}
        .menu-center{flex:1;display:flex;justify-content:center;gap:25px;}
        .menu-center a{color:#444;text-decoration:none;font-weight:500;padding:8px 15px;border-radius:10px;position:relative;}
        .menu-center a::after{content:"";position:absolute;bottom:0;left:50%;transform:translateX(-50%);width:0%;height:2px;background:linear-gradient(90deg,#ff99aa,#e2759f);transition:0.3s ease;}
        .menu-center a:hover{color:#e2759f;}
        .menu-center a:hover::after{width:60%;}
        .menu-center a.active{color:#e2759f;}
        .menu-center a.active::after{width:60%;}
        .logout a{color:#fff;background:linear-gradient(45deg,#e2759f,#c25a8a);padding:10px 22px;border-radius:25px;text-decoration:none;font-size:14px;font-weight:600;box-shadow:0 8px 20px rgba(226,117,159,0.3);}
        .logout a:hover{transform:translateY(-2px);background:linear-gradient(45deg,#c25a8a,#a0446f);box-shadow:0 12px 25px rgba(226,117,159,0.4);}
        .container{max-width:1200px;margin:110px auto 30px;background:#fff;padding:30px;border-radius:15px;box-shadow:0 5px 25px rgba(226,117,159,0.1);border:1px solid rgba(226,117,159,0.15);}
        h2{color:#e2759f;text-align:center;margin-bottom:30px;font-size:28px;}
        .latest-donor{background:#ffe6f0;border:1px solid #f3a9c2;border-radius:14px;padding:25px;margin-bottom:35px;box-shadow:0 5px 20px rgba(226,117,159,0.15);text-align:center;transition:0.3s;width:98%;margin-left:auto;margin-right:auto;}
        .latest-donor:hover{transform:translateY(-3px);box-shadow:0 6px 25px rgba(226,117,159,0.25);border-color:#e2759f;}
        .latest-donor h3{color:#e2759f;font-size:25px;font-weight:700;margin-bottom:12px;text-shadow:0 1px 1px rgba(226,117,159,0.2);}
        .latest-donor p{color:#444;font-size:17px;margin:6px 0;}
        .latest-donor span{font-weight:bold;color:#e2759f;}
        table{width:100%;border-collapse:collapse;border-radius:10px;overflow:hidden;box-shadow:0 3px 10px rgba(0,0,0,0.1);margin-top:20px;}
        th,td{padding:15px;text-align:left;border-bottom:1px solid #f0f0f0;}
        th{background:#e2759f;color:white;font-weight:600;text-align:center;}
        tr:nth-child(even){background:#fdf2f8;}
        tr:hover{background:#fce4ec;}
        .donation-amount{color:#e2759f;font-weight:bold;text-align:right;}
        .no-data{text-align:center;padding:40px;color:#666;font-style:italic;}
        .stats-box{text-align:center;margin-top:25px;font-size:18px;color:#333;}
        .stats-box span{font-weight:bold;color:#e2759f;}
        @media(max-width:768px){
            .container{margin:120px 15px 30px;padding:20px;}
            table{font-size:14px;}
            th,td{padding:10px 8px;}
        }
    </style>
</head>
<body>
<div class="navbar">
    <div class="brand"><b>DOMS</b></div>
    <div class="menu-center">
        <a href="dashboard.php">Home</a>
        <a href="tambah_donasi.php">Tambah Donasi</a>
        <a href="data_donasi.php" class="active">Data Donasi</a>
        <a href="target.php">Target</a>
        <a href="tentang.php">Tentang Kami</a>
    </div>
    <div class="logout">
        <a href="logout.php" onclick="return confirm('Yakin ingin logout?');">Logout</a>
    </div>
</div>

<div class="container">
    <h2>Data Donasi</h2>

    <div class="latest-donor">
        <?php if ($latest): ?>
            <h3>Selamat <?= htmlspecialchars($latest['donor_name']) ?> — Donasi Anda Telah Dikirim</h3>
            <p>Donasi sebesar <span>Rp <?= number_format($latest['amount'], 0, ',', '.') ?></span> telah dikirimkan.</p>
            <p>Silakan tunggu konfirmasi dari admin. Kami akan memproses verifikasi bukti transfer Anda secepatnya.</p>
            <p><small>Pada tanggal <?= htmlspecialchars($latest['created_at']) ?></small></p>
        <?php else: ?>
            <p>Belum ada donasi terbaru</p>
        <?php endif; ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Donatur</th>
                <th>Jumlah Donasi</th>
                <th>Tanggal</th>
                <th>Nomor HP</th>
                <th>Tujuan Donasi</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($donations) > 0): ?>
                <?php foreach ($donations as $i => $d): ?>
                    <?php
                        $donor = ($d['is_anonim']==1 || strtolower($d['donor_name'])=='anonymous') ? 'Nono Yaa' : $d['donor_name'];
                        $tujuan = $map[$d['tujuan_donasi']] ?? $d['tujuan_donasi'];
                        $phone = strlen($d['phone']) > 4 ? substr($d['phone'],0,4).'****'.substr($d['phone'],-2) : $d['phone'];
                    ?>
                    <tr>
                        <td style="text-align:center;font-weight:bold;color:#e2759f;"><?= $i+1 ?></td>
                        <td><?= htmlspecialchars($donor) ?></td>
                        <td class="donation-amount">Rp <?= number_format($d['amount'],0,',','.') ?></td>
                        <td style="text-align:center;"><?= date('d/m/Y', strtotime($d['created_at'])) ?></td>
                        <td style="text-align:center;"><?= htmlspecialchars($phone) ?></td>
                        <td style="text-align:center;"><?= htmlspecialchars($tujuan) ?></td>
                        <td style="text-align:center;">
                            <?php if (strtolower($d['status']) == 'confirmed' || strtolower($d['status']) == 'terkonfirmasi'): ?>
                                <span style="color:#4caf50;font-weight:bold;">Terkonfirmasi</span>
                            <?php else: ?>
                                <span style="color:#e2759f;font-weight:bold;">Pending</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="7" class="no-data">Belum ada data donasi</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
