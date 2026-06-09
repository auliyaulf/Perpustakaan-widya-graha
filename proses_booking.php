<?php
session_start();
include 'koneksi.php';

// Atur zona waktu agar sinkron
date_default_timezone_set('Asia/Jakarta');

// Proteksi Halaman
if (!isset($_SESSION['username'])) {
    header("location:login.php");
    exit;
}

// Ambil ID Buku
$id_buku = isset($_GET['id']) ? mysqli_real_escape_string($koneksi, $_GET['id']) : '';
$username_session = $_SESSION['username']; // Ini NIS kamu

if (empty($id_buku)) {
    header("location:katalog_siswa.php");
    exit;
}

// 1. AMBIL DATA ANGGOTA
$query_user = mysqli_query($koneksi, "SELECT * FROM anggota WHERE nis = '$username_session'");
$data_user = mysqli_fetch_assoc($query_user);

if (!$data_user) {
    echo "<script>alert('Data anggota tidak ditemukan!'); window.location.href='login.php';</script>";
    exit;
}

$nis_siswa = $data_user['nis'];
$nama_asli_siswa = $data_user['nama_anggota'];

// 2. LOGIKA PEMBATASAN
$hari_ini = date('Y-m-d');
$cek_harian = mysqli_query($koneksi, "SELECT * FROM transaksi 
                                      WHERE nis = '$nis_siswa' 
                                      AND DATE(tgl_pinjam) = '$hari_ini'");

if (mysqli_num_rows($cek_harian) >= 1) {
    echo "<script>
            alert('Maaf, kamu hanya diperbolehkan melakukan 1 booking buku dalam sehari!');
            window.location.href = 'siswa.php';
          </script>";
    exit;
}

// 3. AMBIL DATA BUKU
$query_buku = mysqli_query($koneksi, "SELECT * FROM buku WHERE id_buku = '$id_buku'");
$data_buku = mysqli_fetch_assoc($query_buku);

if (!$data_buku || $data_buku['stok'] <= 0) {
    echo "<script>alert('Stok buku habis!'); window.location.href='katalog_siswa.php';</script>";
    exit;
}

// 4. PROSES LOGIKA DATA
$kode_transaksi = "TR-" . date('YmdHis');
$tgl_pinjam = date('Y-m-d H:i:s');
$tgl_kembali = date('Y-m-d', strtotime('+7 days'));

// --- PERBAIKAN ERROR FOREIGN KEY (id_pustakawan) ---
// Kita ambil satu ID dari tabel pustakawan agar tidak melanggar constraint.
// Jika di database kamu ID pustakawan pertamanya bukan 1, silakan ganti angka ini.
$id_pustakawan_default = 1; 

// 5. INSERT KE TABEL TRANSAKSI
// Menambahkan id_pustakawan ke dalam query agar sesuai dengan struktur database di gambar 17779599210345832360271532117311_1c4277.jpg
$query_insert = "INSERT INTO transaksi (kode_transaksi, id_buku, nis, nama_anggota, tgl_pinjam, tgl_kembali, status, id_pustakawan) 
                 VALUES ('$kode_transaksi', '$id_buku', '$nis_siswa', '$nama_asli_siswa', '$tgl_pinjam', '$tgl_kembali', 'booking', '$id_pustakawan_default')";

if (mysqli_query($koneksi, $query_insert)) {
    // Kurangi stok buku
    mysqli_query($koneksi, "UPDATE buku SET stok = stok - 1 WHERE id_buku = '$id_buku'");
    
    echo "<script>
            alert('Booking Berhasil!\\n\\nSilakan ambil buku di perpustakaan.\\nBatas Kembali: " . date('d/m/Y', strtotime($tgl_kembali)) . "');
            window.location.href = 'siswa.php';
          </script>";
} else {
    // Menampilkan error spesifik jika gagal
    echo "Gagal: " . mysqli_error($koneksi);
}
?>