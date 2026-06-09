<?php
session_start();
include 'koneksi.php';

// 1. Proteksi Halaman
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'siswa') {
    header("location:login.php");
    exit;
}

// 2. Mengambil Data User
$id_siswa = $_SESSION['id_user'] ?? 0;
$username_session = $_SESSION['username'];
$query_user = mysqli_query($koneksi, "SELECT * FROM anggota WHERE nis='$username_session'");
$user = mysqli_fetch_assoc($query_user);

// Ambil nama siswa untuk chatbox (mencegah error undefined variable)
$nama_siswa = $user['nama_anggota'] ?? $username_session;

date_default_timezone_set('Asia/Jakarta');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Book Digital - Widya Graha</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="siswa_ebook.css">
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <img src="Logoperpus.png" alt="Logo Perpus" class="logo-custom">
        <h2>PERPUSTAKAAN</h2>
        <span>Widya Graha</span>
    </div>
    <ul class="sidebar-menu">
        <li class="menu-item"><a href="siswa.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
        <li class="menu-item"><a href="katalog_siswa.php"><i class="fas fa-book-open"></i> Koleksi Buku</a></li>
        <li class="menu-item active"><a href="data_ebook.php"><i class="fas fa-file-pdf"></i> Koleksi E-Book</a></li>
        <li class="menu-item"><a href="daftar_peraturan.php"><i class="fas fa-gavel"></i> Peraturan</a></li>
        <li class="menu-item"><a href="profil.php"><i class="fas fa-user-circle"></i> Profil</a></li>
        <li class="menu-item logout"><a href="logout.php" onclick="return confirm('Yakin ingin keluar?')"><i class="fas fa-power-off"></i> Keluar</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="top-bar">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item" style="color: #999; font-size: 13px;">Panel Siswa</li>
                <li class="breadcrumb-item active" aria-current="page" style="color: var(--navy); font-weight: 700; font-size: 13px;">Koleksi E-Book</li>
            </ol>
        </nav>
        <div style="font-size: 13px; font-weight: 600; color: #555;">
            <i class="fas fa-user-circle mr-1" style="color: var(--navy);"></i> <?= htmlspecialchars($username_session) ?>
        </div>
    </div>

    <div class="container-fluid-custom">
        <div style="margin-bottom: 30px;">
            <h2 style="font-weight: 700; color: var(--navy); margin-bottom: 5px; font-size: 26px;">E-Book Digital 📱</h2>
            <p style="color: #888; margin: 0; font-size: 15px;">Baca koleksi buku digital SMK Widya Graha langsung dari sini.</p>
        </div>

        <div class="buku-grid" id="wadah-ebook">
            <?php
            // PERBAIKAN: Menggunakan JOIN untuk mengambil nama_kategori dari tabel kategori
            $query_str = "SELECT ebook.*, kategori.nama_kategori 
                          FROM ebook 
                          LEFT JOIN kategori ON ebook.id_kategori = kategori.id_kategori 
                          ORDER BY ebook.id_ebook DESC";
            
            $query = mysqli_query($koneksi, $query_str);

            if(mysqli_num_rows($query) > 0) :
                while ($row = mysqli_fetch_assoc($query)) :
                    // Ambil nama kategori, jika null tampilkan 'Umum'
                    $kategori_nama = $row['nama_kategori'] ?? 'Umum';
            ?>
            <div class="buku-card">
                <div class="sampul">
                    <!-- Tampilan Badge sesuai Nama Kategori, bukan ID lagi -->
                    <span class="badge-kategori"><?= htmlspecialchars($kategori_nama); ?></span>
                    <img src="uploads/cover/<?= $row['cover_ebook']; ?>" onerror="this.src='https://via.placeholder.com/200x300?text=No+Cover'" alt="Cover">
                </div>
                <div class="info">
                    <h4><?= htmlspecialchars($row['judul_ebook']); ?></h4>
                    <p><i class="fas fa-user-edit mr-2"></i><?= htmlspecialchars($row['penulis']); ?></p>
                    <p><i class="fas fa-file-pdf mr-2"></i>Format PDF</p>
                </div>
                <a href="uploads/ebook/<?= $row['file_pdf']; ?>" target="_blank" class="btn-baca">
                    <i class="fas fa-eye mr-2"></i> BACA SEKARANG
                </a>
            </div>
            <?php endwhile; else : ?>
                <div class="text-center w-100 py-5"><p class="text-muted">Belum ada e-book yang tersedia.</p></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Wiga Chatbox Section -->
