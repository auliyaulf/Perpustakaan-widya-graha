<?php
include 'koneksi.php';

if (isset($_POST['simpan'])) {
    $judul    = mysqli_real_escape_string($koneksi, $_POST['judul_buku']);
    $penulis  = mysqli_real_escape_string($koneksi, $_POST['penulis']);
    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);
    $stok     = mysqli_real_escape_string($koneksi, $_POST['stok']);

    // Proses Sampul
    $nama_file = $_FILES['sampul']['name'];
    $tmp_file  = $_FILES['sampul']['tmp_name'];
    
    if ($nama_file != "") {
        $nama_baru = date('dmYHis')."_".$nama_file;
        move_uploaded_file($tmp_file, 'img/' . $nama_baru);
        $sampul_db = $nama_baru;
    } else {
        $sampul_db = "default.jpg"; 
    }

    $query = "INSERT INTO buku (judul_buku, penulis, kategori, stok, sampul) 
              VALUES ('$judul', '$penulis', '$kategori', '$stok', '$sampul_db')";
    
    if (mysqli_query($koneksi, $query)) {
        header("location:buku.php?pesan=berhasil");
    } else {
        echo "Gagal menyimpan: " . mysqli_error($koneksi);
    }
}
?>