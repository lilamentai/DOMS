<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
if (!isset($_SESSION['donasi_data'])) {
    header("Location: tambah_donasi.php");
    exit;
}

$username = $_SESSION['username'];
$donasi_data = $_SESSION['donasi_data'];

// Ambil nomor rekening dari database sesuai nama bank
$bank = $donasi_data['nama_bank'];
$rek_result = mysqli_query($koneksi, "SELECT rekening_number FROM donations WHERE nama_bank = '$bank' LIMIT 1");

if ($rek_result && mysqli_num_rows($rek_result) > 0) {
    $rek_row = mysqli_fetch_assoc($rek_result);
    $no_rekening = $rek_row['rekening_number'];
} else {
    $no_rekening = 'Tidak ditemukan di database';
}

// cegah insert ganda: flag session
if (!isset($_SESSION['donation_saved'])) {
    $_SESSION['donation_saved'] = false;
}

// Jika konfirmasi donasi (simpan ke DB sekali)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['konfirmasi'])) {
    if (!$_SESSION['donation_saved']) {
        $donor_name = $donasi_data['is_anonim'] ? 'Anonim' : $donasi_data['donor_name'];
        $status = 'Pending';

        $stmt = $koneksi->prepare("
            INSERT INTO donations (username, donor_name, phone, tujuan_donasi, amount, is_anonim, nama_bank, rekening_number, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->bind_param('ssssissss', $username, $donor_name, $donasi_data['phone'], $donasi_data['tujuan_donasi'], $donasi_data['amount'], $donasi_data['is_anonim'], $donasi_data['nama_bank'], $no_rekening, $status);
        $stmt->execute();
        if ($stmt->error) {
            die("Gagal simpan donasi: " . $stmt->error);
        }
        $donation_id = $stmt->insert_id;
        $stmt->close();

        $stmt2 = $koneksi->prepare("
            INSERT INTO transaksi (id_donation, donor_name, phone, amount, tujuan_donasi, nama_bank, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt2->bind_param('issssss', $donation_id, $donor_name, $donasi_data['phone'], $donasi_data['amount'], $donasi_data['tujuan_donasi'], $donasi_data['nama_bank'], $status);
        $stmt2->execute();
        $stmt2->close();

        $_SESSION['donation_id'] = $donation_id;
        $_SESSION['donation_saved'] = true; // sudah disimpan
    }

    unset($_SESSION['donasi_data']);
    header("Location: bukti_trans.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Review Donasi</title>
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    body {
        background: linear-gradient(135deg, #f8f1f1 0%, #ffe6f0 100%);
        min-height: 100vh; display: flex; justify-content: center; align-items: center; padding: 20px;
    }
    .container {
        background: #fff; padding: 40px 30px; border-radius: 25px;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        width: 100%; max-width: 500px; border: 2px solid rgba(226, 117, 159, 0.2);
    }
    h2 { text-align: center; color: #e2759f; font-size: 26px; margin-bottom: 25px; font-weight: 700; }
    .info-box { background: #fff0f6; border-left: 5px solid #e2759f; padding: 15px 20px; border-radius: 15px; margin-bottom: 25px; box-shadow: 0 4px 10px rgba(226, 117, 159, 0.1); }
    .info-item { display: flex; justify-content: space-between; border-bottom: 1px solid rgba(226, 117, 159, 0.15); padding: 8px 0; font-size: 15px; }
    .info-item:last-child { border: none; }
    .info-label { color: #e2759f; font-weight: 600; }
    .info-value { color: #444; font-weight: 500; }
    .rekening-box { background: #fdf2f7; border-radius: 15px; padding: 15px 20px; font-size: 15px; color: #333; margin-top: 10px; text-align: center; border: 2px dashed #e2759f; position: relative; }
    .copy-btn { position: absolute; top: 50%; right: 15px; transform: translateY(-50%); background: #e2759f; color: #fff; border: none; padding: 6px 10px; border-radius: 10px; cursor: pointer; font-size: 13px; }
    .btn { display: block; width: 100%; background: linear-gradient(45deg, #e2759f, #c25a8a); color: white; padding: 15px 40px; border: none; border-radius: 25px; font-size: 18px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 8px 20px rgba(226, 117, 159, 0.3); margin-top: 15px; }
    .btn:hover { transform: translateY(-2px); box-shadow: 0 12px 25px rgba(226, 117, 159, 0.4); background: linear-gradient(45deg, #c25a8a, #a0446f); }
    .back-link { text-align: center; margin-top: 15px; }
    .link-text { color: #e2759f; text-decoration: none; font-weight: 600; }
    .link-text:hover { text-decoration: underline; }
    .instruction { margin-top:12px; font-size:14px; color:#555; line-height:1.5; background:#fff; border-radius:10px; padding:12px; border:1px solid #eee; }
</style>
</head>
<body>

<div class="container">
    <h2>Review Donasi</h2>

    <div class="info-box">
        <div class="info-item">
            <span class="info-label">Nama Donatur:</span>
            <span class="info-value"><?= htmlspecialchars($donasi_data['is_anonim'] ? '****' : $donasi_data['donor_name']) ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">Tujuan Donasi:</span>
            <span class="info-value"><?= htmlspecialchars($donasi_data['tujuan_donasi']) ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">Jumlah Donasi:</span>
            <span class="info-value">Rp <?= number_format($donasi_data['amount'], 0, ',', '.') ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">Bank Tujuan:</span>
            <span class="info-value"><?= htmlspecialchars($donasi_data['nama_bank']) ?></span>
        </div>
    </div>

    <div class="rekening-box" id="rekeningBox">
        Nomor Rekening: <b><?= htmlspecialchars($no_rekening) ?></b>
        <button class="copy-btn" onclick="copyRekening('<?= htmlspecialchars($no_rekening) ?>')">Salin</button>
    </div>

    <div class="instruction">
        💡 Silakan salin nomor rekening di atas, lalu lakukan transfer sesuai jumlah donasi Anda.<br>
        Jangan lupa untuk <b>screenshot bukti transfer</b> sebagai konfirmasi donasi.
    </div>

    <form method="POST" style="margin-top:16px;">
        <button type="submit" name="konfirmasi" class="btn">Konfirmasi Donasi</button>
    </form>

    <div class="back-link">
        <a href="transaksi.php" class="link-text">Kembali</a>
    </div>
</div>

<script>
function copyRekening(noRek) {
    navigator.clipboard.writeText(noRek).then(() => {
        alert("Nomor rekening berhasil disalin!");
    }).catch(err => {
        console.error("Gagal menyalin teks: ", err);
    });
}
</script>

</body>
</html>
