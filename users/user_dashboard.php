<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../modules/auth_user.php';
require_once __DIR__ . '/../modules/config.php';

$pageTitle = "Benutzer-Dashboard";

include __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-container">
    <div class="sidebar">
        <h2>Menü</h2>
        <ul>
            <li><a href="#">📊 Übersicht</a></li>
            <li><a href="#">👤 Profil</a></li>
            <li><a href="<?= BASE_URL ?>/users/user_settings.php">⚙️ Einstellungen</a></li>
            <li><a href="/v/packages/file-sort/index.php" target="_blank">🧮 File Sorter PWA</a></li>

            <li><a href="#">❓ Hilfe</a></li>
            <li><a href="<?= LOGOUT_PAGE ?>">🚪 Logout</a></li>
        </ul>
    </div>

    <div class="content">
        <h1>Willkommen, <?= htmlspecialchars($_SESSION['username']) ?>!</h1>
        <p>Du bist eingeloggt als <strong><?= htmlspecialchars($_SESSION['role']) ?></strong>.</p>
        <p>Dies ist dein persönliches Dashboard.</p>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>