<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username'])) {
    header("location:login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['id']);
    
    // 1. Ambil data transaksi
    $query = mysqli_query($koneksi, "SELECT * FROM transaksi WHERE id_transaksi = '$id'");
    $data = mysqli_fetch_assoc($query);

    if ($data) {
        $id_buku = $data['id_buku'];
        $tgl_pinjam = $data['tgl_pinjam'];
        $tgl_kembali_sekarang = date('Y-m-d');

        // 2. Tentukan batas kembali (7 hari dari pinjam)
        $batas_kembali = date('Y-m-d', strtotime('+7 days', strtotime($tgl_pinjam)));

        // 3. Hitung Denda dengan Object DateTime (Lebih Akurat)
        $denda = 0;
        $tarif = 500;

        // Cek jika tanggal hari ini sudah MELEWATI batas kembali
        if ($tgl_kembali_sekarang > $batas_kembali) {
            $tgl1 = new DateTime($batas_kembali);
            $tgl2 = new DateTime($tgl_kembali_sekarang);
            $selisih = $tgl1->diff($tgl2);
            
            $hari_terlambat = $selisih->days;
            $denda = $hari_terlambat * $tarif;
        }

        // 4. Update data ke tabel transaksi
        $update = mysqli_query($koneksi, "UPDATE transaksi SET 
            tgl_kembali = '$tgl_kembali_sekarang', 
            denda = '$denda', 
            status = 'kembali' 
            WHERE id_transaksi = '$id'");

        if ($update) {
            // 5. Kembalikan stok buku
            mysqli_query($koneksi, "UPDATE buku SET stok = stok + 1 WHERE id_buku = '$id_buku'");

            if ($denda > 0) {
                echo "<script>
                    alert('Berhasil! Terlambat $hari_terlambat hari. Denda: Rp " . number_format($denda, 0, ',', '.') . "');
                    window.location='transaksi.php';
                </script>";
            } else {
                echo "<script>
                    alert('Berhasil! Buku dikembalikan tepat waktu.');
                    window.location='transaksi.php';
                </script>";
            }
        }
    }
} else {
    header("location:transaksi.php");
}
?>