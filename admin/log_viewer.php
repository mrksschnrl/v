<?php
require_once __DIR__ . '/../modules/config.php';
require_once __DIR__ . '/../modules/secure_admin.php';

$pageTitle = "Log Viewer";
include __DIR__ . '/../includes/header.php';

// Sicherheitsprüfung & Pfadaufbau
$logDir = realpath(__DIR__ . '/../logs/');
$file = isset($_GET['file']) ? basename($_GET['file']) : null;
$filePath = $file ? realpath($logDir . '/' . $file) : null;

// Datei existiert und liegt im Log-Verzeichnis?
$valid = $filePath && strpos($filePath, $logDir) === 0 && is_file($filePath);
?>

<div class="dashboard-container">
    <div class="sidebar">
        <h2>Admin-Menü</h2>
        <ul>
            <li><a href="admin_dashboard.php">Übersicht</a></li>
            <li><a href="admin_tools.php">Dateiverwaltung & Tools</a></li>
            <li><a href="manage_users.php">Benutzer verwalten</a></li>
            <li><a href="verify_users.php">Benutzer verifizieren</a></li>
            <li><a href="admin_logs.php" class="active">Logs</a></li>
            <li><a href="<?= LOGOUT_PAGE ?>">Logout</a></li>
        </ul>
    </div>

    <div class="content">
        <h1>Logdatei ansehen</h1>

        <?php if ($valid): ?>
            <h3>Datei: <code><?= htmlspecialchars($file) ?></code></h3>
            <pre style="background:#111; padding:1rem; color:#ccc; max-height:500px; overflow:auto; font-family:monospace;">
<?= htmlspecialchars(file_get_contents($filePath)) ?>
            </pre>
        <?php else: ?>
            <p>⚠️ Ungültige oder nicht angegebene Datei.</p>
        <?php endif; ?>

        <p><a href="admin_logs.php">🔙 Zurück zur Logliste</a></p>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
