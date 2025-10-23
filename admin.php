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
$nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : "Admin";

// Cek admin
if ($username != 'kokwet') {
    header("Location: dashboard.php");
    exit;
}

// Ambil total donasi confirmed
$totalDonasiQ = mysqli_query($koneksi, "SELECT SUM(amount) as total FROM donations WHERE status='confirmed' AND deleted_admin_temp = 0");
$totalDonasi = ($totalDonasiQ && mysqli_num_rows($totalDonasiQ) > 0) ? mysqli_fetch_assoc($totalDonasiQ)['total'] ?? 0 : 0;

// Ambil jumlah donatur confirmed
$totalDonaturQ = mysqli_query($koneksi, "SELECT COUNT(DISTINCT donor_name) as countd FROM donations WHERE status='confirmed' AND deleted_admin_temp = 0");
$totalDonatur = ($totalDonaturQ && mysqli_num_rows($totalDonaturQ) > 0) ? mysqli_fetch_assoc($totalDonaturQ)['countd'] ?? 0 : 0;

// Ambil jumlah pending
$pendingCountQ = mysqli_query($koneksi, "SELECT COUNT(*) as cnt FROM donations WHERE status='pending' AND deleted_admin_temp = 0");
$pendingCount = ($pendingCountQ && mysqli_num_rows($pendingCountQ) > 0) ? mysqli_fetch_assoc($pendingCountQ)['cnt'] ?? 0 : 0;

// Ambil daftar donasi terbaru (tidak menampilkan yang sudah dihapus sementara)
$donasiResult = mysqli_query(
    $koneksi,
    "SELECT d.id as donation_id, d.donor_name, d.phone, d.amount,
            IFNULL(d.nama_bank, '-') AS nama_bank,
            d.created_at, d.status, d.bukti_transfer, d.tujuan_donasi, d.is_anonim
     FROM donations d
     WHERE d.deleted_admin_temp = 'no'
     ORDER BY d.id DESC
     LIMIT 100"
);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Dashboard Admin</title>
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
        .stats {
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
        }
        .stat {
            background: rgba(226, 117, 159, 0.1);
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(226, 117, 159, 0.1);
            flex: 1;
            margin: 0 10px;
        }
        .stat h4 {
            margin: 0;
            color: #e2759f;
            font-size: 18px;
        }
        .stat p {
            margin: 10px 0 0 0;
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(226, 117, 159, 0.1);
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid rgba(226, 117, 159, 0.2);
            text-align: center;
        }
        th {
            background: #e2759f;
            color: #fff;
        }
        tr:nth-child(even) { background: #fdf2f8; }
        tr:hover { background: #fce4ec; }
        .bukti-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }
        .bukti-img:hover {
            transform: scale(1.1);
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            padding-top: 80px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.8);
        }
        .modal-content {
            margin: auto;
            display: block;
            width: 60%;
            max-width: 600px;
            border-radius: 10px;
        }
        .close {
            position: absolute;
            top: 40px;
            right: 60px;
            color: white;
            font-size: 35px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: #e2759f;
        }
        .status-pending {
            background: #fff5f7;
            color: #b0003a;
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 600;
        }
        .status-confirm {
            background: #e6f8ee;
            color: #117a3a;
            padding: 4px 8px;
            border-radius: 6px;
            font-weight: 600;
        }

        /* Judul Dashboard di atas */
        .title-container {
            position: fixed;
            top: 0;
            left: 240px;
            width: calc(100% - 240px);
            background: #fff;
            text-align: center;
            padding: 15px 0;
            box-shadow: 0 3px 8px rgba(226, 117, 159, 0.15);
            z-index: 500;
        }
        .title-container h1 {
            color: #e2759f;
            margin: 0;
            font-size: 26px;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(226,117,159,0.3);
        }
        .content { padding-top: 100px; } /* kasih jarak agar konten tidak ketiban judul */
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Selamat Datang, Admin</h2>
        <a href="admin.php" class="active">Dashboard</a>
        <a href="kelola_donasi.php">Kelola</a>
        <a href="laporan_donasi.php">Laporan</a>
        <a href="edit.php">Edit</a>
        <a href="logout.php" onclick="return confirm('Yakin ingin logout?');">Logout</a>
    </div>

    <!-- Judul Dashboard di atas -->
    <div class="title-container">
        <h1>Dashboard Admin</h1>
    </div>

    <!-- Konten -->
    <div class="content">
        <div class="container">
            <table>
                <thead>
                    <tr>
                        <th>ID Donasi</th>
                        <th>Donatur</th>
                        <th>No. HP</th>
                        <th>Jumlah</th>
                        <th>Tujuan</th>
                        <th>Nama Bank</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $tujuan_map = [];
                    $tujuan_query = mysqli_query($koneksi, "SELECT DISTINCT tujuan_donasi FROM donations ORDER BY tujuan_donasi");
                    while ($row = mysqli_fetch_assoc($tujuan_query)) {
                        $tujuan_map[$row['tujuan_donasi']] = $row['tujuan_donasi'];
                    }
                    if ($donasiResult && mysqli_num_rows($donasiResult) > 0) {
                        while ($d = mysqli_fetch_assoc($donasiResult)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($d['donation_id']) . "</td>";
                            $namaDonatur = $d['donor_name'];
                            if ($d['is_anonim'] == 1 || $namaDonatur === 'Anonymous' || $namaDonatur === 'Donatur Anonim') {
                                echo "<td>anonim</td>";
                            } else {
                                echo "<td>" . htmlspecialchars($namaDonatur) . "</td>";
                            }
                            echo "<td>" . htmlspecialchars($d['phone']) . "</td>";
                            echo "<td>Rp " . number_format($d['amount'], 0, ',', '.') . "</td>";
                            echo "<td>" . ($tujuan_map[$d['tujuan_donasi']] ?? ucfirst($d['tujuan_donasi'])) . "</td>";
                            echo "<td>" . htmlspecialchars($d['nama_bank']) . "</td>";
                            echo "<td>" . date('d/m/Y H:i', strtotime($d['created_at'])) . "</td>";
                            if ($d['status'] === 'pending') {
                                echo "<td><span class='status-pending'>Pending</span></td>";
                            } else {
                                echo "<td><span class='status-confirm'>Confirmed</span></td>";
                            }
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>Tidak ada data donasi.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Gambar -->
    <div id="imgModal" class="modal">
        <span class="close" onclick="closeModal()">&times;</span>
        <img class="modal-content" id="modalImage">
    </div>

    <script>
        function showModal(src) {
            document.getElementById("imgModal").style.display = "block";
            document.getElementById("modalImage").src = src;
        }
        function closeModal() {
            document.getElementById("imgModal").style.display = "none";
        }
    </script>
</body>
</html>
