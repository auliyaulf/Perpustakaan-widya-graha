<?php
include 'koneksi.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Hapus data transaksi berdasarkan ID
    $query = mysqli_query($koneksi, "DELETE FROM transaksi WHERE id_transaksi = '$id'");

    if ($query) {
        echo "<script>alert('Riwayat berhasil dihapus!'); window.location='transaksi.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data!'); window.location='transaksi.php';</script>";
    }
} else {
    header("location:transaksi.php");
}
?>