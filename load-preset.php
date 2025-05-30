<?php
session_start();

// 🔐 Prüfen ob Benutzer eingeloggt ist
if (!isset($_SESSION['username']) || !preg_match('/^[a-zA-Z0-9_-]{3,}$/', $_SESSION['username'])) {
    http_response_code(403);
    echo json_encode(["error" => "Zugriff verweigert oder ungültiger Benutzername"]);
    exit;
}

$username = $_SESSION['username'];
$baseDir  = __DIR__ . "/../users/accounts/";
$filePath = $baseDir . $username . "/presets/file-sorter.txt";

// 🔍 Datei prüfen und lesen
if (!file_exists($filePath)) {
    http_response_code(404);
    echo json_encode(["error" => "Kein Preset gefunden"]);
    exit;
}

$content = file_get_contents($filePath);
$lines   = explode(PHP_EOL, $content);

// 🔄 In assoziatives Array umwandeln
$preset = [];
foreach ($lines as $line) {
    if (strpos($line, '=') !== false) {
        list($key, $value) = explode('=', $line, 2);
        $preset[trim($key)] = trim($value);
    }
}

// ✅ Erfolgreiche Antwort
echo json_encode([
    "status" => "OK",
    "preset" => $preset
]);
