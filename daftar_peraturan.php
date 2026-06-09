<?php
session_start();
include 'koneksi.php'; 

// Proteksi halaman - Hanya Siswa yang sudah login
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'siswa') {
    header("location:login.php");
    exit;
}

$username_session = $_SESSION['username'] ?? 'Siswa';

// Ambil data untuk konteks Wiga (agar Wiga tahu peraturan apa saja yang ada)
$context_peraturan = "";
$data_ctx = mysqli_query($koneksi, "SELECT * FROM peraturan");
while($row_ctx = mysqli_fetch_assoc($data_ctx)) {
    $context_peraturan .= $row_ctx['nama_peraturan'] . ": " . $row_ctx['isi_peraturan'] . ". ";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tata Tertib Siswa - Widya Graha</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
 
        <link rel="stylesheet" href="daftar_peraturan.css">

</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <img src="Logoperpus.png" alt="Logo" class="logo-custom">
        <h2>PERPUSTAKAAN</h2>
        <span>WIDYA GRAHA</span>
    </div>
    
    <ul class="sidebar-menu">
        <li class="menu-item">
            <a href="siswa.php"><i class="fas fa-th-large"></i> Dashboard</a>
        </li>
        <li class="menu-item">
            <a href="katalog_siswa.php"><i class="fas fa-book-open"></i> Katalog Buku</a>
        </li>
        <li class="menu-item">
            <a href="siswa_ebook.php"><i class="fas fa-file-pdf"></i> Koleksi Ebook</a>
        </li>
        <li class="menu-item active">
            <a href="daftar_peraturan.php"><i class="fas fa-gavel"></i> Peraturan</a>
        
        </li>
        <li class="menu-item">
            <a href="profil.php"><i class="fas fa-user-circle"></i> Profil</a></li>
        
        </li>
        <li class="menu-item logout">
            <a href="logout.php" onclick="return confirm('Ingin keluar dari aplikasi?')">
                <i class="fas fa-power-off"></i> Keluar
            </a>
        </li>
    </ul>
</div>

<div class="main-content">
    <div class="top-bar">
        <div style="font-size: 13px; color: #999;">
            Panel Siswa / <b style="color: #1a3a5f;">Tata Tertib</b>
        </div>
        <div style="font-size: 13px; font-weight: 600; color: #555;">
            <i class="fas fa-user-circle mr-1" style="color: #1a3a5f;"></i> <?= htmlspecialchars($username_session) ?>
        </div>
    </div>

    <div class="container-fluid-custom">
        <div class="card-main">
            <div class="header-info">
                <div class="header-icon">
                    <i class="fas fa-gavel"></i>
                </div>
                <div>
                    <h4 class="mb-1" style="font-weight: 700; color: #1a3a5f;">Ketentuan & Peraturan</h4>
                    <p class="text-muted mb-0 small">Pedoman pengunjung Perpustakaan Widya Graha</p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <?php 
                    $data = mysqli_query($koneksi, "SELECT * FROM peraturan ORDER BY id_peraturan ASC");
                    if(mysqli_num_rows($data) > 0) {
                        while($d = mysqli_fetch_array($data)){ 
                    ?>
                    <div class="rule-card">
                        <div class="rule-title">
                            <i class="fas fa-check-circle"></i>
                            <?= htmlspecialchars($d['nama_peraturan']); ?>
                        </div>
                        <p class="rule-text"><?= nl2br(htmlspecialchars($d['isi_peraturan'])); ?></p>
                    </div>
                    <?php 
                        }
                    } else {
                    ?>
                    <div class="text-center py-5">
                        <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="60" style="opacity: 0.1; margin-bottom: 15px;">
                        <p class="text-muted">Belum ada informasi peraturan saat ini.</p>
                    </div>
                    <?php } ?>
                </div>
            </div>
            
            <div class="mt-4 p-3 bg-light rounded" style="border: 1px dashed #ddd;">
                <small class="text-muted">
                    <i class="fas fa-info-circle mr-1"></i> 
                    <b>Catatan:</b> Peraturan di atas berlaku untuk seluruh anggota perpustakaan. Pelanggaran terhadap poin-poin tersebut dapat dikenakan sanksi sesuai ketentuan yang berlaku.
                </small>
            </div>
        </div>
        <footer class="mt-5 mb-4 text-center text-muted" style="font-size: 12px;">
            &copy; <?= date('Y') ?> <b>Widya Graha</b> SMK Negeri Kebumen. All Rights Reserved.
        </footer>
    </div>
</div>

<div id="wiga-container">
    <div id="wiga-chatbox">
        <div style="background: linear-gradient(135deg, #FF9800, #E65100); padding: 15px; color: white; display: flex; justify-content: space-between; align-items: center;">
            <div><strong>Wiga Assistant</strong><br><small>Asisten Peraturan</small></div>
            <button onclick="toggleWiga()" style="background:none; border:none; color:white; cursor:pointer;"><i class="fas fa-times"></i></button>
        </div>
        <div id="ai-chat-isi" style="flex: 1; padding: 15px; overflow-y: auto; background: #121212; display: flex; flex-direction: column; gap: 10px;">
            <div style="background: #333; color: #ddd; padding: 10px; border-radius: 0 10px 10px 10px; font-size: 13px; max-width: 80%;">
                Halo! Ada yang kurang jelas mengenai peraturan di sini? Kamu bisa tanya saya soal denda, durasi pinjam, atau sanksi.
            </div>
        </div>
        <div style="padding: 10px; background: #1a1a1a; border-top: 1px solid #333; display: flex; gap: 5px;">
            <input type="text" id="ai-input-pesan" placeholder="Tanya soal aturan..." style="flex: 1; background: #222; border: 1px solid #444; color: white; padding: 8px 12px; border-radius: 20px; outline: none;">
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

async function kirimChatAI() {
    const inputPesan = document.getElementById('ai-input-pesan');
    const chatIsi = document.getElementById('ai-chat-isi');
    const pesan = inputPesan.value.trim();

    if (pesan === "") return;

    // Tambah chat user
    chatIsi.innerHTML += `<div style="align-self: flex-end; background: #E65100; color: white; padding: 10px; border-radius: 10px 0 10px 10px; font-size: 13px; max-width: 80%;">${pesan}</div>`;
    inputPesan.value = ""; 
    chatIsi.scrollTop = chatIsi.scrollHeight;

    try {
        const response = await fetch('http://localhost:3000/chat', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ 
                message: pesan,
                context: "Kamu adalah Wiga, asisten perpustakaan Widya Graha. Berikut adalah data peraturan perpustakaan kami: <?= $context_peraturan; ?>. Jawab pertanyaan user berdasarkan data peraturan tersebut dengan bahasa yang sopan dan mudah dimengerti siswa." 
            })
        });

        const data = await response.json();
        const balasan = data.choices[0].message.content;

        // Tambah balasan AI
        chatIsi.innerHTML += `<div style="background: #333; color: #ddd; padding: 10px; border-radius: 0 10px 10px 10px; font-size: 13px; max-width: 80%;">${balasan}</div>`;
        chatIsi.scrollTop = chatIsi.scrollHeight;
    } catch (error) {
        chatIsi.innerHTML += `<div style="color: #ff4444; font-size: 11px; text-align: center;">Wiga sedang offline. Pastikan server AI menyala.</div>`;
    }
}

document.getElementById('ai-input-pesan').addEventListener('keypress', function (e) {
    if (e.key === 'Enter') kirimChatAI();
});
</script>

</body>
</html>