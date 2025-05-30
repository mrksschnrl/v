<?php
// meta_generator.php – Erstellt eine Metadatei, aber überschreibt nie

date_default_timezone_set("Europe/Vienna");

$outputDir = __DIR__ . '/output/';
$logDir    = __DIR__ . '/log/';
$filename  = $_GET['file'] ?? null;

if (!$filename) {
    http_response_code(400);
    echo "Fehler: Kein Dateiname angegeben.";
    exit;
}

$metaFile = $outputDir . $filename . '-meta.txt';
$logFile  = $logDir . 'meta-conflicts.txt';

// Wenn Metadatei bereits existiert, loggen – nicht überschreiben
if (file_exists($metaFile)) {
    $logEntry = "[" . date("Y-m-d H:i:s") . "] ⚠ Metadatei existiert bereits: $filename-meta.txt – Vorgang abgebrochen\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    echo "conflict";
    exit;
}

// Metadatei-Inhalt schreiben
$metaContent = "filename: $filename\ncreated_at: " . date("Y-m-d H:i:s") . "\nstatus: created\n";
file_put_contents($metaFile, $metaContent);
echo "success";
