<?php
function get_env_key($key) {
    $path = __DIR__ . '/.env';
    if (!file_exists($path)) return null;

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        if (trim($name) == $key) return trim($value);
    }
    return null;
}

// Ambil kunci API untuk digunakan nanti
$groq_key = get_env_key('GROQ_API_KEY');
?>