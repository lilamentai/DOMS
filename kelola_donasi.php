<?php
session_start();
include 'koneksi.php';

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'kokwet') {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$nama = $_SESSION['nama'] ?? 'Admin';

/* ==========================================================
   🔧 CEK & TAMBAH KOLOM deleted_admin_temp OTOMATIS
========================================================== */
$checkColumn = mysqli_query($koneksi, "
    SHOW COLUMNS FROM donations LIKE 'deleted_admin_temp'
");
if (mysqli_num_rows($checkColumn) == 0) {
    mysqli_query($koneksi, "
        ALTER TABLE donations 
        ADD COLUMN deleted_admin_temp ENUM('yes', 'no') NOT NULL DEFAULT 'no' AFTER status
    ");
}

/* ==========================================================
   📊 STATISTIK
========================================================== */
$totalDonasiQ = mysqli_query($koneksi, "SELECT SUM(amount) as total FROM donations WHERE status='confirmed'");
$totalDonasi = ($totalDonasiQ && mysqli_num_rows($totalDonasiQ) > 0) ? mysqli_fetch_assoc($totalDonasiQ)['total'] ?? 0 : 0;

$totalDonaturQ = mysqli_query($koneksi, "SELECT COUNT(DISTINCT donor_name) as countd FROM donations WHERE status='confirmed'");
$totalDonatur = ($totalDonaturQ && mysqli_num_rows($totalDonaturQ) > 0) ? mysqli_fetch_assoc($totalDonaturQ)['countd'] ?? 0 : 0;

$pendingCountQ = mysqli_query($koneksi, "SELECT COUNT(*) as cnt FROM donations WHERE status='pending'");
$pendingCount = ($pendingCountQ && mysqli_num_rows($pendingCountQ) > 0) ? mysqli_fetch_assoc($pendingCountQ)['cnt'] ?? 0 : 0;

/* ==========================================================
   ✅ KONFIRMASI DONASI
========================================================== */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['confirm_donation'])) {
    $id = intval($_POST['donation_id']);
    $donasi_data = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama_bank FROM donations WHERE id='$id'"));
    $nama_bank = mysqli_real_escape_string($koneksi, $donasi_data['nama_bank']);

    mysqli_query($koneksi, "UPDATE donations SET status='confirmed' WHERE id='$id'");

    $donasi_data_full = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT donor_name, phone, amount, tujuan_donasi, bukti_transfer FROM donations WHERE id='$id'"));
    $bukti_transfer = mysqli_real_escape_string($koneksi, $donasi_data_full['bukti_transfer']);

    $check_transaksi = mysqli_query($koneksi, "SELECT id_transaksi FROM transaksi WHERE id_donation='$id'");
    if (mysqli_num_rows($check_transaksi) == 0) {
        $donor_name = mysqli_real_escape_string($koneksi, $donasi_data_full['donor_name']);
        $phone = mysqli_real_escape_string($koneksi, $donasi_data_full['phone']);
        $amount = $donasi_data_full['amount'];
        $tujuan_donasi = mysqli_real_escape_string($koneksi, $donasi_data_full['tujuan_donasi']);

        $query = "INSERT INTO transaksi (id_donation, donor_name, phone, amount, tujuan_donasi, nama_bank, bukti_transfer, status, created_at)
                  VALUES ('$id', '$donor_name', '$phone', $amount, '$tujuan_donasi', '$nama_bank', '$bukti_transfer', 'confirmed', NOW())";
        mysqli_query($koneksi, $query);
    } else {
        mysqli_query($koneksi, "UPDATE transaksi SET nama_bank='$nama_bank', status='confirmed' WHERE id_donation='$id'");
    }

    header("Location: kelola_donasi.php?msg=confirmed");
    exit();
}

/* ==========================================================
   🗑️ HAPUS (Sembunyikan dari admin)
========================================================== */
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_donation'])) {
    $id = intval($_POST['donation_id']);
    // Tandai hanya dihapus untuk tampilan admin, bukan hapus permanen
    mysqli_query($koneksi, "UPDATE donations SET deleted_admin_temp='yes' WHERE id='$id'");
    header("Location: kelola_donasi.php?msg=deleted");
    exit();
}

