<?php
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

$username = $_POST['username'] ?? 'anonymous';

if (!isset($_FILES['file'])) {
    echo json_encode(["status" => "error", "error" => "Keine Datei erhalten"]);
    exit;
}

$file = $_FILES['file'];
$uploadDir = __DIR__ . "/../users/accounts/$username/uploads";

// Ordner anlegen, falls nicht vorhanden
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$targetPath = $uploadDir . '/' . basename($file['name']);

// Falls Datei bereits vorhanden ist
if (file_exists($targetPath)) {
    echo json_encode(["status" => "exists"]);
    exit;
}

// Datei verschieben
if (move_uploaded_file($file['tmp_name'], $targetPath)) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "error" => "Fehler beim Speichern"]);
}
