<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
header('Content-Type: application/json');

$username = $_GET['username'] ?? ($_SESSION['username'] ?? 'anonymous');
$username = basename($username);

$presetPath = __DIR__ . "/../users/accounts/$username/presets/default.json";

if (!file_exists($presetPath)) {
    echo json_encode(["status" => "error", "error" => "Keine Preset-Datei gefunden."]);
    exit;
}

$content = file_get_contents($presetPath);
if ($content === false) {
    echo json_encode(["status" => "error", "error" => "Konnte Preset nicht lesen."]);
    exit;
}

$preset = json_decode($content, true);
if ($preset === null) {
    echo json_encode(["status" => "error", "error" => "Preset JSON ungültig."]);
    exit;
}

echo json_encode(["status" => "OK", "preset" => $preset]);
