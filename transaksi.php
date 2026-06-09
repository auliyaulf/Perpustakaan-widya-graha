<?php
session_start();
include 'koneksi.php';

// Proteksi Halaman
if(!isset($_SESSION['username'])){
    header("location:login.php");
    exit;
}

// --- LOGIKA SIMPAN TRANSAKSI ---
if(isset($_POST['tambah_transaksi'])){
    $kode       = "TR-" . date('YmdHis');
    $id_buku    = $_POST['id_buku'];
    $nama       = mysqli_real_escape_string($koneksi, $_POST['nama_anggota']);
    $tgl_pinjam = date('Y-m-d');
    $status     = 'pinjam';

    // 1. CEK APAKAH PEMINJAM SUDAH MEMINJAM HARI INI
    $cek_hari_ini = mysqli_query($koneksi, "SELECT * FROM transaksi WHERE nama_anggota = '$nama' AND tgl_pinjam = '$tgl_pinjam'");
    
    if(mysqli_num_rows($cek_hari_ini) > 0){
        // Jika sudah ada peminjaman hari ini
        echo "<script>alert('Gagal! Nama ini sudah melakukan peminjaman hari ini. Batas maksimal 1 kali sehari.'); window.location='transaksi.php';</script>";
    } else {
        // 2. Cek stok buku sekali lagi sebelum insert
        $cek_stok = mysqli_query($koneksi, "SELECT stok FROM buku WHERE id_buku = '$id_buku'");
        $s = mysqli_fetch_assoc($cek_stok);

        if($s['stok'] > 0){
            $insert = mysqli_query($koneksi, "INSERT INTO transaksi (kode_transaksi, id_buku, nama_anggota, tgl_pinjam, status) 
                                              VALUES ('$kode', '$id_buku', '$nama', '$tgl_pinjam', '$status')");
            
            if($insert){
                mysqli_query($koneksi, "UPDATE buku SET stok = stok - 1 WHERE id_buku = '$id_buku'");
                echo "<script>alert('Peminjaman Berhasil Disimpan!'); window.location='transaksi.php';</script>";
            }
        } else {
            echo "<script>alert('Maaf, Stok buku habis!'); window.location='transaksi.php';</script>";
        }
    }
}

// --- QUERY DATA ---
$query_booking = mysqli_query($koneksi, "SELECT transaksi.*, buku.judul_buku FROM transaksi JOIN buku ON transaksi.id_buku = buku.id_buku WHERE transaksi.status = 'booking' ORDER BY id_transaksi DESC");
$query_aktif   = mysqli_query($koneksi, "SELECT transaksi.*, buku.judul_buku FROM transaksi JOIN buku ON transaksi.id_buku = buku.id_buku WHERE transaksi.status = 'pinjam' ORDER BY tgl_pinjam DESC");
$query_riwayat = mysqli_query($koneksi, "SELECT transaksi.*, buku.judul_buku FROM transaksi JOIN buku ON transaksi.id_buku = buku.id_buku WHERE transaksi.status = 'kembali' ORDER BY tgl_kembali DESC");

$total_aktif = mysqli_num_rows($query_aktif);

// Data untuk Modal
$buku_opt = mysqli_query($koneksi, "SELECT * FROM buku WHERE stok > 0");
$anggota_opt = mysqli_query($koneksi, "SELECT * FROM anggota");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi - Widya Graha</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="Logoperpus.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="transaksi.css">

</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <img src="Logoperpus.png" alt="Logo Perpus" class="logo-custom">
        <h2>PERPUSTAKAAN</h2>
        <span>Widya Graha</span>
    </div>
    <nav class="sidebar-nav">
        <ul class="sidebar-menu">
            <li class="menu-item"><a href="admin.php"><i class="fas fa-th-large"></i> <span>Dashboard</span></a></li>
            <li class="menu-item"><a href="buku.php"><i class="fas fa-book-open"></i> <span>Koleksi Buku</span></a></li>
            <li class="menu-item"><a href="data_ebook.php"><i class="fas fa-file-pdf"></i> <span>Data E-Book</span></a></li>
            <li class="menu-item"><a href="anggota.php"><i class="fas fa-user-friends"></i> <span>Data Anggota</span></a></li>
            <li class="menu-item active"><a href="transaksi.php"><i class="fas fa-exchange-alt"></i> <span>Transaksi</span></a></li>
            <li class="menu-item"><a href="peraturan.php"><i class="fas fa-gavel"></i> <span>Kelola Peraturan</span></a></li>
            <li class="menu-item"><a href="profil_admin.php"><i class="fas fa-user-shield"></i> <span>Profil Admin</span></a></li>
            
            <li class="menu-item logout">
                <a href="logout.php" onclick="return confirm('Yakin ingin keluar?')">
                    <i class="fas fa-power-off"></i> <span>Keluar</span>
                </a>
            </li>
        </ul>
    </nav>
</div>

<div class="main-content">
    <div class="card-header-admin d-flex justify-content-between align-items-center">
        <div style="font-size: 16px; color: #888;">
            Panel Admin / <b style="color: #555;">Manajemen Transaksi</b>
        </div>
        <span class="badge badge-light p-2"><i class="fas fa-calendar-alt"></i> &nbsp; <?= date('d F Y') ?></span>
    </div>

    <div class="content-body">
        <div class="card card-custom p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div class="d-flex flex-wrap">
                    <button type="button" class="btn btn-navy mr-2 mb-2 mb-md-0" data-toggle="modal" data-target="#modalTambah">
                        <i class="fas fa-plus-circle mr-2"></i> Tambah Peminjaman
                    </button>
                    <a href="export_excel.php" class="btn btn-excel mb-2 mb-md-0">
                        <i class="fas fa-file-excel mr-2"></i> Export Excel
                    </a>
                </div>
                <div class="text-muted font-weight-bold">
                    Peminjaman Aktif: <span class="text-primary"><?= $total_aktif; ?> Orang</span>
                </div>
            </div>
        </div>

        <div class="card card-custom" style="border-top: 5px solid #ffc107;">
            <div class="p-4">
                <h5 class="font-weight-bold" style="color: var(--navy);"><i class="fas fa-bell text-warning mr-2"></i> Antrean Booking</h5>
                <hr>
                <div class="table-responsive">
                    <table class="table table-hover text-center">
                        <thead>
                            <tr><th>No</th><th>Peminjam</th><th>Judul Buku</th><th>Tgl Booking</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                            <?php $no_b=1; while($rb = mysqli_fetch_assoc($query_booking)){ ?>
                            <tr>
                                <td><?= $no_b++ ?></td>
                                <td class="font-weight-bold"><?= $rb['nama_anggota'] ?></td>
                                <td><?= $rb['judul_buku'] ?></td>
                                <td><?= date('d/m/Y', strtotime($rb['tgl_pinjam'])) ?></td>
                                <td>
                                    <a href="proses_konfirmasi.php?id=<?= $rb['id_transaksi'] ?>" class="btn btn-success btn-sm rounded-circle" title="Konfirmasi Pinjam"><i class="fas fa-check"></i></a>
                                </td>
                            </tr>
                            <?php } if(mysqli_num_rows($query_booking)==0) echo "<tr><td colspan='5' class='text-muted py-3'>Tidak ada antrean booking.</td></tr>"; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card card-custom" style="border-top: 5px solid #007bff;">
            <div class="p-4">
                <h5 class="font-weight-bold" style="color: var(--navy);"><i class="fas fa-hourglass-half text-primary mr-2"></i> Peminjaman Berjalan</h5>
                <hr>
                <div class="table-responsive">
                    <table class="table table-hover text-center">
                        <thead>
                            <tr><th>No</th><th>Peminjam</th><th>Judul Buku</th><th>Batas Kembali</th><th>Status</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                            <?php $no_a=1; while($ra = mysqli_fetch_assoc($query_aktif)){ 
                                $batas = date('Y-m-d', strtotime('+7 days', strtotime($ra['tgl_pinjam'])));
                                $is_telat = (date('Y-m-d') > $batas);
                            ?>
                            <tr>
                                <td><?= $no_a++ ?></td>
                                <td class="font-weight-bold"><?= $ra['nama_anggota'] ?></td>
                                <td><?= $ra['judul_buku'] ?></td>
                                <td class="text-orange font-weight-bold"><?= date('d/m/Y', strtotime($batas)) ?></td>
                                <td><span class="badge-custom <?= $is_telat ? 'bg-danger text-white' : 'bg-primary text-white' ?>"><?= $is_telat ? 'Terlambat' : 'Dipinjam' ?></span></td>
                                <td>
                                    <a href="proses_kembali.php?id=<?= $ra['id_transaksi'] ?>" class="btn btn-primary btn-sm px-3 rounded-pill">Kembali</a>
                                    <a href="proses_perpanjang.php?id=<?= $ra['id_transaksi'] ?>" class="btn btn-warning btn-sm text-white rounded-circle" title="Perpanjang"><i class="fas fa-clock"></i></a>
                                </td>
                            </tr>
                            <?php } if(mysqli_num_rows($query_aktif)==0) echo "<tr><td colspan='6' class='text-muted py-3'>Belum ada peminjaman aktif.</td></tr>"; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card card-custom" style="border-top: 5px solid #28a745;">
            <div class="p-4">
                <h5 class="font-weight-bold" style="color: var(--navy);"><i class="fas fa-history text-success mr-2"></i> Riwayat Peminjaman</h5>
                <hr>
                <div class="table-responsive">
                    <table class="table table-hover text-center">
                        <thead>
                            <tr><th>No</th><th>Peminjam</th><th>Judul Buku</th><th>Tgl Kembali</th><th>Denda</th><th>Opsi</th></tr>
                        </thead>
                        <tbody>
                            <?php $no_r=1; while($rr = mysqli_fetch_assoc($query_riwayat)){ ?>
                            <tr>
                                <td><?= $no_r++ ?></td>
                                <td><?= $rr['nama_anggota'] ?></td>
                                <td><?= $rr['judul_buku'] ?></td>
                                <td><?= date('d/m/Y', strtotime($rr['tgl_kembali'])) ?></td>
                                <td><b class="<?= ($rr['denda'] > 0) ? 'text-danger' : 'text-success' ?>">Rp <?= number_format($rr['denda'], 0, ',', '.') ?></b></td>
                                <td>
                                    <a href="proses_hapus_riwayat.php?id=<?= $rr['id_transaksi'] ?>" class="text-danger" onclick="return confirm('Hapus riwayat ini?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php } if(mysqli_num_rows($query_riwayat)==0) echo "<tr><td colspan='6' class='text-muted py-3'>Belum ada riwayat.</td></tr>"; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <footer class="mt-5 mb-4 text-center text-muted" style="font-size: 12px;">
            &copy; <?= date('Y') ?> <b>Widya Graha</b> SMK Negeri Kebumen. All Rights Reserved.
        </footer>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 15px; border: none;">
            <div class="modal-header border-0">
                <h4 class="modal-title font-weight-bold" style="color: var(--navy);">Tambah Transaksi</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Peminjam</label>
                        <select name="nama_anggota" class="form-control" style="border-radius: 10px; background: #f8f9fa;" required>
                            <option value="">-- Pilih Anggota --</option>
                            <?php mysqli_data_seek($anggota_opt, 0); while($a = mysqli_fetch_assoc($anggota_opt)){ ?>
                                <option value="<?= $a['nama_anggota'] ?>"><?= $a['nis'] ?> - <?= $a['nama_anggota'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Judul Buku</label>
                        <select name="id_buku" class="form-control" style="border-radius: 10px; background: #f8f9fa;" required>
                            <option value="">-- Pilih Buku --</option>
                            <?php mysqli_data_seek($buku_opt, 0); while($b = mysqli_fetch_assoc($buku_opt)){ ?>
                                <option value="<?= $b['id_buku'] ?>"><?= $b['judul_buku'] ?> (Stok: <?= $b['stok'] ?>)</option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" name="tambah_transaksi" class="btn btn-navy btn-block py-2" style="border-radius: 10px;">Simpan Peminjaman</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>