<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
if ($username !== 'kokwet') {
    header("Location: dashboard.php");
    exit();
}

// rekap total per bulan
$rekapBulanQ = mysqli_query($koneksi, "
    SELECT DATE_FORMAT(created_at, '%Y-%m') AS bulan, SUM(amount) AS total
    FROM donations
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY bulan DESC
");

// ambil status yang sinkron dengan dashboard
$statusQ = mysqli_query($koneksi, "
    SELECT status, COUNT(*) AS jumlah
    FROM donations
    WHERE status IN ('pending','confirmed')
    GROUP BY status
");

// top donatur
$topDonaturQ = mysqli_query($koneksi, "
    SELECT donor_name, SUM(amount) AS total
    FROM donations
    GROUP BY donor_name
    ORDER BY total DESC
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Laporan Donasi - Admin</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: linear-gradient(135deg,#f8f1f1 0%,#ffe6f0 100%);
            margin: 0;
            display: flex;
            min-height: 100vh;
            color: #3a3a3a;
        }

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

        .content {
            margin-left: 240px;
            padding: 30px;
            flex: 1;
        }

        .container {
            max-width: 1000px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }

        h2 {
            text-align: center;
            color: #e2759f;
            margin-bottom: 25px;
        }

        h3 {
            color: #e2759f;
            margin-top: 30px;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(226, 117, 159, 0.1);
        }

        th,
        td {
            padding: 12px;
            border-bottom: 1px solid rgba(226, 117, 159, 0.2);
            text-align: center;
        }

        th {
            background: #e2759f;
            color: #fff;
        }

        tr:nth-child(even) {
            background: #fdf2f8;
        }

        tr:hover {
            background: #fce7f3;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Selamat Datang, Admin</h2>
        <a href="admin.php">Dashboard</a>
        <a href="kelola_donasi.php">Kelola </a>
        <a href="laporan_donasi.php" class="active">Laporan </a>
        <a href="edit.php">Edit</a>
        <a href="logout.php">Logout</a>
    </div>

    <!-- Konten -->
    <div class="content">
        <div class="container">
            <h2>Laporan Donasi</h2>

            <!-- Rekap per bulan -->
            <h3>📊 Rekap Donasi per Bulan</h3>
            <table>
                <tr>
                    <th>Bulan</th>
                    <th>Total Donasi</th>
                </tr>
                <?php while ($r = mysqli_fetch_assoc($rekapBulanQ)) { ?>
                    <tr>
                        <td><?php echo $r['bulan']; ?></td>
                        <td>Rp <?php echo number_format($r['total'], 0, ',', '.'); ?></td>
                    </tr>
                <?php } ?>
            </table>

            <!-- Status donasi -->
            <h3>📌 Status Donasi</h3>
            <table>
                <tr>
                    <th>Status</th>
                    <th>Jumlah</th>
                </tr>
                <?php 
                // Menampilkan ulang hasil query status
                mysqli_data_seek($statusQ, 0);
                while ($s = mysqli_fetch_assoc($statusQ)) { ?>
                    <tr>
                        <td><?php echo ucfirst($s['status']); ?></td>
                        <td><?php echo $s['jumlah']; ?></td>
                    </tr>
                <?php } ?>
            </table>

            <!-- Top Donatur -->
            <h3>🏅 Top 5 Donatur</h3>
            <table>
                <tr>
                    <th>Nama Donatur</th>
                    <th>Total Donasi</th>
                </tr>
                <?php while ($d = mysqli_fetch_assoc($topDonaturQ)) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($d['donor_name']); ?></td>
                        <td>Rp <?php echo number_format($d['total'], 0, ',', '.'); ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</body>

</html>
