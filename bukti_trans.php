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

$username = $_SESSION['username'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['bukti_transfer']) && $_FILES['bukti_transfer']['error'] === UPLOAD_ERR_OK) {
        $bukti_nama = $_FILES['bukti_transfer']['name'];
        $bukti_tmp  = $_FILES['bukti_transfer']['tmp_name'];
        $target_dir = __DIR__ . "/uploads/"; // absolute path lebih aman

        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $ext = strtolower(pathinfo($bukti_nama, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png'];

        // Validasi ekstensi
        if (!in_array($ext, $allowed)) {
            $error = "Format file tidak didukung (hanya JPG, JPEG, PNG).";
        } else {
            // Validasi ukuran (5MB)
            if ($_FILES["bukti_transfer"]["size"] > 5 * 1024 * 1024) {
                $error = "Ukuran file terlalu besar, maksimal 5MB.";
            } else {
                // Cek apakah file benar gambar
                $check = @getimagesize($bukti_tmp);
                if ($check === false) {
                    $error = "File yang diunggah bukan gambar.";
                } else {
                    // buat nama file unik
                    $safe_basename = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($bukti_nama, PATHINFO_FILENAME));
                    $unique_name = $safe_basename . '_' . time() . '_' . mt_rand(1000,9999) . '.' . $ext;
                    $target_file = $target_dir . $unique_name;

                    if (!move_uploaded_file($bukti_tmp, $target_file)) {
                        $error = "Terjadi kesalahan saat memindahkan file.";
                    } else {
                        // update tabel donations: pakai donation_id jika ada, kalau tidak cari donasi pending terakhir milik user
                        $donation_id = null;
                        if (!empty($_SESSION['donation_id'])) {
                            $donation_id = (int) $_SESSION['donation_id'];
                        } else {
                            // cari id donasi terakhir berstatus Pending milik user
                            $q = $koneksi->prepare("SELECT id FROM donations WHERE username = ? AND status LIKE 'Pending' ORDER BY created_at DESC LIMIT 1");
                            if ($q) {
                                $q->bind_param('s', $username);
                                $q->execute();
                                $q->bind_result($found_id);
                                if ($q->fetch()) {
                                    $donation_id = (int) $found_id;
                                }
                                $q->close();
                            }
                        }

                        if ($donation_id === null || $donation_id === 0) {
                            // gagal menemukan donation id -> set error and remove uploaded file
                            @unlink($target_file);
                            $error = "Tidak dapat menemukan data donasi untuk menyimpan bukti. Silakan hubungi admin jika masalah berlanjut.";
                        } else {
                            // update kolom bukti_transfer di donations untuk id yang benar
                            $upd = $koneksi->prepare("UPDATE donations SET bukti_transfer = ? WHERE id = ?");
                            if (!$upd) {
                                @unlink($target_file);
                                $error = "Gagal menyiapkan update donations: " . mysqli_error($koneksi);
                            } else {
                                $upd->bind_param('si', $unique_name, $donation_id);
                                if (!$upd->execute()) {
                                    @unlink($target_file);
                                    $error = "Gagal menyimpan bukti transfer ke database (donations).";
                                } else {
                                    $upd->close();

                                    // update tabel transaksi jika ada record dengan id_donation = $donation_id
                                    $upd2 = $koneksi->prepare("UPDATE transaksi SET bukti_transfer = ? WHERE id_donation = ?");
                                    if ($upd2) {
                                        $upd2->bind_param('si', $unique_name, $donation_id);
                                        $upd2->execute();
                                        $upd2->close();
                                    }
                                    
                                    // opsional: hapus session donation_id karena sudah selesai proses unggah
                                    if (isset($_SESSION['donation_id'])) {
                                        unset($_SESSION['donation_id']);
                                    }

                                    $success = "Bukti transfer berhasil diunggah!";
                                    // Redirect ke data_donasi agar user lihat status (status masih 'Pending' sampai admin konfirmasi)
                                    header("Location: data_donasi.php");
                                    exit;
                                }
                            }
                        }
                    }
                }
            }
        }
    } else {
        $error = "Harap pilih file bukti transfer.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<title>Upload Bukti Transfer</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
body { background: linear-gradient(135deg,#f8f1f1 0%,#ffe6f0 100%); min-height:100vh; display:flex; justify-content:center; align-items:center; padding:20px; }
.container { background:#fff; padding:40px 30px; border-radius:25px; box-shadow:0 15px 40px rgba(0,0,0,0.15); width:100%; max-width:500px; border:2px solid rgba(226,117,159,0.2); animation:fadeIn .6s ease; }
h2 { text-align:center; color:#e2759f; font-size:26px; margin-bottom:25px; font-weight:700; }
.message { padding:12px 20px; border-radius:10px; text-align:center; margin-bottom:25px; font-size:15px; box-shadow:0 4px 15px rgba(0,0,0,0.1); }
.success { background:#e8f5e9; color:#2e7d32; border-left:6px solid #2e7d32; }
.error { background:#ffebee; color:#c62828; border-left:6px solid #c62828; }
label { display:block; margin-bottom:8px; font-weight:600; color:#e2759f; }
input[type="file"] { width:100%; padding:14px 16px; border-radius:15px; border:2px solid #f8bbd9; background:#fff; font-size:15px; transition:all .3s ease; }
input[type="file"]:focus { outline:none; border-color:#e2759f; box-shadow:0 0 0 3px rgba(226,117,159,0.15); }
.preview-container { margin-top:20px; text-align:center; }
#preview { display:none; width:100%; max-height:250px; border-radius:15px; border:2px dashed #f8bbd9; margin-top:10px; object-fit:contain; box-shadow:0 6px 15px rgba(226,117,159,0.2); }
.btn-upload { background: linear-gradient(45deg,#e2759f,#c25a8a); color:white; padding:15px 40px; border:none; border-radius:25px; font-size:18px; font-weight:600; cursor:pointer; transition:all .3s ease; box-shadow:0 8px 20px rgba(226,117,159,0.3); width:100%; margin-top:25px; }
.btn-upload:hover { transform: translateY(-2px); box-shadow:0 12px 25px rgba(226,117,159,0.4); background: linear-gradient(45deg,#c25a8a,#a0446f); }
.link-back { text-align:center; margin-top:20px; font-size:14px; }
.link-text { color:#e2759f; text-decoration:none; font-weight:600; }
.link-text:hover { border-bottom:1px solid #e2759f; }
@keyframes fadeIn { from { opacity:0; transform:translateY(10px);} to { opacity:1; transform:translateY(0);} }
</style>
</head>
<body>

<div class="container">
    <h2>Upload Bukti Transfer</h2>

    <?php if (!empty($success)): ?>
        <div class="message success"><?= htmlspecialchars($success) ?></div>
    <?php elseif (!empty($error)): ?>
        <div class="message error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label for="bukti_transfer">Pilih Bukti Transfer (JPG / JPEG / PNG)</label>
        <input type="file" id="bukti_transfer" name="bukti_transfer" accept=".jpg,.jpeg,.png" required>
        <div class="preview-container">
            <img id="preview" alt="Preview Bukti Transfer" />
        </div>
        <button type="submit" class="btn-upload">Kirim</button>
        <div class="link-back">
            <a href="review_donasi.php" class="link-text">Kembali</a>
        </div>
    </form>
</div>

<script>
const fileInput = document.getElementById('bukti_transfer');
const previewImg = document.getElementById('preview');

fileInput.addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.style.display = 'block';
            previewImg.src = e.target.result;
        }
        reader.readAsDataURL(file);
    } else {
        previewImg.style.display = 'none';
        previewImg.src = '';
    }
});
</script>

</body>
</html>