$donasiResult = mysqli_query($koneksi, "
    SELECT
        d.id as donation_id,
        t.id_transaksi,
        d.donor_name,
        d.phone,
        d.amount,
        IFNULL(d.nama_bank, '-') AS nama_bank,
        d.created_at,
        d.status,
        d.tujuan_donasi,
        d.is_anonim,
        COALESCE(t.bukti_transfer, d.bukti_transfer) AS bukti_transfer
    FROM donations d
    LEFT JOIN transaksi t ON d.id = t.id_donation
    WHERE d.status != 'deleted_admin' 
      AND (d.deleted_admin_temp IS NULL OR d.deleted_admin_temp = 'no')
    ORDER BY d.id DESC
");

if (!$donasiResult) {
    die('Query gagal: ' . mysqli_error($koneksi));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Kelola Donasi - Admin</title>
<style>
/* Semua CSS sama seperti versi kamu sebelumnya */
body {
    font-family: 'Segoe UI', Tahoma, sans-serif;
    background: linear-gradient(135deg,#f8f1f1 0%,#ffe6f0 100%);
    margin: 0;
    display: flex;
    min-height: 100vh;
    color: #3a3a3a;
    overflow-x: hidden;
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
.sidebar a:hover { color: #e2759f; }
.sidebar a:hover::after { width: 60%; }
.sidebar a.active { color: #e2759f; }
.sidebar a.active::after { width: 60%; }
.content {
    margin-left: 240px;
    padding: 30px;
    flex: 1;
}
.container {
    max-width: 1200px;
    margin: auto;
    background: #fff;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
}
h2 {
    text-align: center;
    color: #e2759f;
    margin-bottom: 20px;
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
.stat h4 { margin: 0; color: #e2759f; font-size: 18px; }
.stat p { margin: 10px 0 0 0; font-size: 24px; font-weight: bold; color: #333; }
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 3px 10px rgba(226, 117, 159, 0.1);
}
th, td {
    padding: 8px;
    border-bottom: 1px solid rgba(226, 117, 159, 0.2);
    text-align: center;
    font-size: 12px;
}
th { background: #e2759f; color: #fff; }
tr:nth-child(even) { background: #fdf2f8; }
tr:hover { background: #fce7f3; }
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
.action-link {
    padding: 4px 6px;
    border-radius: 6px;
    text-decoration: none;
    color: #fff;
    font-size: 11px;
    border: none;
    cursor: pointer;
}
.action-confirm { background: #4caf50; }
.action-delete { background: #e74c3c; }
.bukti-transfer-img {
    max-height: 60px;
    border: 1px solid #e2759f;
    border-radius: 8px;
    cursor: pointer;
    transition: box-shadow 0.3s ease;
}
.bukti-transfer-img:hover { box-shadow: 0 0 12px 3px rgba(226, 117, 159, 0.4); }
.modal {
    display: none;
    position: fixed;
    z-index: 1001;
    padding-top: 100px;
    left: 0; top: 0;
    width: 100%; height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.7);
}
.modal-content {
    margin: auto;
    display: block;
    max-width: 80%;
    max-height: 80%;
    border-radius: 12px;
    box-shadow: 0 8px 30px rgba(226,117,159,0.6);
}
.modal-close {
    position: absolute;
    top: 40px; right: 50px;
    color: white;
    font-size: 35px;
    font-weight: bold;
    cursor: pointer;
}
.modal-close:hover { color: #e2759f; }
.table-wrapper { overflow-x: auto; }
</style>
</head>
<body>
<div class="sidebar">
   <h2>Selamat Datang, Admin</h2>
    <a href="admin.php">Dashboard</a>
    <a href="kelola_donasi.php" class="active">Kelola</a>
    <a href="laporan_donasi.php">Laporan</a>
    <a href="edit.php">Edit</a>
    <a href="logout.php">Logout</a>
</div>

<div class="content">
<div class="container">
<h2>Kelola Donasi</h2>

<div class="stats">
    <div class="stat"><h4>Total Donasi</h4><p>Rp <?= number_format($totalDonasi, 0, ',', '.'); ?></p></div>
    <div class="stat"><h4>Jumlah Donatur</h4><p><?= $totalDonatur; ?></p></div>
    <div class="stat"><h4>Donasi Pending</h4><p><?= $pendingCount; ?></p></div>
</div>

<?php if (isset($_GET['msg']) && $_GET['msg'] == 'confirmed'): ?>
<div style="background:#e6f8ee;color:#117a3a;padding:10px;border-radius:6px;margin-bottom:15px;text-align:center;">
✅ Donasi berhasil dikonfirmasi!
</div>
<?php elseif (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
<div style="background:#ffe6e6;color:#a30000;padding:10px;border-radius:6px;margin-bottom:15px;text-align:center;">
🗑️ Donasi berhasil dihapus dari dashboard admin!
</div>
<?php endif; ?>

<div class="table-wrapper">
<table>
<tr>
<th>ID Donasi</th>
<th>Donatur</th>
<th>No. HP</th>
<th>Jumlah</th>
<th>Tujuan</th>
<th>Nama Bank</th>
<th>Tanggal</th>
<th>Status</th>
<th>Bukti Transfer</th>
<th>Aksi</th>
</tr>

<?php if (mysqli_num_rows($donasiResult) > 0): ?>
<?php while ($d = mysqli_fetch_assoc($donasiResult)): ?>
<tr>
<td><?= htmlspecialchars($d['donation_id']); ?></td>
<td><?= $d['is_anonim'] ? 'anonim' : htmlspecialchars($d['donor_name']); ?></td>
<td><?= htmlspecialchars($d['phone']); ?></td>
<td>Rp <?= number_format($d['amount'], 0, ',', '.'); ?></td>
<td><?= ucfirst(str_replace('_', ' ', $d['tujuan_donasi'])); ?></td>
<td><?= htmlspecialchars($d['nama_bank']); ?></td>
<td><?= date('d/m/Y H:i', strtotime($d['created_at'])); ?></td>
<td>
<?php if ($d['status'] === 'pending'): ?>
<span class="status-pending">Pending</span>
<?php else: ?>
<span class="status-confirm">Confirmed</span>
<?php endif; ?>
</td>
<td>
<?php if (!empty($d['bukti_transfer']) && file_exists("uploads/" . $d['bukti_transfer'])): ?>
<img src="uploads/<?= htmlspecialchars($d['bukti_transfer']); ?>" class="bukti-transfer-img" onclick="openModal(this)">
<?php else: ?>-<?php endif; ?>
</td>
<td>
<?php if ($d['status'] === 'pending'): ?>
<form method="POST" style="display:inline;">
<input type="hidden" name="donation_id" value="<?= $d['donation_id']; ?>">
<button type="submit" name="confirm_donation" class="action-link action-confirm">Konfirmasi</button>
</form>
<?php elseif ($d['status'] === 'confirmed'): ?>
<form method="POST" style="display:inline;">
<input type="hidden" name="donation_id" value="<?= $d['donation_id']; ?>">
<button type="submit" name="delete_donation" class="action-link action-delete" onclick="return confirm('Hapus donasi ini dari dashboard admin?')">Hapus</button>
</form>
<?php endif; ?>
</td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr><td colspan="10">Belum ada data donasi</td></tr>
<?php endif; ?>
</table>
</div>
</div>

<div id="imgModal" class="modal">
<span class="modal-close" onclick="closeModal()">&times;</span>
<img class="modal-content" id="modalImage">
</div>

<script>
function openModal(imgElement){const modal=document.getElementById('imgModal');const modalImg=document.getElementById('modalImage');modal.style.display="block";modalImg.src=imgElement.src;}
function closeModal(){document.getElementById('imgModal').style.display="none";}
window.onclick=function(event){const modal=document.getElementById('imgModal');if(event.target===modal)modal.style.display="none";}
</script>
</body>
</html>
