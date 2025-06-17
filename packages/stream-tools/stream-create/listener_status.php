<?php
$file = __DIR__ . "/folder_listener_running.txt";
if (file_exists($file)) {
    echo "🟢 Folder listener is running.";
} else {
    echo "⚪ Folder listener is not running.";
}
?>
