<?php
session_start();
require_once __DIR__ . '/../modules/db.php';
require_once __DIR__ . '/../modules/config.php';

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if (empty($username) || empty($password)) {
    die("❌ Benutzername und Passwort erforderlich.");
}

$stmt = $pdo->prepare("SELECT id, username, password, role, is_verified FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user) {
    die("❌ Benutzer nicht gefunden.");
}

if (!password_verify($password, $user['password'])) {
    die("❌ Passwort ist falsch.");
}

if ((int)$user['is_verified'] !== 1) {
    die("❌ Account ist noch nicht verifiziert.");
}

$_SESSION['user_id'] = $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['role'] = $user['role'];
$_SESSION['last_activity'] = time();

// ✅ Korrekte Weiterleitungen:
if ($user['role'] === 'admin') {
    header("Location: /v/admin/admin_dashboard.php");
    exit;
} elseif ($user['role'] === 'user') {
    header("Location: /v/users/user_dashboard.php");
    exit;
} else {
    // Fallback: Rolle nicht erkannt
    header("Location: /v/index.php");
    exit;
}
