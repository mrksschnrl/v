<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /v/login/login.php");
    exit;
}

require_once __DIR__ . '/db.php';

$stmt = $pdo->prepare("SELECT role, is_verified FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    header("Location: /v/login/login.php");
    exit;
}

if ($user['role'] !== 'user') {
    echo "🚫 Zugriff verweigert: Kein Benutzerkonto.";
    exit;
}

if ((int)$user['is_verified'] !== 1) {
    echo "🔒 Dein Konto ist nicht verifiziert.";
    exit;
}
