<?php
session_start();
require_once __DIR__ . '/config.php';

// Session-Timeout (15 Min)
$timeout = 900;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
    session_unset();
    session_destroy();
    header("Location: " . LOGIN_PAGE . "?timeout=1");
    exit;
}
$_SESSION['last_activity'] = time();

// Theme aus GET übernehmen (und speichern)
if (isset($_GET['theme'])) {
    $_SESSION['theme'] = $_GET['theme'];
}
