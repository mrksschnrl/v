<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
header('Content-Type: application/json');

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['username'], $data['presetname'], $data['content'])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Ungültige Daten"]);
    exit;
}

$username   = basename($data['username']);
$presetname = basename($data['presetname']);
$content    = $data['content'];

$userDir = __DIR__ . "/../users/accounts/$username";
$baseDir = "$userDir/presets";

if (!is_dir($userDir)) {
    mkdir($userDir, 0775, true);
}
if (!is_dir($baseDir)) {
    mkdir($baseDir, 0775, true);
}

$fullPath = "$baseDir/$presetname.json";

if (file_put_contents($fullPath, $content) !== false) {
    echo json_encode(["status" => "success"]);
} else {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Fehler beim Schreiben"]);
}
