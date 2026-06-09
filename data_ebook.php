<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['username'])){
    header("location:login.php");
    exit;
}

// --- LOGIKA HAPUS ---
if(isset($_GET['hapus'])){
    $id = $_GET['hapus'];
    $cari = mysqli_query($koneksi, "SELECT cover_ebook, file_pdf FROM ebook WHERE id_ebook = '$id'");
    $dt = mysqli_fetch_assoc($cari);
    if($dt){
        // Hapus file fisik dari folder agar tidak menumpuk
        if(file_exists("uploads/cover/".$dt['cover_ebook'])) unlink("uploads/cover/".$dt['cover_ebook']);
        if(file_exists("uploads/ebook/".$dt['file_pdf'])) unlink("uploads/ebook/".$dt['file_pdf']);
        
        mysqli_query($koneksi, "DELETE FROM ebook WHERE id_ebook = '$id'");
    }
    echo "<script>alert('Data Berhasil Dihapus'); window.location='data_ebook.php';</script>";
}

// --- LOGIKA TAMBAH ---
if(isset($_POST['simpan_ebook'])){
    $judul    = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $penulis  = mysqli_real_escape_string($koneksi, $_POST['penulis']);
    $id_kat   = $_POST['id_kategori'];

    // Penamaan file unik menggunakan time()
    $cv_baru = time()."_".$_FILES['cover']['name'];
    $pd_baru = time()."_".$_FILES['pdf']['name'];

    if(move_uploaded_file($_FILES['cover']['tmp_name'], "uploads/cover/".$cv_baru) && 
       move_uploaded_file($_FILES['pdf']['tmp_name'], "uploads/ebook/".$pd_baru)){
        
        mysqli_query($koneksi, "INSERT INTO ebook (judul_ebook, penulis, id_kategori, cover_ebook, file_pdf) 
                                VALUES ('$judul', '$penulis', '$id_kat', '$cv_baru', '$pd_baru')");
        echo "<script>alert('E-Book Berhasil Ditambahkan'); window.location='data_ebook.php';</script>";
    }
}

// --- LOGIKA UPDATE ---
if(isset($_POST['update_ebook'])){
    $id      = $_POST['id_ebook'];
    $judul   = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $penulis = mysqli_real_escape_string($koneksi, $_POST['penulis']);
    $id_kat  = $_POST['id_kategori'];

    mysqli_query($koneksi, "UPDATE ebook SET judul_ebook='$judul', penulis='$penulis', id_kategori='$id_kat' WHERE id_ebook='$id'");

    // Update Cover jika ada file baru
    if($_FILES['cover']['name'] != ""){
        $cv_baru = time()."_".$_FILES['cover']['name'];
        move_uploaded_file($_FILES['cover']['tmp_name'], "uploads/cover/".$cv_baru);
        mysqli_query($koneksi, "UPDATE ebook SET cover_ebook='$cv_baru' WHERE id_ebook='$id'");
    }

    // Update PDF jika ada file baru
    if($_FILES['pdf']['name'] != ""){
        $pd_baru = time()."_".$_FILES['pdf']['name'];
        move_uploaded_file($_FILES['pdf']['tmp_name'], "uploads/ebook/".$pd_baru);
        mysqli_query($koneksi, "UPDATE ebook SET file_pdf='$pd_baru' WHERE id_ebook='$id'");
    }
    echo "<script>alert('Data Berhasil Diperbarui'); window.location='data_ebook.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data E-Book - Widya Graha</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css"> <!-- Pastikan file CSS sidebar kamu ada -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root { --navy: #003366; --gold: #f19f2c; }
        body { background-color: #f4f7f6; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
        .breadcrumb-container {
            background-color: white; padding: 15px 25px; border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05); margin-bottom: 20px;
            display: flex; justify-content: space-between; align-items: center;
        }
        .breadcrumb-text { font-size: 14px; color: #888; margin: 0; }
        .breadcrumb-text b { color: var(--navy); }
        .date-box { background: #fff; border: 1px solid #eee; padding: 5px 15px; border-radius: 8px; font-weight: bold; font-size: 13px; }
        
        .badge-kategori { background-color: #31b0d5; color: white; border-radius: 5px; padding: 5px 12px; font-size: 11px; }
        .img-cover { width: 45px; height: 60px; object-fit: cover; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .btn-action { background: none; border: none; font-size: 1.1rem; padding: 0 5px; cursor: pointer; transition: 0.2s; }
        .btn-action:hover { transform: scale(1.2); }
        
        .table thead th { border: none; color: #999; font-size: 11px; text-transform: uppercase; letter-spacing: 1px; }
        .card { border-radius: 15px; border: none; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <img src="Logoperpus.png" alt="Logo" class="logo-custom">
        <h2>PERPUSTAKAAN</h2>
        <span>Widya Graha</span>
    </div>
    <nav class="sidebar-nav">
        <ul class="sidebar-menu">
            <li class="menu-item"><a href="admin.php"><i class="fas fa-th-large"></i> <span>Dashboard</span></a></li>
            <li class="menu-item"><a href="buku.php"><i class="fas fa-book-open"></i> <span>Koleksi Buku</span></a></li>
            <li class="menu-item active"><a href="data_ebook.php"><i class="fas fa-file-pdf"></i> <span>Data E-Book</span></a></li>
            <li class="menu-item"><a href="anggota.php"><i class="fas fa-user-friends"></i> <span>Data Anggota</span></a></li>
            <li class="menu-item"><a href="transaksi.php"><i class="fas fa-exchange-alt"></i> <span>Transaksi</span></a></li>
             <li class="menu-item"><a href="peraturan.php"><i class="fas fa-gavel"></i> <span>Kelola Peraturan</span></a></li>
           <li class="menu-item"><a href="profil_admin.php"><i class="fas fa-user-shield"></i> <span>Profil Admin</span></a></li>
            <li class="menu-item logout"><a href="logout.php"><i class="fas fa-power-off"></i> <span>Keluar</span></a></li>
        </ul>
    </nav>
</div>

<div class="main-content">
    <div class="container-fluid px-4 pt-4">
        
        <div class="breadcrumb-container">
            <p class="breadcrumb-text">Panel Admin / <b>Data E-Book</b></p>
            <div class="date-box shadow-sm">
                <i class="fas fa-calendar-alt mr-2"></i> <?= date('d M Y') ?>
            </div>
        </div>

        <div class="card shadow-sm p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <button class="btn btn-primary font-weight-bold px-4" style="background-color: var(--navy); border: none; border-radius: 8px;" data-toggle="modal" data-target="#modalTambah">
                    <i class="fas fa-plus-circle mr-2"></i> Tambah E-Book
                </button>
                <?php
                $hitung = mysqli_query($koneksi, "SELECT count(*) as total FROM ebook");
                $total = mysqli_fetch_assoc($hitung);
                ?>
                <div class="text-muted small">Total: <b><?= $total['total']; ?> E-Book</b></div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>SAMPUL</th>
                            <th>JUDUL & PENULIS</th>
                            <th class="text-center">KATEGORI</th>
                            <th class="text-center">FILE</th>
                            <th class="text-center">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $query = mysqli_query($koneksi, "SELECT ebook.*, kategori.nama_kategori 
                                                        FROM ebook 
                                                        LEFT JOIN kategori ON ebook.id_kategori = kategori.id_kategori 
                                                        ORDER BY id_ebook DESC");
                        while ($row = mysqli_fetch_assoc($query)) :
                        ?>
                        <tr>
                            <td class="align-middle text-muted"><?= $no++; ?></td>
                            <td class="align-middle">
                                <img src="uploads/cover/<?= $row['cover_ebook']; ?>" class="img-cover" onerror="this.src='https://via.placeholder.com/45x60?text=No+Img'">
                            </td>
                            <td class="align-middle">
                                <div class="font-weight-bold" style="color: var(--navy);"><?= $row['judul_ebook']; ?></div>
                                <small class="text-muted"><?= $row['penulis']; ?></small>
                            </td>
                            <td class="text-center align-middle">
                                <span class="badge-kategori"><?= $row['nama_kategori'] ?? 'Umum'; ?></span>
                            </td>
                            <td class="text-center align-middle">
                                <a href="uploads/ebook/<?= $row['file_pdf']; ?>" target="_blank" title="Lihat PDF">
                                    <i class="fas fa-file-pdf text-danger fa-2x"></i>
                                </a>
                            </td>
                            <td class="text-center align-middle">
                                <button class="btn-action text-warning btn-edit" 
                                        data-id="<?= $row['id_ebook']; ?>" 
                                        data-judul="<?= htmlspecialchars($row['judul_ebook']); ?>" 
                                        data-penulis="<?= htmlspecialchars($row['penulis']); ?>" 
                                        data-kategori="<?= $row['id_kategori']; ?>" 
                                        data-toggle="modal" data-target="#modalEdit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="?hapus=<?= $row['id_ebook']; ?>" class="btn-action text-danger" onclick="return confirm('Hapus E-Book ini?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL TAMBAH -->
<div class="modal fade" id="modalTambah" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header text-white" style="background: var(--navy)">
                <h5 class="modal-title">Tambah E-Book</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Judul E-Book</label>
                        <input type="text" name="judul" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Penulis</label>
                        <input type="text" name="penulis" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="id_kategori" class="form-control" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php 
                            $kat = mysqli_query($koneksi, "SELECT * FROM kategori");
                            while($k = mysqli_fetch_assoc($kat)): ?>
                                <option value="<?= $k['id_kategori'] ?>"><?= $k['nama_kategori'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Cover (Gambar)</label>
                        <input type="file" name="cover" class="form-control-file" accept="image/*" required>
                    </div>
                    <div class="form-group">
                        <label>File E-Book (PDF)</label>
                        <input type="file" name="pdf" class="form-control-file" accept=".pdf" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="simpan_ebook" class="btn btn-primary btn-block">Simpan E-Book</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT -->
<div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Edit Data E-Book</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id_ebook" id="edit-id">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Judul E-Book</label>
                        <input type="text" name="judul" id="edit-judul" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Penulis</label>
                        <input type="text" name="penulis" id="edit-penulis" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Kategori</label>
                        <select name="id_kategori" id="edit-kategori" class="form-control" required>
                            <?php 
                            $kat2 = mysqli_query($koneksi, "SELECT * FROM kategori");
                            while($k2 = mysqli_fetch_assoc($kat2)): ?>
                                <option value="<?= $k2['id_kategori'] ?>"><?= $k2['nama_kategori'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Ganti Cover <small class="text-danger">*Kosongkan jika tidak ganti</small></label>
                        <input type="file" name="cover" class="form-control-file" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label>Ganti PDF <small class="text-danger">*Kosongkan jika tidak ganti</small></label>
                        <input type="file" name="pdf" class="form-control-file" accept=".pdf">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="update_ebook" class="btn btn-warning btn-block">Update Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Script untuk memindahkan data dari tabel ke dalam form Modal Edit
    $(document).on("click", ".btn-edit", function() {
        var id = $(this).data('id');
        var judul = $(this).data('judul');
        var penulis = $(this).data('penulis');
        var kategori = $(this).data('kategori');

        $("#edit-id").val(id);
        $("#edit-judul").val(judul);
        $("#edit-penulis").val(penulis);
        $("#edit-kategori").val(kategori);
    });
</script>

</body>
</html>