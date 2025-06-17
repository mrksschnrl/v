<?php
// upload.php (Debug-Version)

ini_set('display_errors', 1);
error_reporting(E_ALL);

$uploadDir = __DIR__ . '/input_mp4/';

if (!empty($_FILES['files']['name'][0])) {
    foreach ($_FILES['files']['name'] as $key => $name) {
        $tmpName = $_FILES['files']['tmp_name'][$key];
        $error = $_FILES['files']['error'][$key];
        $size = $_FILES['files']['size'][$key];

        if ($error !== UPLOAD_ERR_OK) {
            echo "Fehler beim Upload von {$name}. Fehlercode: {$error}\n";
            continue;
        }

        $targetFile = $uploadDir . basename($name);

        if (move_uploaded_file($tmpName, $targetFile)) {
            echo "Datei {$name} erfolgreich hochgeladen (Größe: {$size} Bytes).\n";
        } else {
            echo "Fehler beim Verschieben von {$name}.\n";
        }
    }
} else {
    echo "Keine Datei erhalten.";
}
