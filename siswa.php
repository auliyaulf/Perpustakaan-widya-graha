<?php
session_start();
include 'koneksi.php';

// 1. Proteksi Halaman Siswa
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'siswa') {
    header("location:login.php");
    exit;
}

// 2. Mengambil Data User
$username_session = $_SESSION['username']; 
$query_user = mysqli_query($koneksi, "SELECT * FROM anggota WHERE nis='$username_session'");
$user = mysqli_fetch_assoc($query_user);
$nama_siswa = $user['nama_anggota'] ?? $_SESSION['nama']; 

// 3. Data Statistik
$query_buku = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM buku");
$count_buku = mysqli_fetch_assoc($query_buku)['total'] ?? 0;

$query_pinjam = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM transaksi WHERE nama_anggota='$nama_siswa' AND (status='pinjam' OR status='Dipinjam')");
$count_pinjam = mysqli_fetch_assoc($query_pinjam)['total'] ?? 0;

$query_denda = mysqli_query($koneksi, "SELECT SUM(denda) as total_denda FROM transaksi WHERE nama_anggota='$nama_siswa'");
$total_denda = mysqli_fetch_assoc($query_denda)['total_denda'] ?? 0;

// 4. Logika Notifikasi & Pop-up (Telat >= 7 Hari ATAU Sisa < 1 Hari)
$notif_popup = ""; 
date_default_timezone_set('Asia/Jakarta');
$hari_ini = new DateTime(date('Y-m-d'));

$cek_deadline = mysqli_query($koneksi, "SELECT t.*, b.judul_buku FROM transaksi t JOIN buku b ON t.id_buku = b.id_buku WHERE t.nama_anggota = '$nama_siswa' AND (t.status = 'pinjam' OR t.status = 'Dipinjam')");

while($d = mysqli_fetch_assoc($cek_deadline)) {
    $tgl_p_awal = $d['tgl_pinjam'];
    $tgl_k_awal = $d['tgl_kembali'];

    // Atur deadline otomatis H+7 jika database kosong agar tidak strip
    if ($tgl_k_awal == '0000-00-00' || empty($tgl_k_awal)) {
        $deadline_cek = date('Y-m-d', strtotime($tgl_p_awal . ' + 7 days'));
    } else {
        $deadline_cek = $tgl_k_awal;
    }

    $deadline_dt_cek = new DateTime($deadline_cek);
    $diff_cek = $hari_ini->diff($deadline_dt_cek);
    $selisih_hari = (int)$diff_cek->format("%r%a"); 

    // SYARAT POP-UP: Telat >= 7 hari ATAU Deadline hari ini ( < 1 hari )
    if ($selisih_hari <= -7) {
        $hari_telat = abs($selisih_hari);
        $notif_popup .= "<li class='text-danger' style='margin-bottom:10px;'><i class='fas fa-exclamation-circle'></i> <b>[TELAT $hari_telat HARI]</b><br><small class='text-dark'>".$d['judul_buku']."</small></li>";
    } 
    elseif ($selisih_hari >= 0 && $selisih_hari < 1) {
        $notif_popup .= "<li class='text-warning' style='margin-bottom:10px;'><i class='fas fa-hourglass-half'></i> <b>[DEADLINE HARI INI]</b><br><small class='text-dark'>".$d['judul_buku']."</small></li>";
    }
}

