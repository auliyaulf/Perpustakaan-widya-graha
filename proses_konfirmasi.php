<?php
include 'koneksi.php';
$id = $_GET['id'];

// Mengubah status agar booking selesai dan resmi dipinjam
$query = mysqli_query($koneksi, "UPDATE transaksi SET status = 'pinjam' WHERE id_transaksi = '$id'");

if($query){
    echo "<script>alert('Berhasil dikonfirmasi! Notifikasi di dashboard sekarang sudah hilang.'); window.location='transaksi.php';</script>";
} else {
    echo "Gagal: " . mysqli_error($koneksi);
}
?>