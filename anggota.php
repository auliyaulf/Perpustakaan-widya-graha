<?php
session_start();
include 'koneksi.php'; 

// Proteksi Halaman
if(!isset($_SESSION['username'])){
    header("location:login.php");
    exit;
}

// --- LOGIKA PROSES ANGGOTA ---
if (isset($_POST['simpan'])) {
    $nis     = mysqli_real_escape_string($koneksi, $_POST['nis']);
    $nama    = mysqli_real_escape_string($koneksi, $_POST['nama_anggota']);
    $jk      = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
    $telp    = mysqli_real_escape_string($koneksi, $_POST['no_telp']);
    $alamat  = mysqli_real_escape_string($koneksi, $_POST['alamat']);

    // Proses Upload Foto
    $foto = $_FILES['foto']['name'];
    $tmp  = $_FILES['foto']['tmp_name'];
    
    if(!empty($foto)){
        $fotobaru = date('dmYHis').$foto;
        $path = "img/siswa/".$fotobaru;
        if(move_uploaded_file($tmp, $path)){
            mysqli_query($koneksi, "INSERT INTO anggota (nis, nama_anggota, jenis_kelamin, no_telp, alamat, foto) VALUES ('$nis', '$nama', '$jk', '$telp', '$alamat', '$fotobaru')");
        }
    } else {
        mysqli_query($koneksi, "INSERT INTO anggota (nis, nama_anggota, jenis_kelamin, no_telp, alamat) VALUES ('$nis', '$nama', '$jk', '$telp', '$alamat')");
    }
    header("location:anggota.php?pesan=tambah_berhasil");
}

if (isset($_POST['update'])) {
    $id      = $_POST['id_anggota'];
    $nis     = mysqli_real_escape_string($koneksi, $_POST['nis']);
    $nama    = mysqli_real_escape_string($koneksi, $_POST['nama_anggota']);
    $jk      = mysqli_real_escape_string($koneksi, $_POST['jenis_kelamin']);
    $telp    = mysqli_real_escape_string($koneksi, $_POST['no_telp']);
    $alamat  = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    
    $foto    = $_FILES['foto']['name'];
    $tmp     = $_FILES['foto']['tmp_name'];

    if(empty($foto)){
        mysqli_query($koneksi, "UPDATE anggota SET nis='$nis', nama_anggota='$nama', jenis_kelamin='$jk', no_telp='$telp', alamat='$alamat' WHERE id_anggota='$id'");
    } else {
        $fotobaru = date('dmYHis').$foto;
        $path = "img/siswa/".$fotobaru;
        
        if(move_uploaded_file($tmp, $path)){
            // Hapus foto lama
            $query = mysqli_query($koneksi, "SELECT foto FROM anggota WHERE id_anggota='$id'");
            $data = mysqli_fetch_array($query);
            if(!empty($data['foto']) && file_exists("img/siswa/".$data['foto'])) unlink("img/siswa/".$data['foto']);

            mysqli_query($koneksi, "UPDATE anggota SET nis='$nis', nama_anggota='$nama', jenis_kelamin='$jk', no_telp='$telp', alamat='$alamat', foto='$fotobaru' WHERE id_anggota='$id'");
        }
    }
    header("location:anggota.php?pesan=update_berhasil");
}

