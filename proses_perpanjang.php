<?php
include 'koneksi.php';

// Menangkap ID dari tombol yang diklik di transaksi.php
if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);

    // 1. Ambil data transaksi berdasarkan ID
    $sql  = mysqli_query($koneksi, "SELECT tgl_pinjam FROM transaksi WHERE id_transaksi = '$id'");
    $data = mysqli_fetch_assoc($sql);
    
    if ($data) {
        $tgl_lama = $data['tgl_pinjam'];
        
        // 2. Tambahkan 7 hari ke tanggal pinjam yang ada di database
        // Ini akan membuat hitungan batas_kembali di transaksi.php otomatis bertambah
        $tgl_baru = date('Y-m-d', strtotime('+7 days', strtotime($tgl_lama)));

        // 3. Update tanggal pinjam di database
        $update = mysqli_query($koneksi, "UPDATE transaksi SET tgl_pinjam = '$tgl_baru' WHERE id_transaksi = '$id'");

        if ($update) {
            echo "<script>
                alert('Masa pinjam berhasil diperpanjang!');
                window.location='transaksi.php';
            </script>";
        } else {
            echo "<script>
                alert('Gagal memperpanjang data.');
                window.location='transaksi.php';
            </script>";
        }
    }
} else {
    header("location:transaksi.php");
}
?>