<?php
session_start();
include 'koneksi.php';

// Proteksi Halaman
if (!isset($_SESSION['username'])) {
    header("location:login.php");
    exit;
}

// --- LOGIKA PROSES TAMBAH ---
if (isset($_POST['simpan'])) {
    $judul    = mysqli_real_escape_string($koneksi, $_POST['judul_buku']);
    $penulis  = mysqli_real_escape_string($koneksi, $_POST['penulis']);
    $kategori = mysqli_real_escape_string($koneksi, $_POST['id_kategori']); // Mengambil ID
    $stok     = mysqli_real_escape_string($koneksi, $_POST['stok']);
    $rak      = mysqli_real_escape_string($koneksi, $_POST['id_rak']); // Mengambil ID
    $sinopsis = mysqli_real_escape_string($koneksi, $_POST['sinopsis']);

    $nama_file = $_FILES['sampul']['name'];
    if ($nama_file != "") {
        $nama_baru = date('dmYHis') . $nama_file;
        move_uploaded_file($_FILES['sampul']['tmp_name'], 'img/' . $nama_baru);
        $sampul_db = $nama_baru;
    } else {
        $sampul_db = "default.jpg";
    }

    mysqli_query($koneksi, "INSERT INTO buku (judul_buku, penulis, id_kategori, stok, id_rak, sampul, sinopsis) 
                            VALUES ('$judul', '$penulis', '$kategori', '$stok', '$rak', '$sampul_db', '$sinopsis')");
    header("location:buku.php?pesan=tambah_berhasil");
}

// --- LOGIKA PROSES UPDATE ---
if (isset($_POST['update'])) {
    $id       = $_POST['id_buku'];
    $judul    = mysqli_real_escape_string($koneksi, $_POST['judul_buku']);
    $penulis  = mysqli_real_escape_string($koneksi, $_POST['penulis']);
    $kategori = mysqli_real_escape_string($koneksi, $_POST['id_kategori']);
    $stok     = mysqli_real_escape_string($koneksi, $_POST['stok']);
    $rak      = mysqli_real_escape_string($koneksi, $_POST['id_rak']);
    $sinopsis = mysqli_real_escape_string($koneksi, $_POST['sinopsis']);

    $nama_file = $_FILES['sampul']['name'];
    if ($nama_file != "") {
        $nama_baru = date('dmYHis') . $nama_file;
        move_uploaded_file($_FILES['sampul']['tmp_name'], 'img/' . $nama_baru);
        $query = "UPDATE buku SET judul_buku='$judul', penulis='$penulis', id_kategori='$kategori', stok='$stok', id_rak='$rak', sampul='$nama_baru', sinopsis='$sinopsis' WHERE id_buku='$id'";
    } else {
        $query = "UPDATE buku SET judul_buku='$judul', penulis='$penulis', id_kategori='$kategori', stok='$stok', id_rak='$rak', sinopsis='$sinopsis' WHERE id_buku='$id'";
    }
    mysqli_query($koneksi, $query);
    header("location:buku.php?pesan=update_berhasil");
}

// --- LOGIKA PROSES HAPUS ---
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($koneksi, "DELETE FROM buku WHERE id_buku='$id'");
    header("location:buku.php?pesan=hapus_berhasil");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Buku - Widya Graha</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="buku.css">
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <img src="Logoperpus.png" alt="Logo Perpus" class="logo-custom">
        <h2>PERPUSTAKAAN</h2>
        <span>Widya Graha</span>
    </div>
    <nav class="sidebar-nav">
        <ul class="sidebar-menu">
            <li class="menu-item"><a href="admin.php"><i class="fas fa-th-large"></i> <span>Dashboard</span></a></li>
            <li class="menu-item active"><a href="buku.php"><i class="fas fa-book-open"></i> <span>Koleksi Buku</span></a></li>
            <li class="menu-item"><a href="data_ebook.php"><i class="fas fa-file-pdf"></i> <span>Data E-Book</span></a></li>
            <li class="menu-item"><a href="anggota.php"><i class="fas fa-user-friends"></i> <span>Data Anggota</span></a></li>
            <li class="menu-item"><a href="transaksi.php"><i class="fas fa-exchange-alt"></i> <span>Transaksi</span></a></li>
            <li class="menu-item"><a href="peraturan.php"><i class="fas fa-gavel"></i> <span>Kelola Peraturan</span></a></li>
           <li class="menu-item"><a href="profil_admin.php"><i class="fas fa-user-shield"></i> <span>Profil Admin</span></a></li>
            <li class="menu-item logout">
                <a href="logout.php" onclick="return confirm('Yakin ingin keluar?')">
                    <i class="fas fa-power-off"></i> <span>Keluar</span>
                </a>
            </li>
        </ul>
    </nav>
</div>

<div class="main-content">
    <div class="top-bar">
        <span style="color: #888;">Panel Admin / <b style="color: #555;">Kelola Buku</b></span>
        <span class="badge badge-light p-2"><i class="fas fa-calendar-alt"></i> &nbsp; <?= date('d F Y') ?></span>
    </div>

    <div class="content-body">
        <div class="card card-custom p-4 shadow-sm" style="border-radius: 15px; border: none;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <button class="btn btn-primary" data-toggle="modal" data-target="#modalTambah" style="background: #1a3a5f; border: none; border-radius: 8px; font-weight: 600; padding: 10px 20px;">
                    <i class="fas fa-plus-circle mr-2"></i> Tambah Buku Baru
                </button>
                <div class="text-muted">Total: <b style="color: #1a3a5f;"><?= mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM buku")); ?></b> Judul</div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr style="font-size: 11px; text-transform: uppercase; color: #999; letter-spacing: 1px;">
                            <th>No</th>
                            <th>Sampul</th>
                            <th>Info Buku</th>
                            <th>Kategori</th>
                            <th>Lokasi</th>
                            <th>Stok</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        // Query dengan JOIN untuk mengambil nama dari ID
                        $query_join = "SELECT buku.*, kategori.nama_kategori, rak.nama_rak 
                                       FROM buku 
                                       LEFT JOIN kategori ON buku.id_kategori = kategori.id_kategori 
                                       LEFT JOIN rak ON buku.id_rak = rak.id_rak 
                                       ORDER BY buku.id_buku DESC";
                        $data = mysqli_query($koneksi, $query_join);
                        while($d = mysqli_fetch_array($data)){
                        ?>
                        <tr>
                            <td class="align-middle"><?= $no++; ?></td>
                            <td class="align-middle">
                                <img src="img/<?= $d['sampul']; ?>" class="img-table shadow-sm" style="width: 50px; height: 70px; object-fit: cover; border-radius: 5px;" onerror="this.src='https://via.placeholder.com/50x70'">
                            </td>
                            <td class="align-middle">
                                <div style="font-weight: 700; color: #1a3a5f;"><?= $d['judul_buku']; ?></div>
                                <small class="text-muted"><i class="fas fa-pen-nib fa-xs"></i> <?= $d['penulis']; ?></small>
                            </td>
                            <td class="align-middle"><span class="badge badge-info px-2 py-1" style="border-radius: 5px;"><?= $d['nama_kategori']; ?></span></td>
                            <td class="align-middle"><span class="badge badge-light text-warning"><?= $d['nama_rak']; ?></span></td>
                            <td class="align-middle text-center"><b><?= $d['stok']; ?></b></td>
                            <td class="align-middle text-center">
                                <button class="btn btn-sm btn-light text-info mr-1" title="Lihat Sinopsis" onclick="alert('Sinopsis:\n\n<?= addslashes(str_replace(["\r", "\n"], ' ', $d['sinopsis'])); ?>')">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-light text-warning edit-btn mr-1" 
                                        data-id="<?= $d['id_buku']; ?>" 
                                        data-judul="<?= htmlspecialchars($d['judul_buku']); ?>" 
                                        data-penulis="<?= htmlspecialchars($d['penulis']); ?>" 
                                        data-kategori="<?= $d['id_kategori']; ?>" 
                                        data-stok="<?= $d['stok']; ?>" 
                                        data-rak="<?= $d['id_rak']; ?>"
                                        data-sinopsis="<?= htmlspecialchars($d['sinopsis']); ?>"
                                        data-toggle="modal" data-target="#modalEdit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="buku.php?hapus=<?= $d['id_buku']; ?>" class="btn btn-sm btn-light text-danger" onclick="return confirm('Hapus buku ini?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0">
                <h4 style="font-weight: 700; color: #1a3a5f;">Tambah Buku</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="buku.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Judul Buku</label>
                        <input type="text" name="judul_buku" class="form-control" required placeholder="Masukkan judul lengkap">
                    </div>
                    <div class="form-group">
                        <label>Penulis</label>
                        <input type="text" name="penulis" class="form-control" required placeholder="Nama penulis">
                    </div>
                    <div class="form-group">
                        <label>Sinopsis</label>
                        <textarea name="sinopsis" class="form-control" rows="4" placeholder="Ringkasan cerita buku..."></textarea>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Kategori</label>
                                <select name="id_kategori" class="form-control" required>
                                    <option value="">-- Pilih --</option>
                                    <?php
                                    $kat = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
                                    while($k = mysqli_fetch_array($kat)){
                                        echo "<option value='".$k['id_kategori']."'>".$k['nama_kategori']."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Rak</label>
                                <select name="id_rak" class="form-control" required>
                                    <option value="">-- Pilih --</option>
                                    <?php
                                    $rak_res = mysqli_query($koneksi, "SELECT * FROM rak ORDER BY nama_rak ASC");
                                    while($r = mysqli_fetch_array($rak_res)){
                                        echo "<option value='".$r['id_rak']."'>".$r['nama_rak']."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Stok Buku</label>
                        <input type="number" name="stok" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Unggah Sampul</label>
                        <input type="file" name="sampul" class="form-control-file">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" name="simpan" class="btn btn-success px-4" style="border-radius: 10px;">Simpan Data</button>
                    <button type="button" class="btn btn-light px-4" data-dismiss="modal" style="border-radius: 10px;">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header border-0">
                <h4 style="font-weight: 700; color: #1a3a5f;">Edit Informasi Buku</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="buku.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id_buku" id="edit-id">
                    <div class="form-group">
                        <label>Judul Buku</label>
                        <input type="text" name="judul_buku" id="edit-judul" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Penulis</label>
                        <input type="text" name="penulis" id="edit-penulis" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Sinopsis</label>
                        <textarea name="sinopsis" id="edit-sinopsis" class="form-control" rows="4"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Kategori</label>
                                <select name="id_kategori" id="edit-kategori" class="form-control" required>
                                    <?php
                                    $kat_edit = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
                                    while($ke = mysqli_fetch_array($kat_edit)){
                                        echo "<option value='".$ke['id_kategori']."'>".$ke['nama_kategori']."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label>Rak</label>
                                <select name="id_rak" id="edit-rak" class="form-control" required>
                                    <?php
                                    $rak_edit_res = mysqli_query($koneksi, "SELECT * FROM rak ORDER BY nama_rak ASC");
                                    while($re = mysqli_fetch_array($rak_edit_res)){
                                        echo "<option value='".$re['id_rak']."'>".$re['nama_rak']."</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Stok Buku</label>
                        <input type="number" name="stok" id="edit-stok" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Ganti Sampul <small class="text-muted">(Kosongkan jika tidak diganti)</small></label>
                        <input type="file" name="sampul" class="form-control-file">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" name="update" class="btn btn-warning px-4" style="border-radius: 10px; color: white; font-weight: 600;">Update Data</button>
                    <button type="button" class="btn btn-light px-4" data-dismiss="modal" style="border-radius: 10px;">Tutup</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).on("click", ".edit-btn", function () {
        $(".modal-body #edit-id").val($(this).data('id'));
        $(".modal-body #edit-judul").val($(this).data('judul'));
        $(".modal-body #edit-penulis").val($(this).data('penulis'));
        $(".modal-body #edit-sinopsis").val($(this).data('sinopsis'));
        $(".modal-body #edit-kategori").val($(this).data('kategori'));
        $(".modal-body #edit-rak").val($(this).data('rak'));
        $(".modal-body #edit-stok").val($(this).data('stok'));
    });
</script>
</body>
</html>