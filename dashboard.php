<?php
session_start();
include 'koneksi.php';

// pastikan sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$nama = $_SESSION['nama'];
$password = isset($_SESSION['password']) ? $_SESSION['password'] : null;

// cek admin login
if ($username === 'kokwet' && $password === 'admin') {
    $_SESSION['admin'] = $username;
    header("Location: dashboard_admin.php");
    exit();
}

// total donasi yang sudah dikonfirmasi
$total = mysqli_query($koneksi, "SELECT SUM(amount) as total FROM donations WHERE status='confirmed'");
$total_row = mysqli_fetch_assoc($total);
$total_donasi = !empty($total_row['total']) ? $total_row['total'] : 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard Donasi</title>
    <style>
        * {
            box-sizing: border-box;
            transition: all 0.2s ease;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f8f1f1 0%, #ffe6f0 100%);
            min-height: 100vh;
            color: #3a3a3a;
            overflow-x: hidden;
        }

        /* ===== NAVBAR ===== */
        .navbar {
            position: fixed;
            top: 0; left: 0; right: 0;
            background: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 30px;
            box-shadow: 0 4px 15px rgba(226, 117, 159, 0.15);
            border-bottom: 2px solid rgba(226, 117, 159, 0.15);
            z-index: 1000;
        }

        .navbar .brand {
            font-size: 22px;
            font-weight: 700;
            color: #e2759f;
        }

        .menu-center {
            flex: 1;
            display: flex;
            justify-content: center;
            gap: 25px;
        }

        .menu-center a {
            color: #444;
            text-decoration: none;
            font-weight: 500;
            padding: 8px 15px;
            border-radius: 10px;
            position: relative;
        }

        .menu-center a::after {
            content: "";
            position: absolute;
            bottom: 0; left: 50%;
            transform: translateX(-50%);
            width: 0%;
            height: 2px;
            background: linear-gradient(90deg, #ff99aa, #e2759f);
            transition: 0.3s ease;
        }

        .menu-center a:hover {
            color: #e2759f;
        }
        .menu-center a:hover::after {
            width: 60%;
        }

        .menu-center a.active {
            color: #e2759f;
        }
        .menu-center a.active::after {
            width: 60%;
        }

        .logout a {
            color: #fff;
            background: linear-gradient(45deg, #e2759f, #c25a8a);
            padding: 10px 22px;
            border-radius: 25px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 8px 20px rgba(226, 117, 159, 0.3);
        }

        .logout a:hover {
            transform: translateY(-2px);
            background: linear-gradient(45deg, #c25a8a, #a0446f);
            box-shadow: 0 12px 25px rgba(226, 117, 159, 0.4);
        }

        /* ===== WELCOME BOX ===== */
        .welcome-box {
            max-width: 1250px;
            margin: 120px auto 30px;
            background: #fff;
            padding: 45px;
            border-radius: 25px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
            border: 2px solid rgba(226, 117, 159, 0.15);
            text-align: center;
        }

        .welcome-box h1 {
            color: #fff;
            background: linear-gradient(45deg, #e2759f, #c25a8a);
            padding: 18px 35px;
            border-radius: 15px;
            display: inline-block;
            margin-bottom: 25px;
            font-size: 2em;
            font-weight: 700;
            box-shadow: 0 5px 15px rgba(226, 117, 159, 0.3);
        }

        .welcome-box p {
            text-align: justify;
            line-height: 1.8;
            color: #444;
            margin-bottom: 20px;
        }

        .welcome-box b {
            color: #e2759f;
        }

        .welcome-box blockquote {
            font-style: italic;
            color: #4d4d4d;
            background: rgba(226, 117, 159, 0.08);
            padding: 20px 25px;
            margin: 25px 0;
            border-left: 4px solid #e2759f;
            border-radius: 8px;
        }

        /* ===== DONATION GALLERY ===== */
        .donation-gallery {
            margin-top: 40px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            padding: 20px;
            overflow-x: auto;
            white-space: nowrap;
            box-shadow: 0 4px 15px rgba(226, 117, 159, 0.15);
            border: 1px solid rgba(226, 117, 159, 0.1);
        }

        .donation-gallery::-webkit-scrollbar {
            height: 10px;
        }
        .donation-gallery::-webkit-scrollbar-thumb {
            background: linear-gradient(90deg, #ff99aa, #e2759f);
            border-radius: 5px;
        }

        .gallery-item {
            display: inline-block;
            margin-right: 15px;
            border-radius: 15px;
            overflow: hidden;
            width: 500px;
            height: 300px;
            box-shadow: 0 5px 15px rgba(226, 117, 159, 0.15);
        }

        .gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* ===== STATS BOX ===== */
        .stats-box {
            max-width: 480px;
            margin: 50px auto 80px;
            background: #fff;
            padding: 25px;
            border-radius: 20px;
            text-align: center;
            color: #444;
            box-shadow: 0 8px 25px rgba(226, 117, 159, 0.15);
            border: 2px solid rgba(226, 117, 159, 0.15);
        }

        .stats-box span {
            display: block;
            font-size: 26px;
            font-weight: 700;
            color: #e2759f;
            margin-top: 8px;
        }

        @media (max-width: 768px) {
            .welcome-box { padding: 25px; margin: 100px 15px; }
            .welcome-box h1 { font-size: 1.6em; padding: 15px 20px; }
            .gallery-item { width: 300px; height: 180px; }
        }
    </style>
</head>

<body>
    <div class="navbar">
        <div class="brand"><b>DOMS</b></div>
        <div class="menu-center">
            <a href="dashboard.php" class="active">Home</a>
            <a href="tambah_donasi.php">Tambah Donasi</a>
            <a href="data_donasi.php">Data Donasi</a>
             <a href="target.php">Target</a>
            <a href="tentang.php">Tentang Kami</a>
        </div>
        <div class="logout">
            <a href="logout.php" onclick="return confirm('Yakin ingin logout?');">Logout</a>
        </div>
    </div>

    <div class="welcome-box">
        <h1>Selamat datang, <?php echo htmlspecialchars($nama); ?> 👋</h1>

        <p>Terima kasih telah bergabung dengan <b>DOMS (Donations Online Management System)</b>, sebuah platform donasi online yang dirancang khusus untuk mempermudah masyarakat dalam menyalurkan bantuan dengan cara yang cepat, aman, dan transparan. Kami berkomitmen untuk memberikan kemudahan dalam berbuat kebaikan, sehingga setiap donatur dapat berkontribusi tanpa keraguan dan dengan kepastian bahwa donasi mereka sampai tepat sasaran.</p>

        <p>Setiap rupiah yang Anda sumbangkan melalui <b>DOMS</b> bukan sekadar angka, melainkan sumber harapan yang nyata bagi mereka yang sedang mengalami masa sulit. Mulai dari korban bencana alam yang kehilangan tempat tinggal dan kebutuhan dasar, anak-anak yatim yang membutuhkan dukungan untuk masa depan mereka, hingga saudara-saudara kita di Papua yang terus berjuang menghadapi berbagai tantangan. Bantuan Anda sangat berarti untuk meringankan beban dan memberikan peluang bagi kehidupan yang lebih baik.</p>

        <p>Di <b>DOMS</b>, kami mengedepankan sistem yang transparan dan terpercaya. Setiap donasi yang masuk akan langsung dicatat secara sistematis, kemudian diverifikasi oleh tim admin kami untuk memastikan keamanan dan keabsahan transaksi. Setelah itu, donasi akan segera disalurkan ke pihak-pihak yang membutuhkan sesuai dengan tujuan yang telah ditentukan.</p>

        <blockquote> "Sedekah tidak akan mengurangi harta, melainkan menambah keberkahan dan mendekatkan kita kepada kebaikan." </blockquote>

        <p>Mari bersama-sama kita jadikan <b>DOMS</b> sebagai jembatan kebaikan yang menyatukan hati dan memberikan senyum, harapan, serta masa depan yang lebih cerah bagi banyak orang.</p>

        <div class="donation-gallery">
            <div class="gallery-item"><img src="image/bantu1.webp" alt="Korban terbantu 1"></div>
            <div class="gallery-item"><img src="image/bantu2.webp" alt="Korban terbantu 2"></div>
            <div class="gallery-item"><img src="image/bantu3.webp" alt="Korban terbantu 3"></div>
            <div class="gallery-item"><img src="image/bantu4.webp" alt="Korban terbantu 4"></div>
            <div class="gallery-item"><img src="image/bantu5.webp" alt="Korban terbantu 5"></div>
            <div class="gallery-item"><img src="image/bantu6.webp" alt="Korban terbantu 6"></div>
            <div class="gallery-item"><img src="image/bantu7.webp" alt="Korban terbantu 7"></div>
            <div class="gallery-item"><img src="image/bantu8.webp" alt="Korban terbantu 8"></div>
        </div>
    </div>

    <div class="stats-box">
        Total donasi terkumpul hingga hari ini:
        <span>Rp <?php echo number_format($total_donasi, 0, ',', '.'); ?></span>
    </div>

    <script>
        const backgrounds = [
            "url('image/g01.webp')",
            "url('image/g02.webp')",
            "url('image/g03.webp')",
            "url('image/g04.webp')"
        ];
        let index = 0;
        const body = document.body;
        body.style.backgroundImage = backgrounds[index];
        body.style.backgroundSize = "cover";
        body.style.backgroundAttachment = "fixed";
        body.style.backgroundRepeat = "no-repeat";
        setInterval(() => {
            index = (index + 1) % backgrounds.length;
            body.style.backgroundImage = backgrounds[index];
        }, 6000);
    </script>
</body>
</html>
