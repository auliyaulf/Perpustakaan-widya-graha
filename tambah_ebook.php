<?php
include 'koneksi.php';

if (isset($_POST['submit'])) {
    // Ambil data teks
    $judul    = mysqli_real_escape_string($koneksi, $_POST['judul_ebook']);
    $penulis  = mysqli_real_escape_string($koneksi, $_POST['penulis']);
    $kategori = mysqli_real_escape_string($koneksi, $_POST['kategori']);

    // Konfigurasi File PDF
    $pdf_name = $_FILES['file_pdf']['name'];
    $pdf_tmp  = $_FILES['file_pdf']['tmp_name'];
    $pdf_ext  = strtolower(pathinfo($pdf_name, PATHINFO_EXTENSION));
    
    // Konfigurasi File Cover
    $cover_name = $_FILES['cover_ebook']['name'];
    $cover_tmp  = $_FILES['cover_ebook']['tmp_name'];
    $cover_ext  = strtolower(pathinfo($cover_name, PATHINFO_EXTENSION));

    // Beri nama unik agar tidak bentrok jika judul file sama
    $pdf_new   = "EBOOK_" . time() . "." . $pdf_ext;
    $cover_new = "COVER_" . time() . "." . $cover_ext;

    // Lokasi penyimpanan
    $pdf_dest   = "uploads/ebook/" . $pdf_new;
    $cover_dest = "uploads/cover/" . $cover_new;

    // Validasi Ekstensi PDF
    if ($pdf_ext != 'pdf') {
        echo "<script>alert('Gagal! File E-book harus format PDF.');</script>";
    } else {
        // Proses Upload kedua file
        if (move_uploaded_file($pdf_tmp, $pdf_dest) && move_uploaded_file($cover_tmp, $cover_dest)) {
            
            // Masukkan ke tabel ebook (sesuai struktur phpMyAdmin kamu)
            $query = "INSERT INTO ebook (judul_ebook, penulis, kategori, file_pdf, cover_ebook) 
                      VALUES ('$judul', '$penulis', '$kategori', '$pdf_new', '$cover_new')";
            
            if (mysqli_query($koneksi, $query)) {
                echo "<script>alert('E-book Berhasil Disimpan!'); window.location='tambah_ebook.php';</script>";
            } else {
                echo "Gagal simpan database: " . mysqli_error($koneksi);
            }
        } else {
            echo "<script>alert('Gagal upload! Pastikan folder uploads/ebook dan uploads/cover sudah dibuat.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah E-book - Widya Graha</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root { --navy: #1a3a5f; }
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .card-upload { border: none; border-radius: 15px; box-shadow: 0 8px 20px rgba(0,0,0,0.08); }
        .header-upload { background: var(--navy); color: white; border-radius: 15px 15px 0 0; padding: 20px; }
        .btn-save { background: var(--navy); color: white; border-radius: 8px; padding: 10px 25px; transition: 0.3s; }
        .btn-save:hover { background: #122943; color: white; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-upload">
                <div class="header-upload text-center">
                    <h4 class="m-0"><i class="fas fa-cloud-upload-alt mr-2"></i> Input Koleksi E-book</h4>
                </div>
                <div class="card-body p-4">
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="font-weight-bold">Judul E-book</label>
                            <input type="text" name="judul_ebook" class="form-control" placeholder="Contoh: Pemrograman Java SMK XI" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Penulis</label>
                                    <input type="text" name="penulis" class="form-control" placeholder="Nama penulis...">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Kategori</label>
                                    <select name="kategori" class="form-control">
                                        <option value="Teknologi">Teknologi</option>
                                        <option value="Bahasa">Bahasa</option>
                                        <option value="Novel">Novel</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr>
                        
                        <div class="form-group">
                            <label class="font-weight-bold text-primary">File E-book (PDF)</label>
                            <input type="file" name="file_pdf" class="form-control-file" accept=".pdf" required>
                            <small class="text-muted">Maksimal ukuran file disesuaikan dengan server (php.ini).</small>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold text-primary">Cover Sampul (Gambar)</label>
                            <input type="file" name="cover_ebook" class="form-control-file" accept="image/*" required>
                            <small class="text-muted">Format: JPG, PNG, atau JPEG.</small>
                        </div>

                        <div class="mt-4 text-right">
                            <a href="dashboard_admin.php" class="btn btn-light mr-2">Batal</a>
                            <button type="submit" name="submit" class="btn btn-save">
                                <i class="fas fa-save mr-2"></i> Simpan ke Koleksi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <p class="text-center mt-4 text-muted small">&copy; 2026 Perpustakaan Widya Graha - SMK Negeri Kebumen</p>
        </div>
    </div>
</div>

</body>
</html>