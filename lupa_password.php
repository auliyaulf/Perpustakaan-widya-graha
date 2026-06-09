<?php
include 'koneksi.php';

$step = 1; 
$error = "";
$user_id = "";

// Tahap 1: Verifikasi Akun
if (isset($_POST['cek_akun'])) {
    $username = trim(mysqli_real_escape_string($koneksi, $_POST['username']));
    $nama     = trim(mysqli_real_escape_string($koneksi, $_POST['nama']));

    // Mencari user yang username dan namanya cocok
    $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username' AND nama='$nama'");
    
    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        $user_id = $data['username'];
        $step = 2; 
    } else {
        $error = "Data tidak ditemukan. Pastikan NIS dan Nama Lengkap sesuai.";
    }
}

// Tahap 2: Proses Update Password
if (isset($_POST['reset_pass'])) {
    $user_id = $_POST['user_id'];
    $pass_baru = trim(mysqli_real_escape_string($koneksi, $_POST['password_baru']));
    
    // Update password di database menggunakan MD5 agar sinkron dengan login
    $update = mysqli_query($koneksi, "UPDATE users SET password=MD5('$pass_baru') WHERE username='$user_id'");
    
    if ($update) {
        echo "<script>alert('Password berhasil diperbarui! Silakan login kembali.'); window.location='login.php';</script>";
    } else {
        $error = "Gagal memperbarui password.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | Widya Graha</title>
    <link rel="stylesheet" href="login.css">
    <style>
        body { background: #f4f7fa; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; font-family: 'Segoe UI', sans-serif; }
        .reset-box { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        .input-box { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; outline: none; }
        .input-box:focus { border-color: #1a3a5f; }
        .btn-reset { width: 100%; padding: 12px; background: #1a3a5f; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; transition: 0.3s; }
        .btn-reset:hover { background: #122a44; }
        .label-text { font-size: 13px; color: #555; font-weight: 600; display: block; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="reset-box">
        <div style="text-align: center; margin-bottom: 25px;">
            <img src="Logoperpus.png" width="70" alt="Logo">
            <h3 style="color: #1a3a5f; margin-top: 15px;">Reset Password</h3>
            <p style="font-size: 13px; color: #888;">Perpustakaan Widya Graha</p>
        </div>

        <?php if($error): ?>
            <div style="color: #d9534f; background: #fdf7f7; padding: 10px; border-radius: 8px; font-size: 13px; margin-bottom: 20px; border: 1px solid #ebcccc; text-align:center;">
                <?= $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if($step == 1): ?>
            <form method="post" onsubmit="return validasiForm()">
                <label class="label-text">NIS / Username</label>
                <input type="text" name="username" id="username" class="input-box" placeholder="Masukkan NIS Anda" onkeypress="return hanyaAngka(event)" required autocomplete="off">
                
                <label class="label-text">Nama Lengkap</label>
                <input type="text" name="nama" class="input-box" placeholder="Masukkan Nama Sesuai Data" required autocomplete="off">
                
                <button type="submit" name="cek_akun" class="btn-reset">VERIFIKASI AKUN</button>
            </form>
        <?php else: ?>
            <form method="post">
                <input type="hidden" name="user_id" value="<?= $user_id ?>">
                <p style="font-size: 13px; color: #333; margin-bottom: 15px;">User: <b><?= $user_id ?></b></p>
                
                <label class="label-text">Password Baru</label>
                <input type="password" name="password_baru" class="input-box" placeholder="Ketik password baru" required autofocus>
                
                <button type="submit" name="reset_pass" class="btn-reset" style="background:#28a745;">SIMPAN PASSWORD</button>
            </form>
        <?php endif; ?>

        <p style="text-align:center; margin-top:25px;">
            <a href="login.php" style="font-size:12px; color:#1a3a5f; text-decoration:none; font-weight: 600;">Kembali ke Login</a>
        </p>
    </div>

    <script>
        // Fungsi agar NIS hanya bisa diisi angka
        function hanyaAngka(evt) {
            var charCode = (evt.which) ? evt.which : event.keyCode;
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                return false;
            }
            return true;
        }
    </script>
</body>
</html>