$query = mysqli_query($koneksi, "SELECT * FROM ebook");
while($data = mysqli_fetch_assoc($query)) {
    echo "
    <div class='card'>
        <img src='uploads/cover/".$data['cover_ebook']."'>
        <h5>".$data['judul_ebook']."</h5>
        <a href='view_pdf.php?file=".$data['file_pdf']."' class='btn btn-primary'>Baca Sekarang</a>
    </div>";
}