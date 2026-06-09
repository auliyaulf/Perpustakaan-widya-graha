<?php
session_start();
include 'koneksi.php';

// 1. Proteksi Halaman
if (!isset($_SESSION['username'])) {
    header("location:login.php");
    exit;
}

/** 
 * Ambil ID Pustakawan dari session login. 
 * Pastikan saat login, kamu sudah membuat $_SESSION['id_pustakawan'] = $data['id_pustakawan'];
 * Jika belum ada, sementara kita set ke '1' sesuai data di phpMyAdmin kamu.
 */
$id_pustakawan_aktif = $_SESSION['id_pustakawan'] ?? 1;

// 2. Logika CRUD (Simpan & Edit)
if (isset($_POST['simpan'])) {
    $id   = mysqli_real_escape_string($koneksi, $_POST['id_peraturan']);
    $nama = mysqli_real_escape_string($koneksi, trim($_POST['nama_peraturan']));
    $isi  = mysqli_real_escape_string($koneksi, trim($_POST['isi_peraturan']));

    if (empty($id)) {
        // INSERT: Menyertakan id_pustakawan agar tidak error Foreign Key
        $query = "INSERT INTO peraturan (nama_peraturan, isi_peraturan, id_pustakawan) 
                  VALUES ('$nama', '$isi', '$id_pustakawan_aktif')";
        $status = "ditambahkan";
    } else {
        // UPDATE: id_pustakawan tidak wajib diupdate kecuali ingin mengganti pembuatnya
        $query = "UPDATE peraturan SET nama_peraturan='$nama', isi_peraturan='$isi' WHERE id_peraturan='$id'";
        $status = "diperbarui";
    }

    if (mysqli_query($koneksi, $query)) {
        $_SESSION['msg'] = "Data berhasil $status!";
        $_SESSION['msg_type'] = "success";
    } else {
        $_SESSION['msg'] = "Gagal memproses data: " . mysqli_error($koneksi);
        $_SESSION['msg_type'] = "danger";
    }
    header("location:peraturan.php");
    exit;
}

// 3. Logika Hapus
if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    if (mysqli_query($koneksi, "DELETE FROM peraturan WHERE id_peraturan='$id'")) {
        $_SESSION['msg'] = "Data berhasil dihapus!";
        $_SESSION['msg_type'] = "success";
    }
    header("location:peraturan.php");
    exit;
}

// 4. Ambil Data untuk Form Edit
$edit_data = ['id_peraturan' => '', 'nama_peraturan' => '', 'isi_peraturan' => ''];
if (isset($_GET['edit'])) {
    $id_edit = mysqli_real_escape_string($koneksi, $_GET['edit']);
    $q_edit = mysqli_query($koneksi, "SELECT * FROM peraturan WHERE id_peraturan='$id_edit'");
    if (mysqli_num_rows($q_edit) > 0) {
        $edit_data = mysqli_fetch_assoc($q_edit);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Peraturan - Widya Graha</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="Logoperpus.css">
    <link rel="stylesheet" href="peraturan.css">
    <style>
        :root { --navy: #1a3a5f; }
        .btn-navy { background: var(--navy); color: white; border-radius: 8px; }
        .btn-navy:hover { background: #003366; color: white; }
        .card-custom { border: none; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .card-title-box { padding: 15px 20px; border-bottom: 1px solid #eee; }
    </style>
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
            <li class="menu-item"><a href="anggota.php"><i class="fas fa-user-friends"></i> <span>Data Anggota</span></a></li>
            <li class="menu-item"><a href="transaksi.php"><i class="fas fa-exchange-alt"></i> <span>Transaksi</span></a></li>
            <li class="menu-item active"><a href="peraturan.php"><i class="fas fa-gavel"></i> <span>Kelola Peraturan</span></a></li>
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
    <div class="top-bar d-flex justify-content-between align-items-center">
        <span style="color: #888;">Panel Admin / <b style="color: #555;">Kelola Peraturan</b></span>
        <span class="badge badge-light p-2"><i class="fas fa-calendar-alt"></i> &nbsp; <?= date('d F Y') ?></span>
    </div>

    <div class="content-body mt-4">
        <?php if (isset($_SESSION['msg'])): ?>
            <div class="alert alert-<?= $_SESSION['msg_type'] ?> alert-dismissible fade show mb-4" role="alert" style="border-radius: 10px;">
                <?= $_SESSION['msg']; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
            <?php unset($_SESSION['msg']); ?>
        <?php endif; ?>

        <div class="row">
            <!-- Form Input -->
            <div class="col-lg-4">
                <div class="card card-custom">
                    <div class="card-title-box">
                        <h5 class="m-0 font-weight-bold" style="color: var(--navy);">
                            <i class="fas fa-edit mr-2 text-warning"></i> <?= !empty($edit_data['id_peraturan']) ? 'Edit' : 'Tambah' ?> Aturan
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST">
                            <input type="hidden" name="id_peraturan" value="<?= $edit_data['id_peraturan'] ?>">
                            <div class="form-group">
                                <label class="font-weight-bold small">NAMA PERATURAN</label>
                                <input type="text" name="nama_peraturan" class="form-control" value="<?= htmlspecialchars($edit_data['nama_peraturan']) ?>" placeholder="Contoh: Tata Tertib Umum" required>
                            </div>
                            <div class="form-group">
                                <label class="font-weight-bold small">ISI PERATURAN</label>
                                <textarea name="isi_peraturan" class="form-control" rows="6" placeholder="Tuliskan poin-poin peraturan..." required><?= htmlspecialchars($edit_data['isi_peraturan']) ?></textarea>
                            </div>
                            <button type="submit" name="simpan" class="btn btn-navy btn-block py-2">
                                <i class="fas fa-save mr-2"></i> Simpan Data
                            </button>
                            <?php if(!empty($edit_data['id_peraturan'])): ?>
                                <a href="peraturan.php" class="btn btn-light btn-block mt-2" style="border: 1px solid #ddd;">Batal Edit</a>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tabel Data -->
            <div class="col-lg-8">
                <div class="card card-custom">
                    <div class="card-title-box">
                        <h5 class="m-0 font-weight-bold" style="color: var(--navy);">
                            <i class="fas fa-list mr-2 text-primary"></i> Tata Tertib Saat Ini
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="pl-4">No</th>
                                        <th>Judul</th>
                                        <th>Keterangan</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    $query = mysqli_query($koneksi, "SELECT * FROM peraturan ORDER BY id_peraturan DESC");
                                    while($row = mysqli_fetch_assoc($query)):
                                    ?>
                                    <tr>
                                        <td class="pl-4"><?= $no++; ?></td>
                                        <td><b style="color: var(--navy);"><?= htmlspecialchars($row['nama_peraturan']); ?></b></td>
                                        <td><small class="text-muted"><?= nl2br(htmlspecialchars($row['isi_peraturan'])); ?></small></td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <a href="?edit=<?= $row['id_peraturan']; ?>" class="btn btn-sm btn-outline-primary mr-1"><i class="fas fa-pen"></i></a>
                                                <a href="?hapus=<?= $row['id_peraturan']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus peraturan ini?')"><i class="fas fa-trash"></i></a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                    <?php if(mysqli_num_rows($query) == 0): ?>
                                        <tr><td colspan='4' class='text-center py-5 text-muted'>Belum ada peraturan.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="mt-5 mb-4 text-center text-muted" style="font-size: 12px;">
        &copy; <?= date('Y') ?> <b>Widya Graha</b> SMK Widya Graha. All Rights Reserved.
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>