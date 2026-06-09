<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("location:login.php");
    exit;
}

$user_aktif = $_SESSION['username']; 
$query_admin = mysqli_query($koneksi, "SELECT * FROM pustakawan WHERE username='$user_aktif'");
$admin = mysqli_fetch_assoc($query_admin);

if (!$admin) {
    $admin = ['nama_pustakawan' => 'Admin', 'kode_petugas' => '-', 'no_telp' => '-', 'username' => $user_aktif, 'foto' => ''];
}

$foto_tampil = (!empty($admin['foto']) && file_exists("img/admin/" . $admin['foto'])) ? "img/admin/" . $admin['foto'] : "default_petugas.png";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Admin - Widya Graha</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root { 
            --navy-dark: #1a3a5f;--shadow: 0 4px 15px rgba(0,0,0,0.05);
            --yellow: #ffc107;
            --logout-red: #ff5c5c;
            --shadow: 0 4px 15px rgba(0,0,0,0.05); 
        }

        /* Layout Reset */
                body { display: flex; min-height: 100vh; background-color: #f4f7fa; margin: 0; font-family: 'Segoe UI', sans-serif; }

        /* Sidebar Styling */
        .sidebar { 
            display: flex; 
            flex-direction: column; 
            height: 100vh; 
            position: fixed; 
            width: 260px;
            z-index: 1000;
            background: var(--navy-dark);
            color: white;
        }

        .sidebar-header { padding: 30px 10px; text-align: center; }
        .sidebar-header img { width: 50px; margin-bottom: 8px; }
        .sidebar-header h2 { font-size: 14px; font-weight: 800; margin: 0; color: white; letter-spacing: 1px; }
        .sidebar-header span { font-size: 10px; color: var(--yellow); font-weight: 600; text-transform: uppercase; }

        .sidebar-nav { flex: 1; display: flex; flex-direction: column; padding: 10px; }
        .sidebar-menu { display: flex; flex-direction: column; height: 100%; list-style: none; padding: 0; margin: 0; }
        
        .menu-item a { 
            display: flex; align-items: center; padding: 12px 15px; 
            color: #a0aec0; text-decoration: none; font-size: 13px;
            font-weight: 500; transition: 0.2s;
        }
        .menu-item i { width: 30px; font-size: 16px; text-align: center; }

        /* Status Aktif */
        .menu-item.active a { 
            background: var(--navy-light); 
            color: var(--yellow) !important; 
            position: relative;
            border-radius: 0 8px 8px 0;
        }
        .menu-item.active a::before {
            content: ""; position: absolute; left: 0; top: 0; bottom: 0; 
            width: 3px; background: var(--yellow); 
        }

        /* Logout Tanpa Kotak */
        .menu-item.logout { 
            margin-top: auto; 
            border-top: 1px solid rgba(255,255,255,0.1); 
            padding-top: 10px; 
            margin-bottom: 20px; 
        }
        .menu-item.logout a { color: var(--logout-red); font-weight: 700; }
        .menu-item.logout a:hover { color: white; background: var(--logout-red); border-radius: 8px; }

        /* Main Content */
        .main-content { 
            margin-left: 260px; 
            width: calc(100% - 260px); 
            height: 100vh;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
        }

        .content-body { padding: 25px; flex: 1; }
        
        .card-custom { 
            border: none; 
            border-radius: 15px; 
            box-shadow: var(--shadow); 
            background: white; 
            padding: 30px; 
            width: 100%;
        }

        label { font-weight: 700; color: var(--navy-dark); font-size: 11px; text-transform: uppercase; margin-bottom: 8px; display: block; }
        .form-control { border-radius: 10px; background: #f8f9fa; border: 1px solid #eee; padding: 12px; font-size: 14px; }
        .btn-simpan { background: var(--navy-dark); color: white; padding: 12px 30px; border-radius: 10px; font-weight: 700; border: none; cursor: pointer; }

        footer { text-align: center; padding: 20px 0; font-size: 12px; color: #888; background: #fff; border-top: 1px solid #eee; }

        @media (max-width: 992px) {
            .sidebar { width: 80px; }
            .sidebar-header h2, .sidebar-header span, .menu-item span { display: none; }
            .main-content { margin-left: 80px; width: calc(100% - 80px); }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <img src="Logoperpus.png" alt="Logo">
        <h2>PERPUSTAKAAN</h2>
        <span>Widya Graha</span>
    </div>

    <div class="sidebar-nav">
        <ul class="sidebar-menu">
            <li class="menu-item"><a href="admin.php"><i class="fas fa-th-large"></i> <span>Dashboard</span></a></li>
            <li class="menu-item"><a href="buku.php"><i class="fas fa-book"></i> <span>Koleksi Buku</span></a></li>
            <li class="menu-item"><a href="data_ebook.php"><i class="fas fa-file-pdf"></i> <span>Data E-Book</span></a></li>
            <li class="menu-item"><a href="anggota.php"><i class="fas fa-users"></i> <span>Data Anggota</span></a></li>
            <li class="menu-item"><a href="transaksi.php"><i class="fas fa-exchange-alt"></i> <span>Transaksi</span></a></li>
            <li class="menu-item"><a href="peraturan.php"><i class="fas fa-gavel"></i> <span>Kelola Peraturan</span></a></li>
            <li class="menu-item active"><a href="profil_admin.php"><i class="fas fa-user-shield"></i> <span>Profil Admin</span></a></li>

            <!-- Keluar Tanpa Kotak -->
            <li class="menu-item logout">
                <a href="logout.php" onclick="return confirm('Yakin ingin keluar?')">
                    <i class="fas fa-power-off"></i> <span>Keluar</span>
                </a>
            </li>
        </ul>
    </div>
</div>

<div class="main-content">
    <div class="content-body">
        <div class="mb-4">
            <small class="text-muted">Panel Admin / <b>Profil Admin</b></small>
        </div>

        <div class="card-custom">
            <h5 class="font-weight-bold mb-4" style="color: var(--navy-dark);">IDENTITAS ADMIN</h5>
            
            <form action="proses_update_admin.php" method="POST" enctype="multipart/form-data">
                <div class="row align-items-center mb-5">
                    <div class="col-auto">
                        <img src="<?= $foto_tampil; ?>?t=<?= time(); ?>" 
                             style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 4px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
                    </div>
                    <div class="col">
                        <label style="text-transform: none;">Ganti Foto Profil</label>
                        <input type="file" name="foto" class="form-control-file">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama_pustakawan" class="form-control" value="<?= htmlspecialchars($admin['nama_pustakawan']); ?>">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Username</label>
                        <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($admin['username']); ?>" readonly>
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Nomor Telepon</label>
                        <input type="text" name="no_telp" class="form-control" value="<?= htmlspecialchars($admin['no_telp']); ?>">
                    </div>
                    <div class="col-md-6 form-group">
    <label>Ganti Password</label>
    <!-- Atribut name="password_baru" sangat penting untuk ditangkap di file proses -->
    <input type="password" name="password_baru" class="form-control" placeholder="Kosongkan jika tidak diganti">
    <small class="text-muted">Isi hanya jika ingin merubah password.</small>
</div>
                </div>

                <div class="text-right mt-4">
                    <button type="submit" class="btn btn-simpan">SIMPAN PERUBAHAN</button>
                </div>
            </form>
        </div>
    </div>

    <footer>&copy; 2026 Perpustakaan Widya Graha</footer>
</div>

</body>
</html>