<div id="wiga-container" style="position: fixed; bottom: 20px; right: 20px; z-index: 1000; display: flex; flex-direction: column; align-items: flex-end; gap: 10px;">
    <div id="wiga-chatbox" style="display: none; width: 300px; height: 400px; background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.2); flex-direction: column;">
        <div style="background: linear-gradient(135deg, #FF9800, #E65100); padding: 15px; color: white; display: flex; justify-content: space-between; align-items: center;">
            <div><strong>Wiga Assistant</strong><br><small>Pustakawan Widya Graha</small></div>
            <button onclick="toggleWiga()" style="background:none; border:none; color:white; cursor:pointer;"><i class="fas fa-times"></i></button>
        </div>
        <div id="ai-chat-isi" style="flex: 1; padding: 15px; overflow-y: auto; background: #121212; display: flex; flex-direction: column; gap: 10px;">
            <div style="background: #333; color: #ddd; padding: 10px; border-radius: 0 10px 10px 10px; font-size: 13px; max-width: 80%;">
                Halo <?= htmlspecialchars($nama_siswa); ?>! Mau cari E-Book apa?
            </div>
        </div>
        <div style="padding: 10px; background: #1a1a1a; border-top: 1px solid #333; display: flex; gap: 5px;">
            <input type="text" id="ai-input-pesan" placeholder="Tanya Wiga..." style="flex: 1; background: #222; border: 1px solid #444; color: white; padding: 8px 12px; border-radius: 20px; outline: none;">
            <button onclick="kirimChatAI()" style="background: #E65100; border: none; color: white; width: 35px; height: 35px; border-radius: 50%; cursor: pointer;"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>
    <button onclick="toggleWiga()" style="width: 60px; height: 60px; background: #E65100; border: none; border-radius: 50%; color: white; font-size: 24px; cursor: pointer; box-shadow: 0 5px 15px rgba(230, 81, 0, 0.4);">
        <i class="fas fa-robot"></i>
    </button>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
function toggleWiga() {
    const chatbox = document.getElementById('wiga-chatbox');
    chatbox.style.display = (chatbox.style.display === 'none' || chatbox.style.display === '') ? 'flex' : 'none';
}

function filterEbook(keyword) {
    const kartuBuku = document.querySelectorAll('.buku-card');
    keyword = keyword.toLowerCase();
    kartuBuku.forEach(card => {
        const judul = card.querySelector('h4').innerText.toLowerCase();
        const penulis = card.querySelector('p').innerText.toLowerCase();
        card.style.display = (judul.includes(keyword) || penulis.includes(keyword)) ? "flex" : "none";
    });
}

async function kirimChatAI() {
    const inputPesan = document.getElementById('ai-input-pesan');
    const chatIsi = document.getElementById('ai-chat-isi');
    const pesan = inputPesan.value.trim();
    if (pesan === "") return;

    chatIsi.innerHTML += `<div style="align-self: flex-end; background: #E65100; color: white; padding: 10px; border-radius: 10px 0 10px 10px; font-size: 13px; max-width: 80%;">${pesan}</div>`;
    inputPesan.value = ""; 
    chatIsi.scrollTop = chatIsi.scrollHeight;

    try {
        const response = await fetch('http://localhost:3000/chat', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                message: pesan,
                context: "Kamu adalah Wiga. Jika user mencari buku, jawab ramah dan wajib sertakan format 'MENCARI: [judul]' agar sistem memfilter otomatis." 
            })
        });

        const data = await response.json();
        const balasan = data.choices[0].message.content;

        chatIsi.innerHTML += `<div style="background: #333; color: #ddd; padding: 10px; border-radius: 0 10px 10px 10px; font-size: 13px; max-width: 80%;">${balasan}</div>`;
        chatIsi.scrollTop = chatIsi.scrollHeight;

        if (balasan.includes("MENCARI:")) {
            const keyword = balasan.split("MENCARI:")[1].trim().split(" ")[0];
            filterEbook(keyword);
        } else if (pesan.toLowerCase().includes("reset") || pesan.toLowerCase().includes("semua")) {
            filterEbook("");
        }

    } catch (error) {
        chatIsi.innerHTML += `<div style="color: #ff4444; font-size: 11px; text-align: center;">Wiga sedang offline.</div>`;
    }
}

document.getElementById('ai-input-pesan').addEventListener('keypress', function (e) {
    if (e.key === 'Enter') kirimChatAI();
});
</script>

</body>
</html>