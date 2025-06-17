<?php
if (isset($_GET['check'])) {
    $checkPath = __DIR__ . '/' . basename($_GET['check']);
    echo is_dir($checkPath) ? "EXISTS" : "NOT_FOUND";
    exit;
}
?>
