<?php
include 'koneksi.php';

if (isset($_POST['register'])) {
    $nis = mysqli_real_escape_string($koneksi, $_POST['nis']);
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_anggota']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']); 
    $role = 'siswa'; // Otomatis masuk sebagai siswa

    // 1. Cek apakah NIS sudah ada di tabel anggota
    $cek = mysqli_query($koneksi, "SELECT * FROM anggota WHERE nis='$nis'");
    
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('NIS sudah terdaftar! Gunakan NIS lain atau silakan login.'); window.location='register.php';</script>";
    } else {
        // 2. Simpan ke tabel ANGGOTA (Bukan users)
        // Pastikan nama kolom di database kamu (nis, nama_anggota, password, role) sudah sesuai
        $query = mysqli_query($koneksi, "INSERT INTO anggota (nis, nama_anggota, password, role) 
                                         VALUES ('$nis', '$nama', '$password', '$role')");
        
        if ($query) {
            echo "<script>alert('Pendaftaran Berhasil! Silakan Login dengan NIS kamu.'); window.location='login.php';</script>";
        } else {
            // Jika error, akan memunculkan pesan error dari database (membantu buat debug)
            echo "<script>alert('Gagal mendaftar: " . mysqli_error($koneksi) . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun Siswa - Widya Graha</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { 
            background: #1a3a5f; 
            font-family: 'Poppins', sans-serif; 
            display: flex; 
            align-items: center; 
            height: 100vh; 
            margin: 0;
        }
        .card-register { 
            background: white; 
            border-radius: 15px; 
            padding: 30px; 
            width: 100%; 
            max-width: 400px; 
            margin: auto; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .btn-register { 
            background: #ffc107; 
            color: #1a3a5f; 
            font-weight: 700; 
            width: 100%; 
            border: none;
            padding: 12px;
            transition: 0.3s;
        }
        .btn-register:hover {
            background: #e0a800;
            color: #1a3a5f;
            transform: translateY(-2px);
        }
        .form-control { border-radius: 8px; padding: 12px; }
        label { font-weight: 600; color: #1a3a5f; font-size: 14px; }
    </style>
</head>
<body>

<div class="card-register">
    <div class="text-center mb-4">
        <img src="Logoperpus.png" width="70" alt="Logo">
        <h4 class="mt-3 font-weight-bold" style="color: #1a3a5f;">Gabung Siswa</h4>
        <p class="text-muted" style="font-size: 13px;">Daftar akun Perpustakaan Widya Graha</p>
    </div>
    
    <form action="" method="POST">
        <div class="form-group">
            <label>Nomor Induk Siswa (NIS)</label>
            <input type="text" name="nis" class="form-control" placeholder="Contoh: 222310" required>
        </div>
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="nama_anggota" class="form-control" placeholder="Nama asli kamu" required>
        </div>
        <div class="form-group">
            <label>Buat Password</label>
            <input type="password" name="password" class="form-control" placeholder="Min. 6 karakter" required>
        </div>
        <button type="submit" name="register" class="btn btn-register">DAFTAR SEKARANG</button>
        <div class="text-center mt-4">
            <p style="font-size: 13px;">Sudah punya akun? <a href="login.php" style="color: #1a3a5f; font-weight: bold;">Login di sini</a></p>
        </div>
    </form>
</div>

</body>
</html>