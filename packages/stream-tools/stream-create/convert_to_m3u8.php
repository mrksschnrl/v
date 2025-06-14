<?php
// convert_to_m3u8.php

// Fehlerberichterstattung aktivieren
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log-Datei festlegen
$log_file = __DIR__ . '/logs/strict_ui_converter.log';

// Prüfen ob Input-Parameter übergeben wurde
if (!isset($_GET['file']) || empty($_GET['file'])) {
    file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] Fehler: Keine Datei angegeben.\n", FILE_APPEND);
    die('Fehler: Keine Datei angegeben.');
}

// Dateiname und Pfad vorbereiten
$input_file = basename($_GET['file']);
$input_path = __DIR__ . '/input_mp4/' . $input_file;

// Prüfen ob Eingabedatei existiert
if (!file_exists($input_path)) {
    file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] Fehler: Datei nicht gefunden: {$input_file}\n", FILE_APPEND);
    die('Fehler: Eingabedatei nicht gefunden.');
}

// Ausgabeverzeichnis vorbereiten
$output_dir = __DIR__ . '/output_m3u8/' . pathinfo($input_file, PATHINFO_FILENAME);
if (!is_dir($output_dir)) {
    mkdir($output_dir, 0777, true);
}

// Ausgabe-Dateien
$output_m3u8 = $output_dir . '/playlist.m3u8';

// FFmpeg-Befehl aufbauen
$cmd = sprintf(
    'nice -n 15 ffmpeg -i %s -c:v libx264 -preset veryfast -crf 23 -c:a aac -b:a 128k -ac 2 -f hls -hls_time 5 -hls_list_size 0 -hls_segment_filename "%s/segment%%03d.ts" %s',
    escapeshellarg($input_path),
    escapeshellarg($output_dir),
    escapeshellarg($output_m3u8)
);

// FFmpeg ausführen und Ausgabe abfangen
exec($cmd . ' 2>&1', $output, $return_var);

// Ergebnis prüfen
if ($return_var !== 0) {
    file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] FFmpeg Fehler bei {$input_file}:\n" . implode("\n", $output) . "\n\n", FILE_APPEND);
    die('Fehler: Konvertierung fehlgeschlagen. Details im Log.');
} else {
    file_put_contents($log_file, "[" . date('Y-m-d H:i:s') . "] Erfolg: {$input_file} erfolgreich konvertiert.\n", FILE_APPEND);
    echo 'Erfolg: Datei erfolgreich konvertiert.';
}
