<?php
session_start();
include 'koneksi.php';

// cek koneksi
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// pastikan user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// ambil data user berdasarkan username
$user_query = mysqli_query($koneksi, "SELECT nama FROM users WHERE username='$username' LIMIT 1");
$user_data = mysqli_fetch_assoc($user_query);
$donor_name = $user_data['nama'] ?? $username;
$nama_user = $donor_name;

// Ambil daftar tujuan donasi dari database (gunakan kapital, exclude palstina, umum, yang ada underscore, kosong, dan '0')
$tujuan_query = mysqli_query($koneksi, "SELECT DISTINCT UPPER(tujuan_donasi) AS tujuan_donasi FROM donations WHERE UPPER(tujuan_donasi) NOT IN ('PALSTINA', 'UMUM') AND tujuan_donasi NOT LIKE '%_%' AND tujuan_donasi != '' AND tujuan_donasi != '0' AND tujuan_donasi IS NOT NULL ORDER BY tujuan_donasi");

// Ambil pesan error jika ada
$error = $_SESSION['error_msg'] ?? '';
unset($_SESSION['error_msg']);

// Ambil data awal dari session agar form tetap terisi ketika kembali
$old = $_SESSION['donasi_data'] ?? [
    'donor_name' => $donor_name,
    'phone' => '',
    'tujuan_donasi' => '',
    'amount' => '',
    'nama_bank' => '',
    'is_anonim' => 0
];

