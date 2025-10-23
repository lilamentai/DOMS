<?php
session_start();
include 'koneksi.php';

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_SESSION['donasi_data'])) {
    header("Location: tambah_donasi.php");
    exit;
}

$donasi_data = $_SESSION['donasi_data'];
$username = $_SESSION['username'];

$user_query = mysqli_query($koneksi, "SELECT nama FROM users WHERE username='$username' LIMIT 1");
$user_data = mysqli_fetch_assoc($user_query);
$nama_user = $user_data['nama'] ?? '';

// proses form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = (float) $_POST['amount'];
    $nama_bank = mysqli_real_escape_string($koneksi, $_POST['nama_bank']);

    if ($amount < 10000) {
        $error = "Jumlah donasi minimal Rp 10.000";
    } else {
        // simpan ke session
        $_SESSION['donasi_data']['amount'] = $amount;
        $_SESSION['donasi_data']['nama_bank'] = $nama_bank;

        header("Location: review_donasi.php");
        exit;
    }
}

$nomor_rekening = [
    'BCA' => '456749854448',
    'Mandiri' => '123456789012',
    'BNI' => '987654321098',
    'BRI' => '102938475610'
];

// Ambil data rekening dari database jika ada (opsional)
$rekening_query = mysqli_query($koneksi, "SELECT nama_bank, rekening_number FROM donations");
while ($row = mysqli_fetch_assoc($rekening_query)) {
    $nomor_rekening[$row['nama_bank']] = $row['rekening_number'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Transaksi Donasi</title>
<style>
    * {
        margin: 0; padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        background: linear-gradient(135deg, #f8f1f1 0%, #ffe6f0 100%);
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    .container {
        background: #fff;
        padding: 40px 30px;
        border-radius: 25px;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
        width: 100%;
        max-width: 500px;
        border: 2px solid rgba(226, 117, 159, 0.2);
    }

    h2 {
        text-align: center;
        color: #e2759f;
        font-size: 26px;
        margin-bottom: 25px;
        font-weight: 700;
    }

    .info-box {
        background: #fff0f6;
        border-left: 5px solid #e2759f;
        padding: 15px 20px;
        border-radius: 15px;
        margin-bottom: 25px;
        box-shadow: 0 4px 10px rgba(226, 117, 159, 0.1);
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        border-bottom: 1px solid rgba(226, 117, 159, 0.15);
        padding: 8px 0;
        font-size: 15px;
    }

    .info-item:last-child { border: none; }

    .info-label {
        color: #e2759f;
        font-weight: 600;
    }

    .info-value {
        color: #444;
        font-weight: 500;
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

    input, select {
        width: 100%;
        padding: 14px 16px;
        border-radius: 15px;
        border: 2px solid #f8bbd9;
        font-size: 15px;
        transition: all 0.3s ease;
    }

    input:focus, select:focus {
        outline: none;
        border-color: #e2759f;
        box-shadow: 0 0 0 3px rgba(226, 117, 159, 0.15);
    }

    .amount-input {
        position: relative;
    }

    .amount-input::before {
        content: "Rp";
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #666;
        font-weight: 600;
    }

    .amount-input input {
        padding-left: 45px;
    }

    .bank-info {
        display: none;
        background: #fdf2f7;
        border-radius: 15px;
        padding: 12px 15px;
        font-size: 14px;
        color: #444;
        margin-top: 10px;
        text-align: center;
        box-shadow: 0 4px 12px rgba(226, 117, 159, 0.15);
        position: relative;
    }

    .copy-btn {
        background: #e2759f;
        border: none;
        color: #fff;
        padding: 5px 10px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 12px;
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
    }

    .copy-btn:hover {
        background: #c25a8a;
    }

    .btn-confirm {
        background: linear-gradient(45deg, #e2759f, #c25a8a);
        color: white;
        padding: 15px 40px;
        border: none;
        border-radius: 25px;
        font-size: 18px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 8px 20px rgba(226, 117, 159, 0.3);
        width: 100%;
        margin-top: 20px;
    }

    .btn-confirm:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 25px rgba(226, 117, 159, 0.4);
        background: linear-gradient(45deg, #c25a8a, #a0446f);
    }

    .login-link {
        text-align: center;
        margin-top: 20px;
        font-size: 14px;
    }

    .link-text {
        cursor: pointer;
        color: #e2759f;
        text-decoration: none;
    }

    .link-text:hover {
        border-bottom: 1px solid #e2759f;
    }

    .error-message {
        background: linear-gradient(45deg, #ff4757, #ff3742);
        color: white;
        padding: 12px 20px;
        border-radius: 10px;
        text-align: center;
        margin-bottom: 25px;
        font-size: 14px;
        box-shadow: 0 4px 15px rgba(255, 71, 87, 0.3);
    }
</style>
</head>
<body>
<div class="container">
    <h2>Transaksi</h2>

    <?php if (isset($error)): ?>
        <div class="error-message"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="info-box">
        <div class="info-item">
            <span class="info-label">Nama Donatur:</span>
            <span class="info-value"><?= htmlspecialchars($donasi_data['is_anonim'] ? '****' : $donasi_data['donor_name']) ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">Tujuan Donasi:</span>
            <span class="info-value"><?= htmlspecialchars($donasi_data['tujuan_donasi']) ?></span>
        </div>
    </div>

    <form method="POST">
        <div class="form-group">
            <label for="amount">Jumlah Donasi</label>
            <div class="amount-input">
                <input type="number" id="amount" name="amount"
                       placeholder="10000"
                       min="10000" step="1000"
                       value="<?= htmlspecialchars($_POST['amount'] ?? ($donasi_data['amount'] ?? '')) ?>" required>
            </div>
        </div>

        <div class="form-group">
            <label for="nama_bank">Pilih Bank Tujuan</label>
            <select name="nama_bank" id="nama_bank" required>
                <option value="BCA" data-rekening="<?= $nomor_rekening['BCA'] ?>"
                    <?= (($_POST['nama_bank'] ?? ($donasi_data['nama_bank'] ?? '')) == 'BCA') ? 'selected' : '' ?>>BCA</option>
                <option value="Mandiri" data-rekening="<?= $nomor_rekening['Mandiri'] ?>"
                    <?= (($_POST['nama_bank'] ?? ($donasi_data['nama_bank'] ?? '')) == 'Mandiri') ? 'selected' : '' ?>>Mandiri</option>
                <option value="BNI" data-rekening="<?= $nomor_rekening['BNI'] ?>"
                    <?= (($_POST['nama_bank'] ?? ($donasi_data['nama_bank'] ?? '')) == 'BNI') ? 'selected' : '' ?>>BNI</option>
                <option value="BRI" data-rekening="<?= $nomor_rekening['BRI'] ?>"
                    <?= (($_POST['nama_bank'] ?? ($donasi_data['nama_bank'] ?? '')) == 'BRI') ? 'selected' : '' ?>>BRI</option>
            </select>
        </div>

        <button type="submit" class="btn-confirm">Lanjut</button>
        <div class="login-link">
            <a href="tambah_donasi.php" class="link-text">Kembali</a>
        </div>
    </form>
</div>

</body>
</html>
