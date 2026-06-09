<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda - Perpustakaan Widya Graha</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <style>
        :root { 
            --navy: #1a3a5f; 
            --gold: #ffc107; 
            --dark-overlay: rgba(26, 58, 95, 0.85);
        }

        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: #ffffff;
            scroll-behavior: smooth;
        }

        /* Navbar Sticky & Glass */
        .navbar {
            padding: 20px 0;
            transition: all 0.4s;
            background: transparent;
        }
        .navbar.scrolled {
            padding: 10px 0;
            background: white !important;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .navbar-brand b { color: var(--navy); letter-spacing: 1px; }

        /* Hero Section Premium */
        .hero {
            height: 100vh;
            background: linear-gradient(var(--dark-overlay), var(--dark-overlay)), 
                        url('https://images.unsplash.com/photo-1507842217343-583bb7270b66?auto=format&fit=crop&w=1600&q=80');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            color: white;
            clip-path: polygon(0 0, 100% 0, 100% 92%, 0% 100%);
        }

        .hero h1 {
            font-family: 'Montserrat', sans-serif;
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            line-height: 1.1;
        }

        /* Tombol Login Konsisten */
        .btn-login-custom {
            background: var(--gold);
            color: var(--navy) !important;
            padding: 16px 45px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 18px;
            letter-spacing: 1px;
            transition: 0.4s;
            border: none;
            display: inline-block;
            box-shadow: 0 10px 25px rgba(255, 193, 7, 0.4);
        }
        .btn-login-custom:hover {
            transform: translateY(-5px);
            background: white;
            box-shadow: 0 15px 30px rgba(255, 193, 7, 0.6);
            text-decoration: none;
        }

        /* Info Cards */
        .card-fitur {
        border: none;
        border-radius: 25px;
        background: #fff;
        box-shadow: 0 15px 40px rgba(0,0,0,0.06);
        transition: 0.4s ease;
        margin-top: -80px; /* Menimpa hero section */
        display: flex;
        flex-direction: column;
        justify-content: center;
        text-align: center;
        min-height: 320px; /* Memberikan tinggi minimal agar seragam */
        height: 100%; /* Agar tinggi kotak sama dalam satu baris */
        padding: 30px 20px;
        overflow: hidden;
    }

    .card-content {
        width: 100%;
    }

    .card-fitur i {
        font-size: 50px;
        color: var(--gold);
        margin-bottom: 20px;
        display: block;
    }

    .card-fitur h4 {
        font-size: 22px;
        margin-bottom: 15px;
        color: var(--navy);
    }

    .card-fitur p {
        font-size: 14px;
        line-height: 1.6;
        margin-bottom: 0;
        /* Kunci agar teks tidak bocor */
        overflow-wrap: break-word;
        word-wrap: break-word;
    }

    .card-fitur:hover {
        transform: translateY(-15px);
        background: var(--navy);
        color: white !important;
    }

    .card-fitur:hover .text-muted {
        color: rgba(255,255,255,0.8) !important;
    }

    /* Pengaturan Responsive HP */
    @media (max-width: 768px) {
        .card-fitur {
            margin-top: 20px; /* Jarak antar kotak saat menumpuk di HP */
            min-height: auto; 
            padding: 40px 25px;
        }
        
        .hero {
            height: auto;
            padding: 120px 0 150px 0;
            clip-path: none; /* Hilangkan potongan miring di HP agar lebih rapi */
        }
    }
</style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-light fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="Logoperpus.png" alt="Logo" height="45">
                <b class="ml-3 d-none d-sm-inline">WIDYA GRAHA</b>
            </a>
        </div>
    </nav>

    <header class="hero">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 animate__animated animate__fadeInLeft">
                    <span class="badge badge-warning px-3 py-2 rounded-pill mb-3" style="font-weight: 700;">PERPUSTAKAAN</span>
                    <h1 class="font-weight-bold mb-4">Widya Graha</h1>
                    <p class="lead mb-5" style="font-size: 1.2rem; opacity: 0.9;">Solusi literasi cerdas untuk seluruh warga SMK N 1 Kebumen. Akses pengetahuan tanpa batas, kapan saja.</p>
                    <a href="login.php" class="btn-login-custom animate__animated animate__pulse animate__infinite">
                        LOGIN SEKARANG <i class="fas fa-chevron-right ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <section class="pb-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card-fitur text-center animate__animated animate__fadeInUp">
                        <i class="fas fa-book-reader"></i>
                        <h4 class="font-weight-bold">Akses Koleksi</h4>
                        <p class="text-muted">Cari dan baca buku fisik maupun e-book dengan sistem kategori yang rapi.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card-fitur text-center animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                        <i class="fas fa-user-shield"></i>
                        <h4 class="font-weight-bold">Panel Admin</h4>
                        <p class="text-muted">Manajemen buku, anggota, dan transaksi perpustakaan dengan sangat mudah.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                   <div class="card-fitur text-center animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">
                        <i class="fas fa-robot"></i>
                        <h4 class="font-weight-bold">Wiga AI Chat</h4>
                        <p class="text-muted small">Asisten cerdas kami siap memberikan rekomendasi buku terbaik setiap saat.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-light">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 mb-4">
                    <img src="https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?auto=format&fit=crop&w=800&q=80" class="img-fluid rounded-lg shadow-lg" alt="Library" style="border-radius: 30px;">
                </div>
                <div class="col-md-6 pl-md-5">
                    <h2 class="font-weight-bold color-navy mb-4">Misi Literasi Digital</h2>
                    <p class="text-secondary">Kami berkomitmen untuk menyediakan platform pendidikan yang terintegrasi. Baik untuk Admin yang mengelola data maupun Siswa yang mencari referensi, semua tersambung dalam satu ekosistem.</p>
                    <div class="mt-4">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-check-circle text-success mr-3 fa-lg"></i>
                            <span class="font-weight-bold">Navigasi User-Friendly</span>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-check-circle text-success mr-3 fa-lg"></i>
                            <span class="font-weight-bold">Responsif di Semua Perangkat</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-success mr-3 fa-lg"></i>
                            <span class="font-weight-bold">Keamanan Data Terjamin</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="py-5 bg-white border-top">
        <div class="container text-center">
            <img src="Logoperpus.png" alt="Logo" height="40" class="mb-3">
            <h5 class="font-weight-bold mb-3">WIDYA GRAHA</h5>
            <p class="text-muted small">&copy; 2026 Perpustakaan Digital SMK Widya Graha.</p>
        </div>
    </footer>

    <script>
        // Efek Navbar saat Scroll
        window.onscroll = function() {
            var nav = document.getElementById('mainNav');
            if (window.pageYOffset > 50) {
                nav.classList.add("scrolled");
            } else {
                nav.classList.remove("scrolled");
            }
        };
    </script>
</body>
</html>