if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    // Hapus file fisik foto sebelum hapus data di DB
    $query = mysqli_query($koneksi, "SELECT foto FROM anggota WHERE id_anggota='$id'");
    $data = mysqli_fetch_array($query);
    if(!empty($data['foto']) && file_exists("img/siswa/".$data['foto'])) unlink("img/siswa/".$data['foto']);

    mysqli_query($koneksi, "DELETE FROM anggota WHERE id_anggota='$id'");
    header("location:anggota.php?pesan=hapus_berhasil");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Anggota - Widya Graha</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="Logoperpus.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="anggota.css">
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
            <li class="menu-item"><a href="buku.php"><i class="fas fa-book-open"></i> <span>Koleksi Buku</span></a></li>
            <li class="menu-item"><a href="data_ebook.php"><i class="fas fa-file-pdf"></i> <span>Data E-Book</span></a></li>
            <li class="menu-item active"><a href="anggota.php"><i class="fas fa-user-friends"></i> <span>Data Anggota</span></a></li>
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
        <span style="color: #888;">Panel Admin / <b>Kelola Anggota</b></span>
         <span class="badge badge-light p-2"><i class="fas fa-calendar-alt"></i> &nbsp; <?= date('d F Y') ?></span>
    </div>

    <div class="content-body">
        <div class="card card-custom">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <button class="btn btn-primary" data-toggle="modal" data-target="#modalTambah" style="background: var(--navy); border: none; border-radius: 8px; font-weight: 600; padding: 10px 20px;">
                    <i class="fas fa-plus-circle mr-2"></i> Tambah Anggota Baru
                </button>
                <div class="text-muted">Total: <b style="color: var(--navy);"><?php echo mysqli_num_rows(mysqli_query($koneksi, "SELECT * FROM anggota")); ?></b> Orang</div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr style="font-size: 11px; text-transform: uppercase; color: #999; letter-spacing: 1px;">
                            <th>No</th><th>Profil</th><th>NIS</th><th>Info Anggota</th><th>Gender</th><th>No. Telp</th><th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $data = mysqli_query($koneksi, "SELECT * FROM anggota ORDER BY id_anggota DESC");
                        while($d = mysqli_fetch_array($data)){
                        ?>
                        <tr>
                            <td class="align-middle"><?= $no++; ?></td>
                            <td class="align-middle">
                                <?php if(empty($d['foto'])): ?>
                                    <img src="img/siswa/default.png" width="45" height="45" style="border-radius: 10px; object-fit: cover;">
                                <?php else: ?>
                                    <img src="img/siswa/<?= $d['foto']; ?>" width="45" height="45" style="border-radius: 10px; object-fit: cover;">
                                <?php endif; ?>
                            </td>
                            <td class="align-middle"><b><?= $d['nis']; ?></b></td>
                            <td class="align-middle">
                                <div style="font-weight: 700; color: var(--navy);"><?= $d['nama_anggota']; ?></div>
                                <small class="text-muted"><?= $d['alamat']; ?></small>
                            </td>
                            <td class="align-middle"><span class="badge-status"><?= $d['jenis_kelamin']; ?></span></td>
                            <td class="align-middle"><?= $d['no_telp']; ?></td>
                            <td class="align-middle text-center">
                                <button class="btn btn-sm btn-light edit-btn" style="color: #f1c40f;" 
                                        data-id="<?= $d['id_anggota']; ?>" 
                                        data-nis="<?= $d['nis']; ?>" 
                                        data-nama="<?= $d['nama_anggota']; ?>" 
                                        data-jk="<?= $d['jenis_kelamin']; ?>" 
                                        data-telp="<?= $d['no_telp']; ?>" 
                                        data-alamat="<?= $d['alamat']; ?>"
                                        data-toggle="modal" data-target="#modalEdit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="anggota.php?hapus=<?= $d['id_anggota']; ?>" class="btn btn-sm btn-light" style="color: #e74c3c;" onclick="return confirm('Hapus anggota ini?')">
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

    <footer>
        &copy; <?= date('Y') ?> <b>Widya Graha</b> SMK Negeri Kebumen. All Rights Reserved.
    </footer>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 style="font-weight: 700; color: var(--navy);">Tambah Anggota</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form action="anggota.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Foto Profil</label>
                        <input type="file" name="foto" class="form-control-file">
                    </div>
                    <div class="form-group">
                        <label>NIS</label>
                        <input type="text" name="nis" class="form-control" placeholder="Masukkan NIS" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Anggota</label>
                        <input type="text" name="nama_anggota" class="form-control" placeholder="Nama Lengkap" required>
                    </div>
                    <div class="form-group">
                        <label>Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-control" required>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>No. Telp</label>
                        <input type="text" name="no_telp" class="form-control" placeholder="Contoh: 0812..." required>
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat Lengkap" required></textarea>
                    </div>
                    <div class="d-flex mt-4" style="gap: 10px;">
                        <button type="submit" name="simpan" class="btn btn-success" style="flex: 1; border-radius: 10px; font-weight: 700;">Simpan</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal" style="flex: 1; border-radius: 10px; font-weight: 700;">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 style="font-weight: 700; color: var(--navy);">Edit Anggota</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <form action="anggota.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id_anggota" id="edit-id">
                    <div class="form-group">
                        <label>Ganti Foto (Kosongkan jika tidak diubah)</label>
                        <input type="file" name="foto" class="form-control-file">
                    </div>
                    <div class="form-group">
                         <label>NIS</label>
                         <input type="text" name="nis" id="edit-nis" class="form-control" required>
                   </div>
                    <div class="form-group">
                        <label>Nama Anggota</label>
                        <input type="text" name="nama_anggota" id="edit-nama" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Jenis Kelamin</label>
                        <select name="jenis_kelamin" id="edit-jk" class="form-control" required>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>No. Telp</label>
                         <input type="text" name="no_telp" id="edit-telp" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea name="alamat" id="edit-alamat" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="d-flex mt-4" style="gap: 10px;">
                        <button type="submit" name="update" class="btn btn-success" style="flex: 1; border-radius: 10px; font-weight: 700;">Update</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal" style="flex: 1; border-radius: 10px; font-weight: 700;">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).on("click", ".edit-btn", function () {
        $(".modal-body #edit-id").val($(this).data('id'));
        $(".modal-body #edit-nis").val($(this).data('nis'));
        $(".modal-body #edit-nama").val($(this).data('nama'));
        $(".modal-body #edit-jk").val($(this).data('jk'));
        $(".modal-body #edit-telp").val($(this).data('telp'));
        $(".modal-body #edit-alamat").val($(this).data('alamat'));
    });
</script>
</body>
</html>