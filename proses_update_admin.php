<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Ambil data dari form
    $user_aktif      = $_SESSION['username'];
    $nama_pustakawan = mysqli_real_escape_string($koneksi, $_POST['nama_pustakawan']);
    $no_telp         = mysqli_real_escape_string($koneksi, $_POST['no_telp']);
    $password_baru   = $_POST['password_baru'];

    // 2. Ambil data lama untuk cek foto lama
    $query_lama = mysqli_query($koneksi, "SELECT * FROM pustakawan WHERE username='$user_aktif'");
    $data_lama  = mysqli_fetch_assoc($query_lama);
    $foto_lama  = $data_lama['foto'];

    // 3. Logika Upload Foto
    $nama_file = $_FILES['foto']['name'];
    $tmp_file  = $_FILES['foto']['tmp_name'];

    if (!empty($nama_file)) {
        // Beri nama unik pada foto (contoh: 02052026_admin.png)
        $ekstensi    = pathinfo($nama_file, PATHINFO_EXTENSION);
        $foto_baru   = date('dmYHis') . "_" . $user_aktif . "." . $ekstensi;
        $path_upload = "img/admin/" . $foto_baru;

        // Pindahkan file baru
        if (move_uploaded_file($tmp_file, $path_upload)) {
            // Hapus foto lama jika bukan foto default
            if (!empty($foto_lama) && file_exists("img/admin/" . $foto_lama)) {
                unlink("img/admin/" . $foto_lama);
            }
            $foto_final = $foto_baru;
        } else {
            $foto_final = $foto_lama;
        }
    } else {
        $foto_final = $foto_lama;
    }

    // 4. Logika Update Data & Password
    if (!empty($password_baru)) {
        // Jika password diisi (Gunakan password_hash jika di login juga pakai hash)
        $query_update = "UPDATE pustakawan SET 
                         nama_pustakawan = '$nama_pustakawan', 
                         no_telp = '$no_telp', 
                         password = '$password_baru', 
                         foto = '$foto_final' 
                         WHERE username = '$user_aktif'";
    } else {
        // Jika password kosong
        $query_update = "UPDATE pustakawan SET 
                         nama_pustakawan = '$nama_pustakawan', 
                         no_telp = '$no_telp', 
                         foto = '$foto_final' 
                         WHERE username = '$user_aktif'";
    }

    // 5. Eksekusi Query
    if (mysqli_query($koneksi, $query_update)) {
        echo "<script>
                alert('Data Berhasil Diperbarui!');
                window.location='profil_admin.php';
              </script>";
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
} else {
    header("location:profil_admin.php");
}
?>