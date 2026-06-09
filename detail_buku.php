<?php
session_start();
include 'koneksi.php';

// Proteksi login - Hanya Siswa
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'siswa') {
    header("location:login.php");
    exit;
}

$id_buku = isset($_GET['id']) ? mysqli_real_escape_string($koneksi, $_GET['id']) : 0;

// QUERY PERBAIKAN: Mengambil data buku sekaligus nama kategori dan nama rak
$query_sql = "SELECT buku.*, kategori.nama_kategori, rak.nama_rak 
              FROM buku 
              LEFT JOIN kategori ON buku.id_kategori = kategori.id_kategori 
              LEFT JOIN rak ON buku.id_rak = rak.id_rak 
              WHERE buku.id_buku = '$id_buku'";

$query_buku = mysqli_query($koneksi, $query_sql);
$buku = mysqli_fetch_assoc($query_buku);

if (!$buku) {
    echo "<script>alert('Buku tidak ditemukan!'); window.location='katalog_siswa.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail: <?= htmlspecialchars($buku['judul_buku']); ?> - Widya Graha</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        :root { --navy: #1a3a5f; --yellow: #ffc107; }
        
        body, .main-content { background-color: #f4f7f6 !important; }

        .top-bar {
            background: #ffffff !important;
            border-bottom: 1px solid #eee;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .breadcrumb { background-color: transparent !important; padding: 0 !important; margin: 0 !important; }

        .container-fluid-custom { width: 100%; padding: 30px 40px; }

        .back-link { 
            display: inline-flex; 
            align-items: center; 
            text-decoration: none; 
            color: #888; 
            font-size: 13px; 
            margin-bottom: 20px; 
            font-weight: 600; 
            transition: 0.3s;
        }
        .back-link:hover { color: var(--navy); text-decoration: none; }

        /* Detail Card Styling */
        .detail-card { 
            background: white !important; 
            padding: 40px; 
            border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
            display: grid; 
            grid-template-columns: 320px 1fr; 
            gap: 50px; 
            border: 1px solid #eee !important;
        }
        
        .cover-section img { 
            width: 100%; 
            border-radius: 15px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.1); 
        }
        
        .info-section h1 { font-size: 32px; font-weight: 800; color: var(--navy); margin-bottom: 5px; line-height: 1.2; }
        .info-section .penulis { font-size: 16px; color: #888; margin-bottom: 30px; display: block; }

        .meta-item { padding: 18px 0; border-bottom: 1px solid #f1f3f5; }
        .meta-item:last-child { border-bottom: none; }
        .meta-item label { display: block; font-size: 11px; color: #bbb; font-weight: 700; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 1px; }
        .meta-item .value { font-size: 15px; color: #444; font-weight: 600; line-height: 1.6; }

        .badge-kategori { background: #eef2f7; color: var(--navy); padding: 6px 16px; border-radius: 8px; font-size: 12px; font-weight: 700; }
        .badge-rak { background: #fff3cd; color: #856404; padding: 6px 16px; border-radius: 8px; font-size: 12px; font-weight: 700; }

        .stok-status { 
            display: flex; align-items: center; justify-content: center;
            padding: 15px; border-radius: 12px; font-weight: 700; margin-top: 20px; font-size: 14px;
        }
        .stok-ada { background: #eafaf1; color: #27ae60; border: 1px solid #d4efdf; }
        .stok-habis { background: #fdedec; color: #e74c3c; border: 1px solid #fadbd8; }

        .btn-booking { 
            display: inline-flex; align-items: center; justify-content: center; 
            width: 100%; padding: 18px; border-radius: 12px; background: var(--navy); 
            color: white !important; text-decoration: none; font-size: 15px; font-weight: 700; 
            transition: 0.3s; border: none; cursor: pointer; margin-top: 30px;
            box-shadow: 0 4px 15px rgba(26, 58, 95, 0.2);
        }
        .btn-booking:hover { background: var(--yellow); color: var(--navy) !important; transform: translateY(-3px); box-shadow: 0 8px 20px rgba(255, 193, 7, 0.3); }

        @media (max-width: 992px) {
            .detail-card { grid-template-columns: 1fr; padding: 30px; gap: 30px; }
            .cover-section { text-align: center; }
            .cover-section img { max-width: 280px; }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <img src="Logoperpus.png" alt="Logo" class="logo-custom">
        <h2>PERPUSTAKAAN</h2>
        <span>WIDYA GRAHA</span>
    </div>
    <ul class="sidebar-menu">
        <li class="menu-item"><a href="siswa.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
        <li class="menu-item active"><a href="katalog_siswa.php"><i class="fas fa-book-open"></i> Katalog Buku</a></li>
        <li class="menu-item"><a href="siswa_ebook.php"><i class="fas fa-file-pdf"></i> Koleksi E-Book</a></li>
        <li class="menu-item"><a href="peraturan.php"><i class="fas fa-gavel"></i> <span>Peraturan</span></a></li>
        <li class="menu-item"><a href="profil_siswa.php"><i class="fas fa-user-circle"></i> Profil</a></li>
        <li class="menu-item logout"><a href="logout.php" onclick="return confirm('Yakin ingin keluar?')"><i class="fas fa-power-off"></i> Keluar</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="top-bar">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item" style="color: #999; font-size: 13px;">Katalog</li>
                <li class="breadcrumb-item active" style="color: var(--navy); font-weight: 700; font-size: 13px;">Detail Buku</li>
            </ol>
        </nav>
        <div style="font-size: 13px; font-weight: 600; color: #555;">
            <i class="fas fa-user-circle mr-1" style="color: var(--navy);"></i> <?= htmlspecialchars($_SESSION['username']); ?>
        </div>
    </div>

    <div class="container-fluid-custom">
        <a href="katalog_siswa.php" class="back-link"><i class="fas fa-arrow-left mr-2"></i> Kembali ke Katalog</a>

        <div class="detail-card">
            <div class="cover-section">
                <img src="img/<?= !empty($buku['sampul']) ? $buku['sampul'] : 'no-cover.jpg'; ?>" onerror="this.src='https://via.placeholder.com/350x500?text=No+Cover'" alt="Sampul">
                <div class="stok-status <?= ($buku['stok'] > 0) ? 'stok-ada' : 'stok-habis'; ?>">
                    <i class="fas <?= ($buku['stok'] > 0) ? 'fa-check-circle' : 'fa-times-circle'; ?> mr-2"></i> 
                    <?= ($buku['stok'] > 0) ? 'Tersedia: ' . $buku['stok'] . ' Ekspl' : 'Stok Sedang Habis'; ?>
                </div>
                
                <?php if($buku['stok'] > 0): ?>
                    <a href="proses_booking.php?id=<?= $buku['id_buku']; ?>" 
                       class="btn-booking" 
                       onclick="return confirm('Apakah kamu yakin ingin membooking buku ini?')">
                       <i class="fas fa-bookmark mr-2"></i> BOOKING SEKARANG
                    </a>
                <?php else: ?>
                    <button class="btn-booking" style="background: #eee; color: #bbb !important; cursor: not-allowed; box-shadow: none;" disabled>
                        <i class="fas fa-ban mr-2"></i> TIDAK TERSEDIA
                    </button>
                <?php endif; ?>
            </div>

            <div class="info-section">
                <h1><?= htmlspecialchars($buku['judul_buku']); ?></h1>
                <span class="penulis">Ditulis oleh <b style="color: #555;"><?= htmlspecialchars($buku['penulis']); ?></b></span>

                <div class="meta-list">
                    <div class="meta-item">
                        <label>Kategori</label>
                        <div class="value">
                            <!-- Menampilkan nama_kategori hasil JOIN -->
                            <span class="badge-kategori"><?= htmlspecialchars($buku['nama_kategori'] ?? 'Tidak ada kategori'); ?></span>
                        </div>
                    </div>
                    
                    <div class="meta-item">
                        <label>Lokasi Koleksi</label>
                        <div class="value">
                            <!-- Menampilkan nama_rak hasil JOIN -->
                            <span class="badge-rak"><i class="fas fa-map-marker-alt mr-2"></i><?= htmlspecialchars($buku['nama_rak'] ?? 'Belum ditentukan'); ?></span>
                        </div>
                    </div>

                    <div class="meta-item">
                        <label>Sinopsis Buku</label>
                        <div class="value" style="font-weight: 400; color: #666;">
                            <?= !empty($buku['sinopsis']) ? nl2br(htmlspecialchars($buku['sinopsis'])) : '<i>Tidak ada informasi sinopsis untuk buku ini.</i>'; ?>
                        </div>
                    </div>

                    <div class="meta-item">
                        <label>ID Buku</label>
                        <div class="value" style="color: #bbb; font-size: 13px;">#B-<?= str_pad($buku['id_buku'], 5, "0", STR_PAD_LEFT); ?></div>
                    </div>
                </div>
            </div>
        </div>
        
        <footer class="mt-5 mb-4 text-center text-muted" style="font-size: 12px;">
            &copy; <?= date('Y') ?> <b>Widya Graha</b> SMK Negeri Kebumen. All Rights Reserved.
        </footer>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>