<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

// Nicht eingeloggt? → Loginseite
if (!isset($_SESSION['user_id'])) {
    header("Location: " . LOGIN_PAGE);
    exit;
}

// User aus DB holen
$stmt = $pdo->prepare("SELECT role, is_verified FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Benutzer nicht gefunden? → Logout/Redirect
if (!$user) {
    header("Location: " . LOGIN_PAGE);
    exit;
}

// Kein Admin? → Zugriff verweigert
if ($user['role'] !== 'admin') {
    echo "🚫 Zugriff verweigert: Adminrechte erforderlich.";
    exit;
}

// Nicht verifiziert? → Hinweis
if ((int)$user['is_verified'] !== 1) {
    echo "🔒 Dein Konto ist nicht verifiziert.";
    exit;
}
