<?php
session_start();
include 'koneksi.php';

// pastikan user sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['id'];

// ambil data donasi
$result = mysqli_query($koneksi, "SELECT * FROM donations WHERE id='$id'");
$donasi = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $donor_name = mysqli_real_escape_string($koneksi, $_POST['donor_name']);
    $phone = mysqli_real_escape_string($koneksi, $_POST['phone']);
    $amount = (float) $_POST['amount'];

    $query = "UPDATE donations SET donor_name='$donor_name', phone='$phone', amount='$amount' WHERE id='$id'";
    mysqli_query($koneksi, $query);

    header("Location: admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Donasi</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #e2759f;
        }

        input {
            width: 100%;
            padding: 14px 16px;
            border-radius: 15px;
            border: 2px solid #f8bbd9;
            font-size: 15px;
            transition: all 0.3s ease;
        }

        input:focus {
            outline: none;
            border-color: #e2759f;
            box-shadow: 0 0 0 3px rgba(226, 117, 159, 0.15);
        }

        .btn-container {
            margin-top: 28px;
            text-align: center;
        }

        .btn-save {
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
            margin-bottom: 15px;
            display: block;
            width: 100%;
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(226, 117, 159, 0.4);
            background: linear-gradient(45deg, #c25a8a, #a0446f);
        }

        .btn-back {
            color: #e2759f;
            text-decoration: none;
            font-weight: 500;
            padding: 10px 20px;
            border: 2px solid #e2759f;
            border-radius: 20px;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .btn-back:hover {
            background: #e2759f;
            color: white;
            transform: translateY(-1px);
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Edit Donasi</h2>
        <form method="POST">
            <div class="form-group">
                <label for="donor_name">Nama Donatur</label>
                <input type="text" id="donor_name" name="donor_name" value="<?php echo htmlspecialchars($donasi['donor_name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="phone">No. HP</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($donasi['phone']); ?>" required>
            </div>

            <div class="form-group">
                <label for="amount">Jumlah Donasi (Rp)</label>
                <input type="number" id="amount" step="0.01" name="amount" value="<?php echo $donasi['amount']; ?>" required>
            </div>

            <div class="btn-container">
                <button type="submit" class="btn-save">Simpan Perubahan</button>
                <a href="admin.php" class="btn-back">← Kembali</a>
            </div>
        </form>
    </div>
</body>
</html>
