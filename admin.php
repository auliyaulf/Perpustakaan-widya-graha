<?php
session_start();
include 'koneksi.php';

// 1. Proteksi Halaman
if(!isset($_SESSION['username'])){
    header("location:login.php");
    exit;
}

// 2. Ambil Nama Asli dari Tabel 'users'
$username_session = $_SESSION['username'];
$query_user = mysqli_query($koneksi, "SELECT nama_pustakawan FROM pustakawan WHERE username = '$username_session'");
$data_user  = mysqli_fetch_assoc($query_user);
$nama_tampil = ($data_user) ? $data_user['nama_pustakawan'] : $username_session;

// 3. Mengambil data statistik untuk Dashboard & Konteks Wiga
$query_buku = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM buku");
$count_buku = mysqli_fetch_assoc($query_buku)['total'];

$query_ebook = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM ebook");
$count_ebook = mysqli_fetch_assoc($query_ebook)['total'];

$query_anggota = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM anggota");
$count_anggota = mysqli_fetch_assoc($query_anggota)['total'];

$query_pinjam = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM transaksi WHERE status='pinjam'");
$count_pinjam = mysqli_fetch_assoc($query_pinjam)['total'];

$query_denda = mysqli_query($koneksi, "SELECT SUM(denda) as total FROM transaksi");
$data_denda = mysqli_fetch_assoc($query_denda);
$total_denda = $data_denda['total'] ?? 0;

$query_booking = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM transaksi WHERE status='booking'");
$count_booking = mysqli_fetch_assoc($query_booking)['total'];

