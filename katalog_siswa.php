<?php
session_start();
include 'koneksi.php';

// Proteksi halaman - Hanya Siswa
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'siswa') {
    header("location:login.php");
    exit;
}

$id_siswa = $_SESSION['id_user'] ?? 0;
$username_session = $_SESSION['username'] ?? 'Siswa';

// --- LOGIKA DATA BUKU ---
$search = isset($_GET['cari']) ? mysqli_real_escape_string($koneksi, $_GET['cari']) : "";

$query_base = "SELECT buku.*, kategori.nama_kategori, rak.nama_rak 
               FROM buku 
               LEFT JOIN kategori ON buku.id_kategori = kategori.id_kategori 
               LEFT JOIN rak ON buku.id_rak = rak.id_rak";

if ($search != "") {
    $query_sql = "$query_base WHERE 
                  buku.judul_buku LIKE '%$search%' OR 
                  buku.penulis LIKE '%$search%' OR 
                  kategori.nama_kategori LIKE '%$search%' OR 
                  rak.nama_rak LIKE '%$search%' 
                  ORDER BY buku.id_buku DESC";
} else {
    $query_sql = "$query_base ORDER BY buku.id_buku DESC";
}

$query_buku = mysqli_query($koneksi, $query_sql);
$total_buku = mysqli_num_rows($query_buku);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Buku - Widya Graha</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root { 
            --sidebar-bg: #1a3a5f; 
            --sidebar-text: #b8c1cc; 
            --sidebar-active: #f19f2c; 
            --navy-main: #1a3a5f;
        }
        
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; margin: 0; }

        /* --- SIDEBAR STYLE --- */
        .sidebar {
            width: 260px; height: 100vh; background: var(--sidebar-bg);
            position: fixed; left: 0; top: 0; z-index: 1000;
            display: flex; flex-direction: column;
        }
        .sidebar-header { padding: 30px 20px; text-align: center; }
        .logo-custom { width: 65px; margin-bottom: 10px; }
        .sidebar-header h2 { color: white; font-size: 16px; font-weight: 700; margin: 0; text-transform: uppercase; }
        .sidebar-header span { color: var(--sidebar-active); font-size: 11px; font-weight: 600; }
        
        .sidebar-menu { list-style: none; padding: 20px 0; margin: 0; flex-grow: 1; }
        .menu-item a {
            color: var(--sidebar-text); text-decoration: none; display: flex;
            align-items: center; padding: 13px 25px; transition: 0.3s; font-size: 14px;
            border-left: 4px solid transparent;
        }
        .menu-item a i { width: 25px; font-size: 18px; margin-right: 12px; text-align: center; }
        
        .menu-item.active a { 
            background: rgba(255,255,255,0.1); 
            color: var(--sidebar-active) !important; 
            font-weight: 600; 
            border-left: 4px solid var(--sidebar-active); 
        }
        .menu-item a:hover:not(.active a) { background: rgba(255,255,255,0.05); color: white; }

        .sidebar-menu-bottom { padding: 20px 0; border-top: 1px solid rgba(255,255,255,0.05); list-style: none; }
        .menu-item.logout a { color: #e74c3c; }

        /* --- CONTENT STYLE --- */
        .main-content { margin-left: 260px; min-height: 100vh; }
        .top-bar {
            background: white; padding: 15px 40px; display: flex;
            justify-content: space-between; align-items: center; border-bottom: 1px solid #eee;
        }

        .container-fluid-custom { padding: 30px 40px; }
        .header-katalog { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }

        .search-container { position: relative; width: 350px; }
        .search-container input {
            width: 100%; padding: 10px 50px 10px 20px; border-radius: 25px;
            border: 1px solid #ddd; outline: none;
        }
        .search-container button {
            position: absolute; right: 5px; top: 5px; border: none;
            background: var(--navy-main); color: white; width: 35px; height: 35px; border-radius: 50%;
        }

        /* --- GRID BUKU --- */
        .buku-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 25px; }
        .buku-card {
            background: white; border-radius: 12px; overflow: hidden; border: 1px solid #eee;
            display: flex; flex-direction: column; height: 100%; transition: 0.3s;
        }
        .buku-card:hover { transform: translateY(-8px); box-shadow: 0 10px 20px rgba(0,0,0,0.08); }
        .sampul { height: 280px; position: relative; background: #f9f9f9; }
        .sampul img { width: 100%; height: 100%; object-fit: cover; }
        
        .badge-kategori { position: absolute; top: 10px; left: 10px; background: rgba(26,58,95,0.85); color: white; padding: 4px 8px; border-radius: 5px; font-size: 10px; }
        .badge-stok { position: absolute; top: 10px; right: 10px; padding: 4px 8px; border-radius: 5px; font-size: 10px; font-weight: bold; }
        .stok-ada { background: #27ae60; color: white; }
        .stok-habis { background: #e74c3c; color: white; }

        .info { padding: 15px; flex-grow: 1; }
        .info h4 { font-size: 15px; color: var(--navy-main); font-weight: 700; margin-bottom: 8px; height: 40px; overflow: hidden; }
        .rak-label { display: inline-block; background: #fff3cd; color: #856404; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: bold; }

        .btn-detail {
            display: block; text-align: center; padding: 12px; background: var(--navy-main);
            color: white !important; font-size: 12px; font-weight: bold; text-decoration: none;
        }

        /* --- WIGA CHATBOX --- */
        #wiga-chatbox {
            display: none; width: 350px; height: 450px; background: #1a1a1a;
            border-radius: 15px; position: fixed; bottom: 90px; right: 20px;
            z-index: 10000; flex-direction: column; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.4);
        }
        #ai-chat-isi { flex: 1; padding: 15px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px; background: #121212; }
        .msg-wiga { background: #333; color: #ddd; padding: 10px; border-radius: 0 10px 10px 10px; font-size: 13px; align-self: flex-start; max-width: 85%; line-height: 1.5; }
        .msg-user { background: var(--sidebar-active); color: white; padding: 10px; border-radius: 10px 10px 0 10px; font-size: 13px; align-self: flex-end; max-width: 80%; }

        @media (max-width: 768px) { .sidebar { left: -260px; } .main-content { margin-left: 0; } }
    </style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-header">
            <img src="Logoperpus.png" alt="Logo" class="logo-custom">
            <h2>PERPUSTAKAAN</h2>
            <span>WIDYA GRAHA</span>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-item"><a href="siswa.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
            <li class="menu-item active"><a href="katalog_siswa.php"><i class="fas fa-book-open"></i> Koleksi Buku</a></li>
            <li class="menu-item"><a href="siswa_ebook.php"><i class="fas fa-file-pdf"></i> Koleksi E-Book</a></li>
            <li class="menu-item"><a href="daftar_peraturan.php"><i class="fas fa-gavel"></i> Peraturan</a></li>
            <li class="menu-item"><a href="profil.php"><i class="fas fa-user-circle"></i> Profil</a></li>
        </ul>
        <ul class="sidebar-menu-bottom">
            <li class="menu-item logout">
                <a href="logout.php" onclick="return confirm('Yakin ingin keluar?')"><i class="fas fa-power-off"></i> Keluar</a>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <div class="top-bar">
            <span style="font-size: 13px; color: #888;">Panel Siswa / <b>Katalog Buku</b></span>
            <div style="font-weight: 600;"><i class="fas fa-user-circle mr-1"></i> <?= htmlspecialchars($username_session) ?></div>
        </div>

        <div class="container-fluid-custom">
            <div class="header-katalog">
                <h2 style="font-weight: 800; color: var(--navy-main);">Koleksi Buku 📚</h2>
                <form action="" method="GET" class="search-container">
                    <input type="text" name="cari" placeholder="Cari judul, penulis, atau rak..." value="<?= htmlspecialchars($search) ?>">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>

            <div class="buku-grid">
                <?php if ($total_buku > 0): ?>
                    <?php while ($buku = mysqli_fetch_assoc($query_buku)): ?>
                    <div class="buku-card">
                        <div class="sampul">
                            <img src="img/<?= !empty($buku['sampul']) ? $buku['sampul'] : 'no-cover.jpg' ?>" onerror="this.src='https://via.placeholder.com/200x280?text=No+Cover'">
                            <span class="badge-kategori"><?= htmlspecialchars($buku['nama_kategori'] ?? 'Umum') ?></span>
                            <span class="badge-stok <?= $buku['stok'] > 0 ? 'stok-ada' : 'stok-habis' ?>">
                                <?= $buku['stok'] > 0 ? 'Tersedia' : 'Habis' ?>
                            </span>
                        </div>
                        <div class="info">
                            <h4><?= htmlspecialchars($buku['judul_buku']) ?></h4>
                            <p style="font-size: 12px; color: #777;"><i class="fas fa-user-edit mr-1"></i> <?= htmlspecialchars($buku['penulis']) ?></p>
                            <p><span class="rak-label"><i class="fas fa-map-marker-alt mr-1"></i> <?= htmlspecialchars($buku['nama_rak'] ?? '-') ?></span></p>
                        </div>
                        <a href="detail_buku.php?id=<?= $buku['id_buku'] ?>" class="btn-detail">Lihat Detail & Pinjam</a>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="alert alert-light w-100 text-center shadow-sm">Buku tidak ditemukan di database.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- WIGA CHATBOX -->
    <div id="wiga-container" style="position: fixed; bottom: 20px; right: 20px; z-index: 9999;">
        <div id="wiga-chatbox">
            <div style="background: linear-gradient(135deg, #FF9800, #E65100); padding: 15px; color: white; display: flex; justify-content: space-between; align-items: center;">
                <strong>Wiga Assistant</strong>
                <button onclick="toggleWiga()" style="background:none; border:none; color:white; cursor:pointer;"><i class="fas fa-times"></i></button>
            </div>
            <div id="ai-chat-isi">
                <div class="msg-wiga">Halo! Wiga di sini. Tanya judul buku atau topik tertentu ke Wiga ya!</div>
            </div>
            <div style="padding: 10px; background: #1a1a1a; display: flex; gap: 5px;">
                <input type="text" id="ai-input-pesan" placeholder="Tanya Wiga..." style="flex:1; background:#222; border:none; color:white; padding:8px 15px; border-radius:20px; outline:none;">
                <button onclick="kirimChatAI()" style="background:#E65100; border:none; color:white; width:35px; height:35px; border-radius:50%; cursor:pointer;"><i class="fas fa-paper-plane"></i></button>
            </div>
        </div>
        <button onclick="toggleWiga()" style="width: 60px; height: 60px; background: #E65100; border: none; border-radius: 50%; color: white; font-size: 24px; cursor: pointer; box-shadow: 0 5px 15px rgba(230,81,0,0.3);"><i class="fas fa-robot"></i></button>
    </div>

    <script>
        function toggleWiga() {
            const cb = document.getElementById('wiga-chatbox');
            cb.style.display = (cb.style.display === 'none' || cb.style.display === '') ? 'flex' : 'none';
        }

        // --- KONTEKS DATA (UNTUK FITUR SEARCH & AI) ---
        const contextData = `<?php 
            $q_ctx = mysqli_query($koneksi, "SELECT b.judul_buku, b.penulis, k.nama_kategori, r.nama_rak, b.stok 
                                             FROM buku b 
                                             LEFT JOIN kategori k ON b.id_kategori = k.id_kategori 
                                             LEFT JOIN rak r ON b.id_rak = r.id_rak LIMIT 50");
            while($r = mysqli_fetch_assoc($q_ctx)){ 
                echo "- ".addslashes($r['judul_buku'])." | Penulis: ".addslashes($r['penulis'])." | Kategori: ".addslashes($r['nama_kategori'])." | Rak: ".addslashes($r['nama_rak'])." | Stok: ".$r['stok']." "; 
            }
        ?>`;

        async function kirimChatAI() {
            const input = document.getElementById('ai-input-pesan');
            const box = document.getElementById('ai-chat-isi');
            const msg = input.value.trim();
            if(!msg) return;

            // Tampilkan pesan user
            box.innerHTML += `<div class="msg-user">${msg}</div>`;
            input.value = '';
            box.scrollTop = box.scrollHeight;

            // --- FITUR AUTO SEARCH ---
            const trigger = ['cari', 'buku', 'ada', 'tentang', 'mengenai', 'lihat', 'temukan'];
            const isSearching = trigger.some(word => msg.toLowerCase().includes(word));

            if (isSearching) {
                // Ekstraksi kata kunci (membersihkan kata pemicu)
                let query = msg.toLowerCase()
                    .replace(/\bcari\b/g, '').replace(/\bbuku\b/g, '')
                    .replace(/\btentang\b/g, '').replace(/\bmengenai\b/g, '')
                    .replace(/\bada\b/g, '').replace(/\bapakah\b/g, '').trim();

                if (query === "") query = msg;

                // Cek apakah ada di memori lokal (contextData)
                const list = contextData.split("- ").filter(t => t.trim() !== "");
                const match = list.filter(b => b.toLowerCase().includes(query));

                if (match.length > 0) {
                    const d = match[0].split(" | "); // Pecah data buku pertama yang ditemukan
                    box.innerHTML += `<div class="msg-wiga">
                        Siap! Wiga temukan buku yang cocok: <br><br>
                        <b>📖 Judul:</b> ${d[0]} <br>
                        <b>📍 Lokasi:</b> ${d[3].replace('Rak: ', '')} <br>
                        <b>📦 Stok:</b> ${d[4].replace('Stok: ', '')} <br><br>
                        Membuka hasil pencarian lengkap di katalog...
                    </div>`;
                } else {
                    box.innerHTML += `<div class="msg-wiga">Tunggu sebentar, Wiga akan mencari "${query}" di database perpustakaan...</div>`;
                }

                // Redirect otomatis (Auto Search)
                setTimeout(() => {
                    window.location.href = `katalog_siswa.php?cari=${encodeURIComponent(query)}`;
                }, 3000); 
                return;
            }

            // --- CHAT BIASA (JIKA BUKAN PERINTAH CARI) ---
            try {
                const res = await fetch('http://localhost:3000/chat', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ message: msg, context: contextData })
                });
                const data = await res.json();
                box.innerHTML += `<div class="msg-wiga">${data.choices[0].message.content}</div>`;
            } catch (e) {
                box.innerHTML += `<div class="msg-wiga" style="color:#ff6b6b">Maaf, Wiga sedang tidak bisa terhubung ke server AI.</div>`;
            }
            box.scrollTop = box.scrollHeight;
        }

        // Jalankan chat saat tekan Enter
        document.getElementById('ai-input-pesan').addEventListener('keypress', (e) => { 
            if(e.key === 'Enter') kirimChatAI(); 
        });
    </script>
</body>
</html>