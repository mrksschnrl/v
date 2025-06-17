<?php
header('Content-Type: application/json');
$data = json_decode(file_get_contents("php://input"), true);
$path = $data['path'] ?? '';
$type = $data['type'] ?? 'source';

if (!$path || !is_dir($path)) {
    echo json_encode([
        "success" => false,
        "message" => "Pfad ungültig oder nicht vorhanden: $path"
    ]);
    exit;
}

$files = array_values(array_filter(scandir($path), fn($f) =>
    !in_array($f, ['.', '..']) && is_file("$path/$f")
));

echo json_encode([
    "success" => true,
    "files" => $files
]);
