<!DOCTYPE html>
<head>
    <title>Selamat Datang - Widya Graha</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { margin: 0; padding: 0; font-family: 'Segoe UI', sans-serif; background: #f0f4f8; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .portal-container { text-align: center; background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); max-width: 600px; width: 90%; }
        h1 { color: #1a3a5f; margin-bottom: 10px; }
        p { color: #666; margin-bottom: 30px; }
        .role-wrapper { display: flex; gap: 20px; justify-content: center; }
        .role-card { flex: 1; padding: 30px; border: 2px solid #eee; border-radius: 15px; text-decoration: none; transition: 0.3s; color: #1a3a5f; }
        .role-card i { font-size: 50px; margin-bottom: 15px; color: #ffc107; }
        .role-card h3 { margin: 0; }
        .role-card:hover { border-color: #1a3a5f; background: #f8f9fa; transform: translateY(-5px); }
        .role-admin:hover i { color: #ef4444; }
        .role-siswa:hover i { color: #3b82f6; }
    </style>
</head>
<body>

<div class="portal-container">
    <i class="fas fa-university" style="font-size: 40px; color: #1a3a5f; margin-bottom: 15px;"></i>
    <h1>WIDYA GRAHA</h1>
    <p>Sistem Informasi Perpustakaan Digital. Silakan pilih akses masuk:</p>

    <div class="role-wrapper">
        <a href="login.php" class="role-card role-admin">
            <i class="fas fa-user-shield"></i>
            <h3>ADMIN</h3>
            <small>Petugas Perpustakaan</small>
        </a>

        <a href="login_siswa.php" class="role-card role-siswa">
            <i class="fas fa-user-graduate"></i>
            <h3>SISWA</h3>
            <small>Anggota Perpustakaan</small>
        </a>
    </div>
</div>

</body>
</html>