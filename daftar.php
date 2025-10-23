<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = md5($_POST['password']);

    // cek username unik, kecuali untuk 'admin'
    if ($username === "admin") {
        $username = "admin" . uniqid();
    }

    $checkUser = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username'");
    if (mysqli_num_rows($checkUser) > 0) {
        $_SESSION['registration_error'] = "Username sudah digunakan!";
        header("Location: daftar.php");
        exit();
    }

    // simpan user baru
    $query = mysqli_query($koneksi, "INSERT INTO users (nama, username, password)
                                     VALUES ('$nama', '$username', '$password')");

    if ($query) {
        $_SESSION['registration_success'] = "Pendaftaran berhasil, silakan login!";
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['registration_error'] = "Terjadi kesalahan, coba lagi!";
        header("Location: daftar.php");
        exit();
    }
}

// tampilkan pesan error/success
$reg_error = isset($_SESSION['registration_error']) ? $_SESSION['registration_error'] : null;
unset($_SESSION['registration_error']);

$reg_success = isset($_SESSION['registration_success']) ? $_SESSION['registration_success'] : null;
unset($_SESSION['registration_success']);
?>


<!DOCTYPE html>
<html>

<head>
    <title>Registration Page</title>
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

        .register-container {
            background: #fff;
            padding: 40px 30px;
            border-radius: 25px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
            border: 2px solid rgba(226, 117, 159, 0.2);
        }

        h3 {
            text-align: center;
            color: #e2759f;
            font-size: 26px;
            margin-bottom: 25px;
            font-weight: 700;
        }

        .success-message {
            background: linear-gradient(45deg, #a8e6cf, #88d8c0);
            color: #2d5a3d;
            padding: 12px 20px;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 25px;
            font-size: 14px;
            box-shadow: 0 4px 15px rgba(168, 230, 207, 0.3);
            border: 1px solid rgba(168, 230, 207, 0.5);
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

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #e2759f;
        }

        .password-wrapper {
            position: relative;
            width: 100%;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 14px 40px 14px 16px;
            border-radius: 15px;
            border: 2px solid #f8bbd9;
            font-size: 15px;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
            width: 24px;
            height: 24px;
            fill: #e2759f;
            user-select: none;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #e2759f;
            box-shadow: 0 0 0 3px rgba(226, 117, 159, 0.15);
        }

        .button-group {
            margin-top: 28px;
            text-align: center;
        }

        button {
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

        button:hover {
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
            .register-container {
                padding: 30px 20px;
                margin: 10px;
            }

            h3 {
                font-size: 24px;
            }

            button {
                width: 100%;
                margin-bottom: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="register-container">
        <h3>Daftar Akun</h3>

        <?php if (!empty($reg_error))
            echo "<div class='error-message'>$reg_error</div>"; ?>
        <?php if (!empty($reg_success))
            echo "<div class='success-message' 
        style='background:linear-gradient(45deg,#27ae60,#2ecc71);color:white;
        padding:12px 20px;border-radius:10px;text-align:center;margin-bottom:25px;
        font-size:14px;box-shadow:0 4px 15px rgba(46,204,113,0.3);'>
        $reg_success</div>"; ?>

        <form method="POST">
            <div class="form-group">
                <label for="nama">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" required placeholder="Masukkan nama lengkap Anda">
            </div>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required placeholder="Pilih username unik">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" required placeholder="Buat password yang kuat">
                    <svg id="togglePassword" class="toggle-password" viewBox="0 0 24 24" onclick="togglePassword()" xmlns="http://www.w3.org/2000/svg" >
                        <!-- mulai dengan ikon mata tertutup -->
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" fill="none" stroke="#e2759f" stroke-width="2"/>
                        <circle cx="12" cy="12" r="3" fill="none" stroke="#e2759f" stroke-width="2"/>
                        <line x1="2" y1="1" x2="22" y2="21" stroke="#e2759f" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                </div>
            </div>

            <div class="button-group">
                <button type="submit"> Daftar Sekarang</button>
            </div>

            <div class="login-link">
    <span style="color: #333;">Sudah punya akun? </span>
                <a class="link-text" href="login.php">Login disini</a>
</div>

        </form>
    </div>

    <script>
function togglePassword() {
    const passInput = document.getElementById('password');
    const eyeClosed = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" fill="none" stroke="#e2759f" stroke-width="2"/>
                    <circle cx="12" cy="12" r="3" fill="none" stroke="#e2759f" stroke-width="2"/>
                    <line x1="2" y1="1" x2="22" y2="21" stroke="#e2759f" stroke-width="2" stroke-linecap="round"/>`;
    const eyeOpen = `<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" fill="none" stroke="#e2759f" stroke-width="2"/>
                    <circle cx="12" cy="12" r="3" fill="none" stroke="#e2759f" stroke-width="2"/>`;

    const svg = document.getElementById('togglePassword');
    if (passInput.type === "password") {
        passInput.type = "text";
        svg.innerHTML = eyeOpen;
    } else {
        passInput.type = "password";
        svg.innerHTML = eyeClosed;
    }
}
    </script>

</body>

</html>
