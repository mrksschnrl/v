<?php
require_once __DIR__ . '/../modules/config.php';
require_once __DIR__ . '/../modules/db.php';
require_once __DIR__ . '/../modules/secure_admin.php';

session_start();

$userIdToDelete = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Sicherheitsprüfungen
if ($userIdToDelete <= 0) {
    die("Ungültige Benutzer-ID.");
}

if ($_SESSION['user_id'] === $userIdToDelete) {
    die("Du kannst dich nicht selbst löschen.");
}

// Benutzer löschen
$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
if ($stmt->execute([$userIdToDelete])) {
    header("Location: manage_users.php?msg=deleted");
    exit;
} else {
    die("Fehler beim Löschen.");
}
