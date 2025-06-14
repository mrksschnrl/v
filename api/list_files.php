<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Benutzername aus GET oder SESSION holen
$username = $_GET['username'] ?? ($_SESSION['username'] ?? 'anonymous');
$username = basename($username); // gegen Pfadmanipulation absichern

// Zielordner: /users/accounts/USERNAME/uploads
$baseDir = realpath(__DIR__ . "/../users/accounts/$username/uploads");

// Prüfen ob Verzeichnis existiert
if (!$baseDir || !is_dir($baseDir)) {
    echo json_encode(['error' => 'Pfad nicht gefunden']);
    exit;
}

// Erlaubte Dateitypen
$allowedExtensions = [
    'mp4',
    'mkv',
    'mov',
    'avi',
    'mp3',
    'wav',
    'aac',
    'flac',
    'jpg',
    'jpeg',
    'png',
    'webp',
    'heic'
];

$files = [];

foreach (scandir($baseDir) as $file) {
    if ($file === '.' || $file === '..') continue;

    $full = $baseDir . DIRECTORY_SEPARATOR . $file;
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

    // Nur Dateien ohne bestehende -meta.txt zulassen
    if (
        is_file($full) &&
        !str_ends_with($file, '-meta.txt') &&
        in_array($ext, $allowedExtensions)
    ) {
        $files[] = $file;
    }
}

// JSON-Rückgabe
echo json_encode(['files' => $files]);
