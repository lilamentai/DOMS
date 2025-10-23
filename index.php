<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
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
            text-align: center;
        }

        h1 {
            text-align: center;
            color: #e2759f;
            font-size: 28px;
            margin-bottom: 25px;
            font-weight: 700;
        }

        h3 {
            color: #e2759f;
            margin-bottom: 30px;
            font-size: 20px;
        }

        .nav-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 20px;
        }

        .nav-links a {
            color: #e2759f;
            text-decoration: none;
            font-weight: 500;
            padding: 10px 20px;
            border: 2px solid #e2759f;
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .nav-links a:hover {
            background: #e2759f;
            color: white;
            transform: translateY(-1px);
        }

        .logout-btn {
            background: linear-gradient(45deg, #e2759f, #c25a8a);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(226, 117, 159, 0.3);
        }

        .logout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 25px rgba(226, 117, 159, 0.4);
            background: linear-gradient(45deg, #c25a8a, #a0446f);
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Halaman Administrator</h1>
        <h3>Selamat datang, <?php echo htmlspecialchars($_SESSION['nama']); ?> 👋</h3>

        <div class="nav-links">
            <a href="admin.php">Dashboard Admin</a>
            <a href="kelola_donasi.php">Kelola Donasi</a>
        </div>

        <button class="logout-btn" onclick="if(confirm('Yakin ingin logout?')) window.location.href='logout.php';">
            Logout
        </button>
    </div>
</body>
</html>
