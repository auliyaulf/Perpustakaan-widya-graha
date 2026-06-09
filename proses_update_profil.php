<?php
session_start();
include 'koneksi.php';

// 1. Proteksi Halaman
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'siswa') {
    header("location:login.php");
    exit;
}

// 2. Tangkap data dari form
// NIS diambil dari session sebagai kunci (Primary Key) untuk update
$nis = $_SESSION['username']; 

$nama_anggota = mysqli_real_escape_string($koneksi, $_POST['nama_anggota']);
$no_telp = mysqli_real_escape_string($koneksi, $_POST['no_telp']);
$jenis_kelamin = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
$alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);

// 3. Logika Upload Foto Baru
if ($_FILES['foto']['name'] != '') {
    $foto_awal = $_FILES['foto']['name'];
    $tmp_file = $_FILES['foto']['tmp_name'];
    
    // Rename foto agar unik (contoh: 15-05-2026_14-30-00_foto.jpg)
    $foto_baru = date('d-m-Y_H-i-s') . '_' . $foto_awal;
    $path = "img/siswa/" . $foto_baru;

    // Pindahkan file foto ke folder img/siswa
    if (move_uploaded_file($tmp_file, $path)) {
        // Query UPDATE beserta fotonya
        $query = "UPDATE anggota SET 
                    nama_anggota = '$nama_anggota', 
                    no_telp = '$no_telp', 
                    jenis_kelamin = '$jenis_kelamin', 
                    alamat = '$alamat',
                    foto = '$foto_baru' 
                  WHERE nis = '$nis'";
    } else {
        echo "<script>alert('Gagal mengupload foto!'); window.location='profil_siswa.php';</script>";
        exit;
    }
} else {
    // Jika siswa tidak mengupload foto baru (foto lama tetap aman)
    $query = "UPDATE anggota SET 
                nama_anggota = '$nama_anggota', 
                no_telp = '$no_telp', 
                jenis_kelamin = '$jenis_kelamin', 
                alamat = '$alamat' 
              WHERE nis = '$nis'";
}

// 4. Eksekusi Query ke Database
$update = mysqli_query($koneksi, $query);

if ($update) {
    echo "<script>alert('Profil berhasil diperbarui!'); window.location='profil.php';</script>";
} else {
    echo "<script>alert('Gagal memperbarui profil: " . mysqli_error($koneksi) . "'); window.location='profil.php';</script>";
}
?>