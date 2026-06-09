<?php
include 'config.php'; // File yang tadi kita buat untuk baca .env

header('Content-Type: application/json');

// Mengambil input dari JavaScript
$input = json_decode(file_get_contents('php://input'), true);
$judul = $input['judul'] ?? '';

if (empty($judul)) {
    echo json_encode(['error' => 'Judul tidak boleh kosong']);
    exit;
}

$url = "https://api.groq.com/openai/v1/chat/completions";

$data = [
    "model" => "llama3-8b-8192",
    "messages" => [
        ["role" => "system", "content" => "Berikan sinopsis buku sangat singkat (maksimal 2 kalimat) dalam Bahasa Indonesia."],
        ["role" => "user", "content" => "Judul buku: " . $judul]
    ]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $groq_key",
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
curl_close($ch);

echo $response;