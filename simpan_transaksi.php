<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['username'])){
    header("location:login.php");
    exit;
}

if(isset($_POST['simpan'])){
    $id_buku        = $_POST['id_buku'];
    $nama_peminjam  = $_POST['nama_peminjam'];
    $tgl_pinjam     = $_POST['tgl_pinjam'];
    $status         = 'pinjam';
    
    // LOGIKA KODE BARU: TRX + TANGGAL (Misal: TRX-20260315)
    $tgl_kode       = date('Ymd'); 
    $kode_awal      = "TRX-" . $tgl_kode; 

    // Cek stok buku sebelum memproses
    $cek_stok = mysqli_query($koneksi, "SELECT stok FROM buku WHERE id_buku = '$id_buku'");
    $data_buku = mysqli_fetch_assoc($cek_stok);

    if($data_buku['stok'] > 0){
        // Simpan ke database
        $query_pinjam = mysqli_query($koneksi, "INSERT INTO transaksi (kode_transaksi, id_buku, nama_peminjam, tgl_pinjam, status, denda) 
                        VALUES ('$kode_awal', '$id_buku', '$nama_peminjam', '$tgl_pinjam', '$status', '0')");

        if($query_pinjam){
            // Ambil ID yang baru saja masuk untuk disatukan ke kode_transaksi agar benar-benar unik
            $last_id = mysqli_insert_id($koneksi);
            $kode_final = $kode_awal . "-" . $last_id;
            
            // Update kembali kode_transaksinya agar ada nomor urutnya
            mysqli_query($koneksi, "UPDATE transaksi SET kode_transaksi = '$kode_final' WHERE id_transaksi = '$last_id'");

            // Kurangi stok buku
            mysqli_query($koneksi, "UPDATE buku SET stok = stok - 1 WHERE id_buku = '$id_buku'");
            
            echo "<script>alert('Berhasil! Kode Transaksi: $kode_final'); window.location='transaksi.php';</script>";
        } else {
            echo "<script>alert('Gagal simpan!');</script>";
        }
    } else {
        echo "<script>alert('Stok Habis!'); window.location='transaksi.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Pinjaman - Widya Graha</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f4f8; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .form-card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 400px; border-top: 5px solid #1a3a5f; }
        h2 { color: #1a3a5f; margin-top: 0; text-align: center; font-size: 20px; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-size: 14px; color: #666; font-weight: bold; }
        input, select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; font-size: 14px; }
        .btn-simpan { width: 100%; background: #1a3a5f; color: white; padding: 12px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold; transition: 0.3s; font-size: 15px; }
        .btn-simpan:hover { background: #254a7d; transform: translateY(-2px); }
        .btn-batal { display: block; text-align: center; margin-top: 15px; color: #888; text-decoration: none; font-size: 13px; }
    </style>
</head>
<body>

<div class="form-card">
    <h2><i class="fas fa-plus-circle"></i> Pinjam Buku</h2>
    <form action="" method="post">
        <div class="form-group">
            <label>Judul Buku</label>
            <select name="id_buku" required>
                <option value="">-- Pilih Buku --</option>
                <?php
                $buku = mysqli_query($koneksi, "SELECT * FROM buku WHERE stok > 0");
                while($b = mysqli_fetch_assoc($buku)){
                    echo "<option value='".$b['id_buku']."'>".$b['judul_buku']." (Stok: ".$b['stok'].")</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label>Nama Peminjam (Siswa)</label>
            <input type="text" name="nama_peminjam" placeholder="Masukkan nama lengkap" required>
        </div>
        <div class="form-group">
            <label>Tanggal Pinjam</label>
            <input type="date" name="tgl_pinjam" value="<?= date('Y-m-d') ?>" required>
        </div>
        <button type="submit" name="simpan" class="btn-simpan">Konfirmasi Pinjaman</button>
        <a href="transaksi.php" class="btn-batal">Kembali ke Daftar</a>
    </form>
</div>

</body>
</html>