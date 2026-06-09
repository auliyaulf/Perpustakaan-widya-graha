<?php
include 'koneksi.php';

// Memberitahu browser bahwa ini adalah file Excel
header("Content-type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=Laporan_Perpustakaan_Widya_Graha.xls");
?>

<h2>Laporan Transaksi Perpustakaan Widya Graha</h2>
<p>Tanggal Cetak: <?= date('d-m-Y') ?></p>

<table border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Transaksi</th>
            <th>Nama Peminjam</th>
            <th>Judul Buku</th>
            <th>Tgl Pinjam</th>
            <th>Tgl Kembali</th>
            <th>Denda</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $no = 1;
        // Ambil semua data transaksi (JOIN dengan buku agar judul muncul)
        $query = mysqli_query($koneksi, "SELECT transaksi.*, buku.judul_buku 
                                         FROM transaksi 
                                         JOIN buku ON transaksi.id_buku = buku.id_buku 
                                         ORDER BY id_transaksi DESC");
        while($row = mysqli_fetch_assoc($query)){
        ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= $row['kode_transaksi']; ?></td>
            <td><?= $row['nama_anggota']; ?></td>
            <td><?= $row['judul_buku']; ?></td>
            <td><?= date('d/m/Y', strtotime($row['tgl_pinjam'])); ?></td>
            <td><?= ($row['tgl_kembali'] != '0000-00-00' && $row['tgl_kembali'] != NULL) ? date('d/m/Y', strtotime($row['tgl_kembali'])) : '-'; ?></td>
            <td><?= $row['denda']; ?></td>
            <td><?= ucfirst($row['status']); ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>