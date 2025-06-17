<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

// Nur POST erlaubt
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Nur POST erlaubt']);
    exit;
}

// JSON-Daten lesen und prüfen
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!isset($data['username']) || !is_array($data['files'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Ungültige Eingabe']);
    exit;
}

// Zielpfad vorbereiten
$username = preg_replace('/[^a-zA-Z0-9_-]/', '', $data['username']);
$basePath = __DIR__ . "/../users/accounts/$username/uploads";

if (!is_dir($basePath)) {
    mkdir($basePath, 0775, true);
}

$results = [];

foreach ($data['files'] as $file) {
    if (!isset($file['name'], $file['size'], $file['type'])) continue;

    $metaName = $file['name'] . "-meta.txt";
    $metaPath = $basePath . "/" . basename($metaName);

    if (file_exists($metaPath)) {
        $results[] = ['file' => $file['name'], 'status' => 'exists'];
        continue;
    }

    $content = "# Metadaten für: {$file['name']}\n";
    $content .= "# Größe: {$file['size']} Bytes\n";
    $content .= "# Typ: {$file['type']}\n";

    if (file_put_contents($metaPath, $content) !== false) {
        $results[] = ['file' => $file['name'], 'status' => 'created'];
    } else {
        $results[] = ['file' => $file['name'], 'status' => 'error'];
    }
}

// Antwort mit Detail-Liste zurückgeben
echo json_encode([
    'success' => true,
    'results' => $results
]);
 