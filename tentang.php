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

// cek admin login khusus
if ($username === 'kokwet' && $password === 'admin') {
    $_SESSION['admin'] = $username;
    header("Location: dashboard_admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Tentang Kami - DOMS</title>
    <style>
/* ==== GLOBAL STYLE ==== */
* {
    box-sizing: border-box;
    transition: all 0.2s ease;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #f8f1f1 0%, #ffe6f0 100%);
    min-height: 100vh;
    color: #3a3a3a;
    overflow-x: hidden;
}

/* ==== NAVBAR ==== */
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

/* ==== RESPONSIVE ==== */
@media (max-width: 768px) {
    .navbar {
        flex-wrap: wrap;
        padding: 10px 18px;
    }

    .navbar .menu-center {
        flex-basis: 100%;
        justify-content: center;
        gap: 12px;
        margin-top: 10px;
    }
}

@media (max-width: 480px) {
    .navbar .logout a .user-info {
        display: none;
    }
}

/* ==== CONTENT ==== */
.container {
    max-width: 1300px;
    margin: 110px auto 30px;
    background: #fff;
    padding: 45px;
    border-radius: 20px;
    box-shadow: 0 5px 25px rgba(226, 117, 159, 0.1);
    text-align: center;
    line-height: 1.7;
    border: 1px solid rgba(226, 117, 159, 0.15);
}

h2 {
    color: #e2759f;
    text-align: center;
    margin-bottom: 30px;
    font-size: 32px;
    font-weight: 700;
}

p {
    color: #444;
    line-height: 1.8;
    margin-bottom: 20px;
    text-align: justify;
}

b {
    color: #e2759f;
}

blockquote {
    font-style: italic;
    color: #4d4d4d;
    background: rgba(226, 117, 159, 0.08);
    padding: 20px 25px;
    margin: 25px 0;
    border-left: 4px solid #e2759f;
    border-radius: 8px;
}

ul {
    margin: 20px 0;
    padding-left: 20px;
    color: #444;
    text-align: left;
}

li {
    margin-bottom: 10px;
}

@media (max-width: 768px) {
    .container {
        margin: 130px 15px 20px;
        padding: 35px;
    }

    h2 {
        font-size: 28px;
    }
}

@media (max-width: 480px) {
    .container {
        padding: 25px;
    }

    h2 {
        font-size: 24px;
    }
}
    </style>
</head>

<body>
    <!-- NAVBAR -->
    <div class="navbar">
        <div class="brand"><b>DOMS</b></div>
        <div class="menu-center">
            <a href="dashboard.php">Home</a>
            <a href="tambah_donasi.php">Tambah Donasi</a>
            <a href="data_donasi.php">Data Donasi</a>
            <a href="target.php">Target</a>
            <a href="tentang.php" class="active">Tentang Kami</a>
        </div>
        <div class="logout">
            <a href="logout.php" onclick="return confirm('Yakin ingin logout?');">
    Logout
</a>

        </div>
    </div>

    <!-- CONTENT -->
    <div class="container">
        <h2>Tentang DOMS</h2>

        <p>Selamat datang di <b>DOMS (Donations Online Management System)</b>, platform donasi online terpercaya yang dirancang khusus untuk memudahkan masyarakat Indonesia dalam menyalurkan bantuan secara digital. Kami hadir sebagai jembatan antara kebaikan hati Anda dengan mereka yang membutuhkan, memastikan setiap rupiah yang disumbangkan sampai tepat sasaran dengan proses yang transparan dan akuntabel.</p>

        <p>Di era digital ini, berbagi kebaikan tidak lagi harus ribet. Melalui <b>DOMS</b>, Anda dapat berdonasi kapan saja dan di mana saja hanya dengan beberapa klik. Sistem kami dirancang dengan standar keamanan tinggi, menggunakan teknologi enkripsi modern untuk melindungi data pribadi dan transaksi Anda. Kami berkomitmen penuh terhadap transparansi, di mana setiap donasi yang masuk akan dicatat secara real-time dan dapat dipantau perkembangannya.</p>

        <p>Platform ini lahir dari visi untuk menciptakan ekosistem donasi yang inklusif dan berkelanjutan. Kami percaya bahwa setiap individu memiliki potensi untuk berkontribusi dalam membangun masyarakat yang lebih baik. Oleh karena itu, <b>DOMS</b> tidak hanya menyediakan platform untuk berdonasi, tetapi juga membangun komunitas dermawan yang saling terinspirasi untuk terus berbagi.</p>

        <h2>Visi & Misi Kami</h2>
        <p><b>Visi:</b> Menjadi platform donasi online terdepan di Indonesia yang mampu menghubungkan jutaan hati yang ingin berbagi dengan mereka yang membutuhkan, menciptakan dampak sosial yang nyata dan berkelanjutan.</p>

        <p><b>Misi:</b></p>
        <ul>
            <li>Menyediakan platform donasi yang mudah, aman, dan transparan bagi seluruh masyarakat Indonesia</li>
            <li>Membangun sistem manajemen donasi yang efisien untuk memastikan setiap bantuan sampai tepat sasaran</li>
            <li>Mendorong budaya filantropi digital melalui edukasi dan kampanye kesadaran sosial</li>
            <li>Berkolaborasi dengan berbagai pihak untuk memperluas jangkauan program-program kemanusiaan</li>
            <li>Menjaga kepercayaan pengguna melalui standar operasional yang tinggi dan pelaporan yang akurat</li>
        </ul>

        <h2>Fitur Unggulan DOMS</h2>
        <ul>
            <li><b>Formulir Donasi Pintar:</b> Interface yang intuitif memungkinkan Anda mengisi data donasi dalam hitungan detik, dengan opsi donasi anonim untuk privasi maksimal</li>
            <li><b>Dashboard Personal:</b> Pantau semua aktivitas donasi Anda dalam satu tempat, lengkap dengan status konfirmasi real-time dan riwayat lengkap</li>
            <li><b>Sistem Verifikasi Otomatis:</b> Setiap donasi melalui transfer bank akan diverifikasi secara otomatis untuk memastikan keabsahan transaksi</li>
            <li><b>Laporan Transparan:</b> Publikasi laporan penggunaan dana secara berkala, memungkinkan Anda melihat langsung dampak donasi yang telah diberikan</li>
            <li><b>Notifikasi Real-time:</b> Dapatkan update instan tentang status donasi Anda melalui email dan notifikasi di platform</li>
            <li><b>Multi-Platform Support:</b> Akses DOMS melalui desktop, tablet, atau smartphone dengan pengalaman yang konsisten</li>
        </ul>

        <h2>Program Donasi Kami</h2>
        <p><b>DOMS</b> mendukung berbagai program kemanusiaan yang mencakup:</p>
        <ul>
            <li><b>Pendidikan Papua:</b> Membantu anak-anak di Papua mendapatkan akses pendidikan yang layak dan berkualitas</li>
            <li><b>Anak Yatim & Piatu:</b> Program pendidikan, kesehatan, dan kesejahteraan untuk anak-anak yang kehilangan orang tua</li>
            <li><b>Bencana Alam:</b> Respons cepat untuk korban bencana dengan bantuan darurat dan rehabilitasi</li>
            <li><b>Kesehatan Masyarakat:</b> Dukungan untuk program kesehatan preventif dan pengobatan bagi yang membutuhkan</li>
            <li><b>Panti Asuhan:</b> Bantuan operasional dan pengembangan untuk panti asuhan di seluruh Indonesia</li>
            <li><b>Pembangunan Masjid:</b> Kontribusi untuk pembangunan dan pemeliharaan rumah ibadah</li>
        </ul>

        <h2>Keamanan & Privasi Data</h2>
        <p>Keamanan data adalah prioritas utama kami. <b>DOMS</b> menggunakan teknologi enkripsi SSL 256-bit untuk melindungi semua data yang dikirimkan melalui platform. Informasi pribadi Anda seperti nomor telepon akan disensor secara otomatis dalam sistem untuk menjaga privasi. Kami tidak pernah membagikan data pengguna kepada pihak ketiga tanpa izin, dan semua proses penyimpanan data mematuhi standar PDPA (Personal Data Protection Act).</p>

        <p>Sistem kami juga dilengkapi dengan firewall canggih dan monitoring 24/7 untuk mencegah akses tidak sah. Setiap transaksi donasi akan diverifikasi melalui sistem banking yang terintegrasi, memastikan bahwa dana yang Anda sumbangkan benar-benar aman dan terlindungi.</p>

        <h2>Tim & Komitmen Kami</h2>
        <p>Di balik <b>DOMS</b> berdiri tim profesional yang berdedikasi tinggi terhadap misi kemanusiaan. Tim kami terdiri dari developer berpengalaman, ahli keamanan siber, spesialis filantropi, dan relawan sosial yang berkomitmen untuk terus meningkatkan kualitas layanan. Kami percaya bahwa teknologi dapat menjadi alat yang powerful untuk memperbaiki dunia, dan itulah yang kami usahakan setiap hari.</p>

        <p>Kami juga bekerja sama dengan berbagai organisasi kemanusiaan terpercaya untuk memastikan bahwa setiap program donasi yang kami dukung memiliki dampak maksimal. Transparansi adalah kunci kepercayaan, oleh karena itu kami selalu terbuka untuk audit dan verifikasi dari pihak independen.</p>

        <blockquote>
            "Di DOMS, kami tidak hanya mengumpulkan donasi, tetapi juga mengumpulkan harapan. Setiap donasi adalah investasi untuk masa depan yang lebih baik bagi mereka yang membutuhkan. Mari bersama-sama membangun Indonesia yang lebih peduli dan berkeadilan."
        </blockquote>

        <p>Terima kasih telah memilih <b>DOMS</b> sebagai mitra dalam perjalanan kebaikan Anda. Bersama kita bisa membuat perbedaan yang nyata. Mari bergabung dalam gerakan ini dan jadikan hari ini sebagai awal dari perubahan yang lebih baik.</p>

        <p><b>#BersamaDOMS #DonasiBerkah #IndonesiaBerbagi</b></p>
    </div>

    <!-- BACKGROUND SLIDESHOW -->
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