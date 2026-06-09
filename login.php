<?php
session_start();
include 'koneksi.php';

$error_msg = "";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($koneksi, trim($_POST['username']));
    $password = mysqli_real_escape_string($koneksi, trim($_POST['password']));
    $role     = $_POST['role'];

    if ($role == "admin") {
        // Login Admin ke tabel pustakawan
        $query = mysqli_query($koneksi, "SELECT * FROM pustakawan WHERE username='$username' AND password='$password'");
        if (mysqli_num_rows($query) > 0) {
            $data = mysqli_fetch_assoc($query);
            $_SESSION['username'] = $data['username'];
            $_SESSION['nama']     = $data['nama_pustakawan'];
            $_SESSION['role']     = "admin";
            header("location:admin.php");
            exit;
        } else {
            $error_msg = "Username atau Password Admin salah!";
        }
    } else {
        // LOGIN SISWA SESUAI TABEL ANGGOTA
        // Kita cari berdasarkan kolom 'nis' dan 'password'
        $query = mysqli_query($koneksi, "SELECT * FROM anggota WHERE nis='$username' AND password='$password' AND role='siswa'");
        
        if (mysqli_num_rows($query) > 0) {
            $data = mysqli_fetch_assoc($query);
            $_SESSION['username'] = $data['nis'];
            $_SESSION['nama']     = $data['nama_anggota'];
            $_SESSION['role']     = "siswa";
            header("location:siswa.php");
            exit;
        } else {
            // Jika gagal, berikan pesan error yang jelas
            $error_msg = "NIS atau Password tidak cocok dengan data di tabel Anggota!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Widya Graha</title>
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="login-container">
    <div class="login-image">
        <div class="bg-text-login">
            <h1>Widya Graha</h1>
            <p>Buka Buku, Buka Dunia</p>
            <span style="opacity:0.8;">Sistem Informasi Perpustakaan Digital</span>
        </div>
    </div>

    <div class="login-form-section">
        <form method="post" action="">
            <div class="form-header-login">
                <img src="Logoperpus.png" class="form-logo-login" alt="Logo">
                <h2>Selamat Datang</h2>
                <p style="color: #666; font-size: 14px;">Masuk ke akun Perpustakaan</p>
            </div>

            <?php if ($error_msg != ""): ?>
                <div style="background:#f8d7da; color:#721c24; padding:10px; border-radius:5px; margin-bottom:15px; font-size:13px; text-align:center; border:1px solid #f5c6cb;">
                    <i class="fas fa-exclamation-circle"></i> <?= $error_msg; ?>
                </div>
            <?php endif; ?>

            <div class="input-group">
                <i class="fas fa-users-cog"></i>
                <select name="role" id="role" onchange="ubahLabel()" required>
                    <option value="admin">Login Sebagai Pustakawan</option>
                    <option value="siswa" selected>Login Sebagai Siswa</option>
                </select>
            </div>

            <div class="input-group">
                <i class="fas fa-id-card" id="iconUser"></i>
                <input type="text" name="username" id="userInput" placeholder="Masukkan NIS Siswa" required>
            </div>

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit" name="login" class="btn-login-custom">MASUK SEKARANG</button>

            <div class="text-center mt-3" style="text-align: center; margin-top: 15px;">
                <p style="font-size: 14px; color: #666;">
                    Belum punya akun? <a href="register.php" style="color: #1a3a5f; font-weight: 700; text-decoration: none;">Daftar</a>
                </p>
            </div>
        </form>
    </div>
</div>

<script>
    function ubahLabel() {
        var role = document.getElementById("role").value;
        var userInput = document.getElementById("userInput");
        var iconUser = document.getElementById("iconUser");
        
        if (role === "siswa") {
            userInput.placeholder = "Masukkan NIS Siswa";
            iconUser.className = "fas fa-id-card";
        } else {
            userInput.placeholder = "Username Admin";
            iconUser.className = "fas fa-user";
        }
    }
</script>

</body>
</html>