// PROSES FORM JIKA ADA SUBMIT
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['donate'])) {
    // ambil data dari form
    $donor_name_post = trim($_POST['nama'] ?? $donor_name);
    $phone = trim($_POST['phone'] ?? '');
    $tujuan_donasi = trim($_POST['tujuan_donasi'] ?? '');
    $is_anonim = isset($_POST['is_anonim']) ? 1 : 0;

    // validasi (backend)
    if ($phone === '') {
        $_SESSION['error_msg'] = "Nomor HP harus diisi.";
        header("Location: tambah_donasi.php");
        exit;
    }

    // validasi nomor hp: mulai 08 dan panjang 11-13 digit (08 + 9..11 digit)
    if (!preg_match('/^08[0-9]{9,11}$/', $phone)) {
        $_SESSION['error_msg'] = "Nomor HP harus diawali dengan 08 dan memiliki panjang 11–13 digit.";
        // simpan input agar tidak hilang
        $_SESSION['donasi_data'] = [
            'donor_name' => $donor_name_post,
            'phone' => $phone,
            'tujuan_donasi' => $tujuan_donasi,
            'amount' => $old['amount'] ?? '',
            'nama_bank' => $old['nama_bank'] ?? '',
            'is_anonim' => $is_anonim
        ];
        header("Location: tambah_donasi.php");
        exit;
    }

    if ($tujuan_donasi === '') {
        $_SESSION['error_msg'] = "Tujuan donasi harus dipilih.";
        $_SESSION['donasi_data'] = [
            'donor_name' => $donor_name_post,
            'phone' => $phone,
            'tujuan_donasi' => $tujuan_donasi,
            'amount' => $old['amount'] ?? '',
            'nama_bank' => $old['nama_bank'] ?? '',
            'is_anonim' => $is_anonim
        ];
        header("Location: tambah_donasi.php");
        exit;
    }

    // Simpan ke session untuk halaman transaksi
    $_SESSION['donasi_data'] = [
        'donor_name' => $donor_name_post,
        'phone' => $phone,
        'tujuan_donasi' => $tujuan_donasi,
        'amount' => $old['amount'] ?? '',
        'nama_bank' => $old['nama_bank'] ?? '',
        'is_anonim' => $is_anonim
    ];

    // Langsung ke halaman transaksi
    header("Location: transaksi.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Formulir Donasi</title>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        transition: all 0.3s ease;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #f8f1f1 0%, #ffe6f0 100%);
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding: 40px 20px;
    }

    .container {
        background: #fff;
        padding: 40px 30px;
        border-radius: 25px;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        width: 100%;
        max-width: 500px;
        border: 2px solid rgba(226, 117, 159, 0.2);
        margin-top: 60px;
    }

    h2 {
        text-align: center;
        color: #e2759f;
        font-size: 26px;
        margin-bottom: 25px;
        font-weight: 700;
    }

    .message {
        padding: 12px 20px;
        border-radius: 10px;
        text-align: center;
        margin-bottom: 25px;
        font-size: 14px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .message.error {
        background: linear-gradient(45deg, #ff4757, #ff3742);
        color: white;
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

    input[type="text"],
    select {
        width: 100%;
        padding: 14px 16px;
        border-radius: 15px;
        border: 2px solid #f8bbd9;
        font-size: 15px;
        transition: all 0.3s ease;
        box-sizing: border-box;
        background: rgba(255, 255, 255, 0.9);
    }

    input[type="text"]:focus,
    select:focus {
        outline: none;
        border-color: #e2759f;
        box-shadow: 0 0 0 3px rgba(226, 117, 159, 0.15);
        background: white;
    }

    .checkbox-group {
        display: flex;
        align-items: center;
        margin-top: -10px;
        margin-bottom: 15px;
    }

    .checkbox-group input[type="checkbox"] {
        margin-right: 10px;
        transform: scale(1.1);
        accent-color: #e2759f;
    }

    .small-label {
        font-size: 15px;
        color: #e2759f;
        font-weight: 500;
    }

    .submit-btn {
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

    .submit-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 25px rgba(226, 117, 159, 0.4);
        background: linear-gradient(45deg, #c25a8a, #a0446f);
    }

    .link-text {
        cursor: pointer;
        color: #e2759f;
        text-decoration: none;
    }

    .link-text:hover {
        border-bottom: 1px solid #e2759f;
    }

    .login-link {
        text-align: center;
        margin-top: 20px;
        font-size: 14px;
    }

    @media (max-width: 480px) {
        .container {
            padding: 30px 20px;
            margin: 10px;
        }

        h2 {
            font-size: 24px;
        }

        .submit-btn {
            width: 100%;
            margin-bottom: 15px;
        }
    }
</style>
</head>

<body>
    <div class="container">
        <h2>Daftar Donasi</h2>

        <?php if (!empty($error)): ?>
            <div class="message error">❌ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="nama">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($_SESSION['donasi_data']['donor_name'] ?? $nama_user); ?>" required>
            </div>

            <div class="form-group">
                <div class="checkbox-group">
                    <input type="checkbox" id="is_anonim" name="is_anonim" value="1" <?= (!empty($_SESSION['donasi_data']['is_anonim'])) ? 'checked' : '' ?>>
                    <label for="is_anonim" class="small-label">Donasi sebagai anonim</label>
                </div>
            </div>

            <div class="form-group">
                <label for="phone">Nomor HP</label>
                <input type="text" id="phone" name="phone" placeholder="08123456789"
                       value="<?= htmlspecialchars($_SESSION['donasi_data']['phone'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="tujuan_donasi">Tujuan Donasi</label>
                <select id="tujuan_donasi" name="tujuan_donasi" required>
                    <?php
                    $options = [];
                    // Always add BANTUAN BENCANA as the first option
                    echo '<option value="BANTUAN BENCANA">BANTUAN BENCANA</option>';
                    $options[] = 'BANTUAN BENCANA';
                    while ($row = mysqli_fetch_assoc($tujuan_query)) {
                        if (!in_array($row['tujuan_donasi'], $options)) {
                            $options[] = $row['tujuan_donasi'];
                            $sel = (isset($_SESSION['donasi_data']['tujuan_donasi']) && $_SESSION['donasi_data']['tujuan_donasi'] == $row['tujuan_donasi']) ? 'selected' : '';
                            echo '<option ' . $sel . ' value="' . htmlspecialchars($row['tujuan_donasi']) . '">' . htmlspecialchars($row['tujuan_donasi']) . '</option>';
                        }
                    }
                    // Add default options if not already present
                    $defaults = ['BANTUAN PENDIDIKAN', 'BANTUAN KESEHATAN', 'BANTUAN SOSIAL', 'ANAK YATIM', 'PANTI ASUHAN', 'PEMBANGUNAN MASJID'];
                    foreach ($defaults as $default) {
                        if (!in_array($default, $options)) {
                            $sel = (isset($_SESSION['donasi_data']['tujuan_donasi']) && $_SESSION['donasi_data']['tujuan_donasi'] == $default) ? 'selected' : '';
                            echo '<option ' . $sel . ' value="' . htmlspecialchars($default) . '">' . htmlspecialchars($default) . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>

            <button type="submit" name="donate" class="submit-btn">Konfirmasi</button>
        </form>

        <div class="login-link">
            <a href="dashboard.php" class="link-text">Kembali</a>
        </div>
    </div>

    <script>
        const namaInput = document.getElementById('nama');
        const anonimCheckbox = document.getElementById('is_anonim');
        const originalName = namaInput.value;

        anonimCheckbox.addEventListener('change', function () {
            if (this.checked) {
                namaInput.value = "****";
                namaInput.disabled = true;
                namaInput.style.backgroundColor = "#f0f0f0";
                namaInput.style.color = "#666";
            } else {
                namaInput.value = originalName;
                namaInput.disabled = false;
                namaInput.style.backgroundColor = "";
                namaInput.style.color = "";
            }
        });
    </script>
</body>
</html>