// --- KONTEKS WIGA ADMIN (BACKEND) ---
$wiga_context = "Anda adalah Wiga, asisten cerdas Admin Perpustakaan Widya Graha. ";
$wiga_context .= "Data saat ini: Koleksi buku fisik $count_buku, E-book $count_ebook, total anggota $count_anggota siswa. ";
$wiga_context .= "Status transaksi: $count_pinjam buku sedang dipinjam, $count_booking permintaan booking menunggu, dan total kas denda Rp " . number_format($total_denda, 0, ',', '.') . ". ";
$wiga_context .= "Jawab secara profesional, ringkas, dan bantu admin mengambil keputusan berdasarkan data tersebut.";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Widya Graha</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root { --navy: #1a3a5f; --shadow: 0 4px 15px rgba(0,0,0,0.05); }
        body { display: flex; min-height: 100vh; background-color: #f4f7fa; margin: 0; font-family: 'Segoe UI', sans-serif; }
        
        /* Sidebar & Layout */
        .sidebar { display: flex; flex-direction: column; height: 100vh; position: fixed; width: 260px; z-index: 1000; background: var(--navy); }
        .main-content { margin-left: 260px; width: calc(100% - 260px); padding: 0; flex: 1; display: flex; flex-direction: column; }
        .card-header-admin { border: none; background: white; margin-bottom: 20px; padding: 15px 25px; border-bottom: 1px solid #eee; box-shadow: var(--shadow); }
        
        /* Stat Cards */
        .stat-card { border: none; border-radius: 12px; box-shadow: var(--shadow); transition: 0.3s; text-decoration: none !important; color: inherit; }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-icon { width: 50px; height: 50px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 15px; }
        
        .bg-light-blue { background: #e7f1ff; color: #007bff; }
        .bg-light-green { background: #e6fcf5; color: #20c997; }
        .bg-light-orange { background: #fff4e6; color: #fd7e14; }
        .bg-light-purple { background: #f3f0ff; color: #7950f2; }
        .bg-light-red { background: #fff5f5; color: #fa5252; }

        .alert-notif { background: #fff9db; border-left: 5px solid #fcc419; color: #927000; padding: 15px 25px; border-radius: 10px; box-shadow: var(--shadow); }

        /* WIGA CHAT STYLE (SYNC WITH SISWA) */
        #wiga-container { position: fixed; bottom: 25px; right: 25px; z-index: 9999; }
        #wiga-chatbox { 
            display: none; width: 330px; height: 450px; background: #1a1a1a; 
            border-radius: 15px; box-shadow: 0 10px 25px rgba(0,0,0,0.5); 
            flex-direction: column; overflow: hidden; margin-bottom: 15px; border: 1px solid #333;
        }
        .wiga-header { 
            background: linear-gradient(135deg, #FF9800, #E65100); 
            padding: 15px; color: white; display: flex; justify-content: space-between; align-items: center; 
        }
        .chat-isi { flex: 1; padding: 15px; overflow-y: auto; background: #121212; display: flex; flex-direction: column; gap: 10px; }
        .msg-ai { background: #333; color: #ddd; padding: 10px; border-radius: 0 10px 10px 10px; font-size: 13px; max-width: 80%; align-self: flex-start; }
        .msg-user { background: #E65100; color: white; padding: 10px; border-radius: 10px 0 10px 10px; font-size: 13px; max-width: 80%; align-self: flex-end; }
        .wiga-btn-trigger {
            width: 60px; height: 60px; background: #E65100; border: none; border-radius: 50%; 
            color: white; font-size: 24px; cursor: pointer; box-shadow: 0 5px 15px rgba(230, 81, 0, 0.4); transition: 0.3s;
        }
        .wiga-btn-trigger:hover { transform: scale(1.1); }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <img src="Logoperpus.png" alt="Logo" class="logo-custom">
        <h2>PERPUSTAKAAN</h2>
        <span>Widya Graha</span>
    </div>
    <ul class="sidebar-menu">
        <li class="menu-item active"><a href="admin.php"><i class="fas fa-th-large"></i> Dashboard</a></li>
        <li class="menu-item"><a href="buku.php"><i class="fas fa-book-open"></i> Koleksi Buku</a></li>
        <li class="menu-item"><a href="data_ebook.php"><i class="fas fa-file-pdf"></i> Data E-Book</a></li>
        <li class="menu-item"><a href="anggota.php"><i class="fas fa-user-friends"></i> Data Anggota</a></li>
        <li class="menu-item"><a href="transaksi.php"><i class="fas fa-exchange-alt"></i> Transaksi</a></li>
        <li class="menu-item"><a href="peraturan.php"><i class="fas fa-gavel"></i> Kelola Peraturan</a></li>
        <li class="menu-item"><a href="profil_admin.php"><i class="fas fa-user-shield"></i> Profil Admin</a></li>

        <li class="menu-item logout"><a href="logout.php" onclick="return confirm('Keluar?')"><i class="fas fa-power-off"></i> Keluar</a></li>
    </ul>
</div>

<div class="main-content">
    <div class="card-header-admin d-flex justify-content-between align-items-center">
        <div style="font-size: 16px; color: #888;">Panel Admin / <b style="color: #555;">Dashboard Utama</b></div>
        <span class="badge badge-light p-2"><i class="fas fa-calendar-alt"></i> &nbsp; <?= date('d F Y') ?></span>
    </div>

    <div class="container-fluid px-4">
        <?php if($count_booking > 0): ?>
            <div class="alert-notif d-flex justify-content-between align-items-center mb-4">
                <div><i class="fas fa-bell mr-2"></i> Ada <b><?= $count_booking; ?></b> permintaan booking yang menunggu konfirmasi.</div>
                <a href="transaksi.php" class="btn btn-warning btn-sm font-weight-bold px-3">PROSES</a>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; border-left: 5px solid var(--navy) !important;">
            <div class="card-body p-4">
                <h2 class="font-weight-bold" style="color: var(--navy);">Halo, <?= htmlspecialchars($nama_tampil); ?> 👋</h2>
                <p class="text-muted mb-0">Kelola data dan pantau statistik perpustakaan dengan bantuan Wiga AI.</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-4">
                <a href="buku.php" class="card stat-card p-3">
                    <div class="stat-icon bg-light-blue"><i class="fas fa-book"></i></div>
                    <h3 class="font-weight-bold mb-0"><?= $count_buku; ?></h3>
                    <p class="text-muted small mb-0">Total Buku Fisik</p>
                </a>
            </div>
            <div class="col-md-4 mb-4">
                <a href="data_ebook.php" class="card stat-card p-3">
                    <div class="stat-icon bg-light-red"><i class="fas fa-file-pdf"></i></div>
                    <h3 class="font-weight-bold mb-0"><?= $count_ebook; ?></h3>
                    <p class="text-muted small mb-0">Koleksi E-Book Digital</p>
                </a>
            </div>
            <div class="col-md-4 mb-4">
                <a href="anggota.php" class="card stat-card p-3">
                    <div class="stat-icon bg-light-green"><i class="fas fa-users"></i></div>
                    <h3 class="font-weight-bold mb-0"><?= $count_anggota; ?></h3>
                    <p class="text-muted small mb-0">Total Anggota Siswa</p>
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4">
                <a href="transaksi.php" class="card stat-card p-3">
                    <div class="stat-icon bg-light-orange"><i class="fas fa-exchange-alt"></i></div>
                    <h3 class="font-weight-bold mb-0"><?= $count_pinjam; ?></h3>
                    <p class="text-muted small mb-0">Transaksi Pinjam Aktif</p>
                </a>
            </div>
            <div class="col-md-6 mb-4">
                <div class="card stat-card p-3">
                    <div class="stat-icon bg-light-purple"><i class="fas fa-wallet"></i></div>
                    <h3 class="font-weight-bold mb-0">Rp <?= number_format($total_denda, 0, ',', '.'); ?></h3>
                    <p class="text-muted small mb-0">Total Kas Masuk (Denda)</p>
                </div>
            </div>
        </div>
    </div>

    <footer class="mt-auto py-3 text-center text-muted" style="font-size: 12px;">
        &copy; <?= date('Y') ?> <b>Widya Graha</b> SMK Negeri Kebumen.
    </footer>
</div>

<div id="wiga-container">
    <div id="wiga-chatbox">
        <div class="wiga-header">
            <div><strong>Wiga Assistant</strong><br><small>Analisis Data Admin</small></div>
            <button onclick="toggleWiga()" style="background:none; border:none; color:white; cursor:pointer;"><i class="fas fa-times"></i></button>
        </div>
        <div id="ai-chat-isi" class="chat-isi">
            <div class="msg-ai">Halo Admin <strong><?= htmlspecialchars($nama_tampil); ?></strong>! Ada data atau laporan yang ingin Anda tanyakan?</div>
        </div>
        <div style="padding: 10px; background: #1a1a1a; border-top: 1px solid #333; display: flex; gap: 5px;">
            <input type="text" id="ai-input-pesan" placeholder="Tanya denda, stok buku..." style="flex: 1; background: #222; border: 1px solid #444; color: white; padding: 8px 12px; border-radius: 20px; outline: none; font-size: 13px;">
            <button onclick="kirimChatAI()" style="background: #E65100; border: none; color: white; width: 35px; height: 35px; border-radius: 50%; cursor: pointer;"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>
    <button onclick="toggleWiga()" class="wiga-btn-trigger"><i class="fas fa-robot"></i></button>
</div>

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

    chatIsi.innerHTML += `<div class="msg-user">${pesan}</div>`;
    inputPesan.value = ""; 
    chatIsi.scrollTop = chatIsi.scrollHeight;

    try {
        const response = await fetch('http://localhost:3000/chat', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: pesan, context: "<?= addslashes($wiga_context); ?>" })
        });
        const data = await response.json();
        const balasan = data.choices[0].message.content;
        chatIsi.innerHTML += `<div class="msg-ai">${balasan}</div>`;
        chatIsi.scrollTop = chatIsi.scrollHeight;
    } catch (e) {
        chatIsi.innerHTML += `<div style="color: #ff4444; font-size: 11px; text-align: center;">Wiga sedang offline.</div>`;
    }
}
document.getElementById('ai-input-pesan').addEventListener('keypress', (e) => { if (e.key === 'Enter') kirimChatAI(); });
</script>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>