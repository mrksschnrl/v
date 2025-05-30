<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
header('Content-Type: application/json');

// Nur POST erlaubt
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Nur POST erlaubt']);
    exit;
}

// Prüfe Benutzername
if (!isset($_POST['username']) || !preg_match('/^[a-zA-Z0-9_-]+$/', $_POST['username'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Ungültiger Benutzername']);
    exit;
}

$username = $_POST['username'];
$baseDir = __DIR__ . "/../users/accounts/$username/uploads";

// Stelle sicher, dass der Upload-Ordner existiert
if (!is_dir($baseDir)) {
    if (!mkdir($baseDir, 0775, true)) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Zielverzeichnis konnte nicht erstellt werden']);
        exit;
    }
}

// Datei prüfen
if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Fehler beim Hochladen']);
    exit;
}

$filename = basename($_FILES['file']['name']);
$targetPath = "$baseDir/$filename";

// Datei speichern
if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
    echo json_encode(['status' => 'success', 'path' => $targetPath]);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Fehler beim Speichern']);
}
