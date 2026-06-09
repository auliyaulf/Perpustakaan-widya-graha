<?php include 'koneksi.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah User Perpustakaan</title>
</head>
<body>
    <h2>Form Tambah User</h2>
    <form action="" method="POST">
        <input type="text" name="username" placeholder="Username" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        
        <select name="role">
            <option value="admin">Admin</option>
            <option value="siswa">Siswa</option>
        </select><br>
        
        <input type="text" name="nama" placeholder="Nama Lengkap"><br>
        <input type="text" name="kelas" placeholder="Kelas"><br>
        <button type="submit" name="submit">Simpan</button>
    </form>

    <?php
    if (isset($_POST['submit'])) {
        $user  = $_POST['username'];
        // Sebaiknya gunakan password_hash untuk keamanan
        $pass  = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role  = $_POST['role'];
        $nama  = $_POST['nama'];
        $kelas = $_POST['kelas'];

        $query = "INSERT INTO users (username, password, role, nama, kelas) 
                  VALUES ('$user', '$pass', '$role', '$nama', '$kelas')";
        
        if (mysqli_query($koneksi, $query)) {
            echo "<script>alert('Data berhasil disimpan!');</script>";
        } else {
            echo "Error: " . mysqli_error($koneksi);
        }
    }
    ?>
</body>
</html>