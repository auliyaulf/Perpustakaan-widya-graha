<?php
session_start();
include 'koneksi.php';

// 1. Proteksi Halaman
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'siswa') {
    header("location:login.php");
    exit;
}

// 2. Ambil Data User (NIS dari session)
$username_session = $_SESSION['username'];
$query_user = mysqli_query($koneksi, "SELECT * FROM anggota WHERE nis='$username_session'");
$user = mysqli_fetch_assoc($query_user);

// 3. Logika Pemanggilan Foto
$foto_db = $user['foto']; 
$folder_path = "img/siswa/";
$foto_tampil = (!empty($foto_db) && file_exists($folder_path . $foto_db)) ? $folder_path . $foto_db : "aku.jpg";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Widya Graha</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="wiga_siswa.css">

    <style>
        :root { 
         --navy: #1a3a5f; --yellow: #ffc107 ; --danger: #f44336; 
        }
        
        body { 
            font-family: 'Poppins', sans-serif; 
            background: #f4f7f6 !important; 
        }

        /* --- SIDEBAR CUSTOM --- */
        .sidebar { 
            width: 250px; 
            background: var(--navy); 
            height: 100vh; 
            position: fixed; 
            left: 0; 
            top: 0; 
            z-index: 1000; 
        }

        .sidebar-header { 
            padding: 30px 15px; 
            text-align: center; 
        }

        .sidebar-header img {
            filter: drop-shadow(0 4px 5px rgba(0,0,0,0.2));
        }

        .sidebar-header h2 { 
            font-size: 16px; 
            font-weight: 800; 
            color: white; 
            margin: 10px 0 0; 
            letter-spacing: 1px;
        }

        .sidebar-header span { 
            font-size: 12px; 
            color: #ccc; 
            display: block;
            color: #ffc107 !important;
            font-size: 12px;
            display: block;
        }

        .sidebar-menu { 
            list-style: none; 
            padding: 0; 
            margin-top: 20px; 
        }

        .menu-item a { 
            display: flex; 
            align-items: center; 
            padding: 13px 25px; 
            color: rgba(255,255,255,0.7); 
            text-decoration: none; 
            font-size: 14px;
            transition: 0.3s;
            border-left: 4px solid transparent; /* Garis default transparan */
        }

        .menu-item i { 
            margin-right: 12px; 
            width: 20px; 
            text-align: center; 
            font-size: 16px;
        }
        
        /* Hanya satu garis kuning di sebelah kiri saat aktif */
        .menu-item.active a { 
            background: rgba(255,255,255,0.1); 
            color: var(--yellow); 
            font-weight: 600; 
            border-left: 4px solid var(--yellow); 
        }

        .menu-item a:hover { 
            color: white; 
            background: rgba(255,255,255,0.05); 
        }

        /* Menghapus garis horizontal pemisah di atas tombol keluar */
        .menu-item.logout { 
            margin-top: 5px; 
        }

        .menu-item.logout a { 
            color: #ff6b6b; 
            border-top: none !important; 
        }

        /* --- CONTENT AREA --- */
        .main-content { 
            margin-left: 250px; 
            padding: 30px 45px; 
        }

        .card-profil {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border: 1px solid #eee;
        }

        label { 
            font-weight: 700; 
            color: var(--navy); 
            font-size: 11px; 
            text-transform: uppercase; 
            margin-bottom: 8px; 
            display: block; 
        }

        .form-control-custom { 
            border-radius: 10px; 
            border: 1px solid #ddd; 
            padding: 12px 15px; 
            font-size: 14px; 
        }

        .btn-simpan { 
            background: var(--navy); 
            color: white; 
            padding: 12px 35px; 
            border-radius: 10px; 
            font-weight: 700; 
            border: none; 
            transition: 0.3s; 
        }

        .btn-simpan:hover { 
            background: #142d4a; 
            transform: translateY(-2px); 
            box-shadow: 0 5px 15px rgba(0,0,0,0.1); 
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <img src="Logoperpus.png" alt="Logo" class="logo-custom" width="55">
        <h2 style="font-size: 16px; color:white; margin-top:10px;">PERPUSTAKAAN</h2>
                <span style="font-size: 12px; color:#ffc107 !important;">Widya Graha</span>

    </div>

    <ul class="sidebar-menu">
        <li class="menu-item"><a href="siswa.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
        <li class="menu-item"><a href="katalog_siswa.php"><i class="fas fa-book-open"></i> Koleksi Buku</a></li>
        <li class="menu-item"><a href="siswa_ebook.php"><i class="fas fa-file-pdf"></i> Koleksi E-Book</a></li>
        <li class="menu-item"><a href="daftar_peraturan.php"><i class="fas fa-gavel"></i> Peraturan</a></li>
        <li class="menu-item active"><a href="profil_siswa.php"><i class="fas fa-user-circle"></i> Profil</a></li>
        <li class="menu-item logout"><a href="logout.php" onclick="return confirm('Yakin ingin keluar?')"><i class="fas fa-power-off"></i> Keluar</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="top-bar d-flex justify-content-between align-items-center mb-4">
        <span class="text-muted small">Panel Siswa / <b>Profil Saya</b></span>
    </div>

    <div class="card-profil">
        <h4 class="mb-5" style="font-weight: 800; color: var(--navy);">DATA PROFIL SISWA</h4>
        
        <form action="proses_update_profil.php" method="POST" enctype="multipart/form-data">
            <div class="row align-items-center mb-5">
                <div class="col-auto">
                    <img src="<?= $foto_tampil; ?>" style="width: 110px; height: 110px; border-radius: 50%; object-fit: cover; border: 4px solid #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                </div>
                <div class="col">
                    <label style="text-transform: none;">Ubah Foto Profil</label>
                    <input type="file" name="foto" class="form-control-file mb-1">
                    <small class="text-muted">File: <b><?= htmlspecialchars($foto_db); ?></b></small>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama_anggota" class="form-control form-control-custom" value="<?= htmlspecialchars($user['nama_anggota']); ?>">
                </div>
                <div class="col-md-6 form-group">
                    <label>NIS</label>
                    <input type="text" class="form-control form-control-custom bg-light" value="<?= $user['nis']; ?>" readonly>
                </div>
                <div class="col-md-6 form-group">
                    <label>No. WhatsApp</label>
                    <input type="text" name="no_telp" class="form-control form-control-custom" value="<?= $user['no_telp']; ?>">
                </div>
                <div class="col-md-6 form-group">
                    <label>Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-control form-control-custom">
                        <option value="Laki-laki" <?= ($user['jenis_kelamin'] == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                        <option value="Perempuan" <?= ($user['jenis_kelamin'] == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                    </select>
                </div>
                <div class="col-md-12 form-group">
                    <label>Alamat Lengkap</label>
                    <textarea name="alamat" class="form-control form-control-custom" rows="3"><?= htmlspecialchars($user['alamat']); ?></textarea>
                </div>
            </div>

            <div class="text-right mt-4">
                <button type="submit" class="btn btn-simpan shadow-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
<div id="wiga-container" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999;">
        <div id="wiga-chatbox" style="display: none; width: 350px; height: 500px; background: #1a1a1a; border-radius: 15px; border: 1px solid #333; box-shadow: 0 10px 25px rgba(0,0,0,0.5); flex-direction: column; overflow: hidden; margin-bottom: 15px;">
            <div style="background: linear-gradient(135deg, #FF9800, #E65100); padding: 15px; color: white; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <strong style="display: block;">Wiga Assistant</strong>
                    <small style="font-size: 10px; opacity: 0.8;">Pustakawan Widya Graha</small>
                </div>
                <button onclick="toggleWiga()" style="background: none; border: none; color: white; cursor: pointer;"><i class="fas fa-times"></i></button>
            </div>
            
            <div id="ai-chat-isi" style="flex: 1; padding: 15px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px; background: #121212;">
                <div style="background: #333; color: #ddd; padding: 10px; border-radius: 0 10px 10px 10px; font-size: 13px; max-width: 80%;">
                    Halo! Saya Wiga. Cari buku apa hari ini? Ketik saja judul atau temanya.
                </div>
            </div>

            <div style="padding: 10px; background: #1a1a1a; border-top: 1px solid #333; display: flex; gap: 5px;">
                <input type="text" id="ai-input-pesan" placeholder="Tanya Wiga..." style="flex: 1; background: #222; border: 1px solid #444; color: white; padding: 8px 12px; border-radius: 20px; outline: none;">
                <button onclick="kirimChatAI()" style="background: #E65100; border: none; color: white; width: 35px; height: 35px; border-radius: 50%; cursor: pointer;">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>

    
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>