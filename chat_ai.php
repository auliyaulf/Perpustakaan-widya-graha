<?php
ob_start();
header('Content-Type: application/json');

include 'koneksi.php';

ob_clean(); 

$input = json_decode(file_get_contents('php://input'), true);
$pesan_user = $input['pesan'] ?? '';

if (!$pesan_user) {
    echo json_encode(["choices" => [["message" => ["content" => "Ada yang bisa Wiga bantu?"]]]]);
    exit;
}

// 1. KONTEKS BUKU FISIK (Menggunakan JOIN untuk id_rak & id_kategori)
$query_buku = "SELECT buku.judul_buku, buku.penulis, rak.nama_rak, kategori.nama_kategori 
               FROM buku 
               LEFT JOIN rak ON buku.id_rak = rak.id_rak 
               LEFT JOIN kategori ON buku.id_kategori = kategori.id_kategori 
               LIMIT 15";
$q_buku = mysqli_query($koneksi, $query_buku);

// 2. KONTEKS E-BOOK (Sesuaikan dengan nama tabel ebook Anda)
$query_ebook = "SELECT judul_ebook, penulis, format_file FROM ebook LIMIT 15";
$q_ebook = mysqli_query($koneksi, $query_ebook);

$konteks = "Identitas: Kamu adalah Wiga, asisten digital Perpustakaan Widya Graha SMK Negeri Kebumen.\n";
$konteks .= "Tugas: Bantu siswa menemukan buku fisik atau E-Book.\n\n";

$konteks .= "DAFTAR BUKU FISIK:\n";
while($r = mysqli_fetch_assoc($q_buku)) {
    $konteks .= "- " . $r['judul_buku'] . " | Penulis: " . $r['penulis'] . " | Kategori: " . $r['nama_kategori'] . " | Lokasi: " . $r['nama_rak'] . "\n";
}

$konteks .= "\nDAFTAR E-BOOK (Bisa dibaca online):\n";
if ($q_ebook) {
    while($e = mysqli_fetch_assoc($q_ebook)) {
        $konteks .= "- [E-BOOK] " . $e['judul_ebook'] . " karya " . $e['penulis'] . " (Format: " . $e['format_file'] . ")\n";
    }
}

$konteks .= "\nIntruksi: Jika siswa bertanya tentang 'Ebook' atau 'baca online', arahkan ke daftar E-Book. Jika bertanya buku fisik, berikan lokasi Rak-nya.";

// 3. KIRIM KE NODE.JS (Server Chatbot)
$data_ke_node = [
    "message" => $pesan_user,
    "context" => $konteks
];

$curl = curl_init("http://localhost:3000/chat");
curl_setopt_array($curl, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($data_ke_node),
    CURLOPT_HTTPHEADER => ["Content-Type: application/json"],
    CURLOPT_TIMEOUT => 30
]);

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    echo json_encode(["choices" => [["message" => ["content" => "Maaf, Wiga sedang gangguan teknis (CURL Error)."]]]]);
} else {
    echo $response;
}
?>