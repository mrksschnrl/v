<?php
// meta_generator.php – schreibt meta.txt in Benutzerordner

ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

date_default_timezone_set("Europe/Vienna");

$username = preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['user'] ?? $_SESSION['username'] ?? 'anonymous');
$filename = basename($_GET['file'] ?? '');

if (!$filename || !$username) {
    http_response_code(400);
    echo "Fehler: Dateiname oder Benutzer fehlt.";
    exit;
}

// Zielpfad: /v/users/accounts/<username>/meta/
$baseDir = realpath(__DIR__ . '/../../users/accounts');
$userMetaDir = "$baseDir/$username/meta";
$metaFile = "$userMetaDir/{$filename}-meta.txt";
$logFile = __DIR__ . '/log/meta-conflicts.txt';

// Verzeichnis anlegen, falls nicht vorhanden
if (!is_dir($userMetaDir)) {
    mkdir($userMetaDir, 0775, true);
}

// Wenn Metadatei bereits existiert → loggen, abbrechen
if (file_exists($metaFile)) {
    $logEntry = "[" . date("Y-m-d H:i:s") . "] ⚠ Meta existiert: $filename-meta.txt für $username – abgebrochen\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    echo "conflict";
    exit;
}

// Metadaten schreiben
$metaContent = "filename: $filename\nuser: $username\ncreated_at: " . date("Y-m-d H:i:s") . "\nstatus: created\n";
file_put_contents($metaFile, $metaContent);

echo "success";