// 5. Salam Waktu
$jam = date('H');
if ($jam >= 5 && $jam < 11) { $salam = "Selamat Pagi"; }
elseif ($jam >= 11 && $jam < 15) { $salam = "Selamat Siang"; }
elseif ($jam >= 15 && $jam < 18) { $salam = "Selamat Sore"; }
else { $salam = "Selamat Malam"; }
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa - Widya Graha</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="wiga_siswa.css">

    <style>
        :root { --navy: #1a3a5f; --yellow: #ffc107; --danger: #f44336; }
        body, .main-content { background-color: #f4f7f6 !important; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: #fff; padding: 20px; border-radius: 15px; display: flex; align-items: center; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #eee; }
        .stat-icon { width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 12px; margin-right: 15px; }
        .stat-info h3 { margin: 0; font-size: 18px; font-weight: 800; color: #333; }
        .stat-info p { margin: 0; font-size: 10px; color: #bbb; text-transform: uppercase; font-weight: 700; }
        .card-tabel-custom { background: #fff; padding: 25px; border-radius: 15px; border: 1px solid #eee; }
        .kode-text { font-family: monospace; font-weight: bold; color: var(--navy); }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <img src="Logoperpus.png" alt="Logo" class="logo-custom" width="55">
        <h2 style="font-size: 16px; color:white; margin-top:10px;">PERPUSTAKAAN</h2>
        <span style="font-size: 12px; color:ffc107;">Widya Graha</span>
    </div>
    <ul class="sidebar-menu">
        <li class="menu-item active"><a href="siswa.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
        <li class="menu-item"><a href="katalog_siswa.php"><i class="fas fa-book-open"></i> Koleksi Buku</a></li>
        <li class="menu-item"><a href="siswa_ebook.php"><i class="fas fa-file-pdf"></i> Koleksi E-Book</a></li>
        <li class="menu-item"><a href="daftar_peraturan.php"><i class="fas fa-gavel"></i> Peraturan</a></li>
        <li class="menu-item"><a href="profil.php"><i class="fas fa-user-circle"></i> Profil</a></li>
        <li class="menu-item logout"><a href="logout.php" onclick="return confirm('Yakin ingin keluar?')"><i class="fas fa-power-off"></i> Keluar</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="top-bar p-3 d-flex justify-content-between align-items-center">
        <span class="text-muted small">Panel Siswa / <b>Dashboard</b></span>
        <span class="badge badge-light p-2"><i class="fas fa-calendar-alt"></i> <?= date('d F Y') ?></span>
    </div>

    <div class="container-fluid p-4">
        <div class="welcome-card mb-4">
            <h2 style="font-weight: 700; color: var(--navy);"><?= $salam; ?>, <?= htmlspecialchars($nama_siswa); ?>! 👋</h2>
            <p class="text-muted">Jangan lupa mengembalikan buku tepat waktu ya.</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon" style="color: #2196f3; background: #e3f2fd;"><i class="fas fa-id-card"></i></div>
                <div class="stat-info"><h3><?= htmlspecialchars($user['nis']); ?></h3><p>NIS SISWA</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="color: #4caf50; background: #e8f5e9;"><i class="fas fa-book"></i></div>
                <div class="stat-info"><h3><?= $count_buku; ?></h3><p>TOTAL BUKU</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="color: #ff9800; background: #fff3e0;"><i class="fas fa-bookmark"></i></div>
                <div class="stat-info"><h3><?= $count_pinjam; ?></h3><p>DIPINJAM</p></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon" style="color: #f44336; background: #ffebee;"><i class="fas fa-hand-holding-usd"></i></div>
                <div class="stat-info"><h3 style="color: #f44336;">Rp <?= number_format($total_denda, 0, ',', '.'); ?></h3><p>TOTAL DENDA</p></div>
            </div>
        </div>

        <div class="card-tabel-custom">
            <h5 class="font-weight-bold mb-4" style="color: var(--navy);">Buku yang Sedang Dipinjam</h5>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Judul Buku</th>
                            <th class="text-center">Tgl Pinjam</th>
                            <th class="text-center">Tgl Kembali</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $res = mysqli_query($koneksi, "SELECT t.*, b.judul_buku FROM transaksi t JOIN buku b ON t.id_buku = b.id_buku WHERE t.nama_anggota = '$nama_siswa' AND (t.status = 'pinjam' OR t.status = 'Dipinjam' OR t.status = 'booking') ORDER BY t.id_transaksi DESC");
                        
                        while ($row = mysqli_fetch_assoc($res)) {
                            $tgl_p = $row['tgl_pinjam'];
                            $tgl_k = $row['tgl_kembali'];

                            // Logika agar tidak kosong (Pinjam + 7 Hari)
                            if ($tgl_k == '0000-00-00' || empty($tgl_k)) {
                                $deadline = date('Y-m-d', strtotime($tgl_p . ' + 7 days'));
                            } else {
                                $deadline = $tgl_k;
                            }

                            $deadline_dt = new DateTime($deadline);
                            $slsh = (int)$hari_ini->diff($deadline_dt)->format("%r%a");

                            $style = "color: #888;";
                            if ($slsh <= -7) { $style = "color: #f44336; font-weight: 800; text-decoration: underline;"; }
                            elseif ($slsh < 0) { $style = "color: #f44336; font-weight: 700;"; }
                            elseif ($slsh <= 1) { $style = "color: #ff9800; font-weight: 700;"; }
                            ?>
                            <tr>
                                <td class="kode-text"><?= $row['kode_transaksi']; ?></td>
                                <td class="font-weight-bold"><?= $row['judul_buku']; ?></td>
                                <td class="text-center"><?= date('d/m/Y', strtotime($tgl_p)); ?></td>
                                <td class="text-center" style="<?= $style ?>"><?= date('d/m/Y', strtotime($deadline)); ?></td>
                                <td class="text-center">
                                    <span class="badge text-white" style="background: var(--navy);"><?= strtoupper($row['status']); ?></span>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalReminder" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background: var(--yellow); border-radius: 15px 15px 0 0; border:none;">
                <h5 class="modal-title font-weight-bold text-dark"><i class="fas fa-bell mr-2"></i> Pengingat Pengembalian</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-4">
                <p>Halo <b><?= htmlspecialchars($nama_siswa); ?></b>, mohon periksa status buku kamu:</p>
                <ul style="list-style:none; padding-left:0;"><?= $notif_popup; ?></ul>
                <hr>
                <p class="text-muted small">Ayo segera ke perpustakaan untuk mengembalikan atau memperpanjang buku agar tidak terkena denda lebih banyak.</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-dark btn-block" data-dismiss="modal" style="border-radius: 10px; font-weight:700;">Saya Mengerti</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        <?php if (!empty($notif_popup)): ?>
            $('#modalReminder').modal('show');
        <?php endif; ?>
    });
</script>
</body>
</html>