<?php
// get_meta_log.php – Zeigt den Inhalt des Metadatei-Konfliktlogs

$logFile = __DIR__ . '/log/meta-conflicts.txt';

if (!file_exists($logFile)) {
    echo "[Noch keine Konflikte registriert]";
    exit;
}

$logContent = file_get_contents($logFile);
echo nl2br(htmlspecialchars($logContent));
