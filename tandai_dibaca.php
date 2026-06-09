<?php
include 'koneksi.php';
$id_pesan = $_GET['id'];
mysqli_query($koneksi, "UPDATE pesan SET status = 'sudah_dibaca' WHERE id_pesan = '$id_pesan'");
header("location:katalog_siswa.php");
?>