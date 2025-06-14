<?php
// Fehleranzeigen PHP-weit aktivieren
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
header('Content-Type: application/json; charset=UTF-8');

// === 0) Sicherstellen, dass Benutzer angemeldet ist ===
if (!isset($_SESSION['username']) || $_SESSION['username'] === '') {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Nicht angemeldet']);
    exit;
}
$username = $_SESSION['username'];

// === 1) Log-Datei und User-Ordner festlegen ===
$logPath = __DIR__ . "/../../users/accounts/$username/meta-log.txt";
$userDir = dirname($logPath);

// === 2) Sicherstellen, dass der User-Ordner existiert ===
if (!is_dir($userDir)) {
    if (!mkdir($userDir, 0755, true)) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => "Konnte Benutzerverzeichnis nicht anlegen: $userDir"]);
        exit;
    }
}

// === 3) Log am Anfang leeren/neu anlegen ===
file_put_contents($logPath, "Meta-Generator gestartet! (" . date("Y-m-d H:i:s") . ")\n");

// === 4) Meta-Workflow starten: Input-Ordner suchen ===
$inputFolder = __DIR__ . "/../../users/accounts/$username/uploads";
file_put_contents($logPath, "Input-Ordner: $inputFolder\n", FILE_APPEND);

// === 5) Datumsfilter holen (aus POST) ===
$input = json_decode(file_get_contents('php://input'), true);
$from  = $input['from'] ?? null;
$to    = $input['to']   ?? null;
file_put_contents($logPath, "Filter: Von: $from | Bis: $to\n", FILE_APPEND);

// === 6) Ordnerinhalt verarbeiten ===
if (!is_dir($inputFolder)) {
    file_put_contents($logPath, "Fehler: Input-Ordner existiert nicht.\n", FILE_APPEND);
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => "Input-Ordner nicht gefunden: $inputFolder"
    ]);
    exit;
}

// === 7) Dateien durchgehen ===
$files = array_diff(scandir($inputFolder), ['.', '..']);
$anyFound = false;
foreach ($files as $file) {
    $filePath = "$inputFolder/$file";
    if (is_file($filePath)) {
        $anyFound = true;
        // Zeitfilter (letzte Änderung, falls angegeben)
        $lastMod = filemtime($filePath);
        $lastModStr = date("Y-m-d H:i:s", $lastMod);
        $inDate = true;
        if ($from && $to) {
            $inDate = ($lastMod >= strtotime($from)) && ($lastMod <= strtotime($to . ' 23:59:59'));
        }

        if (!$inDate) {
            file_put_contents($logPath, "$file übersprungen (außerhalb Zeitraum)\n", FILE_APPEND);
            continue;
        }

        // Beispiel: EXIF auslesen (falls Bild)
        $exif = @exif_read_data($filePath);
        if ($exif !== false && isset($exif['DateTime'])) {
            $date = $exif['DateTime'];
            $cam  = $exif['Model'] ?? 'unbekannte Kamera';
            file_put_contents($logPath, "$file | Aufnahmedatum: $date | Kamera: $cam\n", FILE_APPEND);
        } else {
            file_put_contents($logPath, "$file | lastModified: $lastModStr | Keine EXIF-Daten\n", FILE_APPEND);
        }
    }
}

// === 8) Abschlussmeldung ins Log ===
if (!$anyFound) {
    file_put_contents($logPath, "Keine Dateien im Input-Ordner gefunden!\n", FILE_APPEND);
} else {
    file_put_contents($logPath, "Meta-Generator abgeschlossen. (" . date("Y-m-d H:i:s") . ")\n", FILE_APPEND);
}

// === 9) Erfolgsmeldung als JSON ===
echo json_encode([
    'status'  => 'success',
    'message' => "Metadaten-Log erzeugt: $logPath"
]);
