<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
$username = $_SESSION['username'] ?? 'mrks'; // Fallback auf "mrks"
$baseDir = realpath(__DIR__ . "/../users/accounts/$username/_output");

if (!$baseDir || !is_dir($baseDir)) {
    echo json_encode(['error' => 'Pfad nicht gefunden']);
    exit;
}

$allowedExtensions = ['mp4', 'mkv', 'mov', 'avi', 'mp3', 'wav', 'aac', 'flac', 'jpg', 'jpeg', 'png', 'webp', 'heic'];
$files = [];

foreach (scandir($baseDir) as $file) {
    if ($file === '.' || $file === '..') continue;
    $full = $baseDir . DIRECTORY_SEPARATOR . $file;
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

    if (
        is_file($full) &&
        !str_ends_with($file, '-meta.txt') &&
        in_array($ext, $allowedExtensions)
    ) {
        $files[] = $file;
    }
}

echo json_encode($files);
