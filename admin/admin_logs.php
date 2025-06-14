<?php
require_once __DIR__ . '/../modules/config.php';
require_once __DIR__ . '/../modules/secure_admin.php';

$pageTitle = "Logdateien anzeigen";
$loadThemeCSS = true;
include __DIR__ . '/../includes/header.php';

// Verfügbare Logdateien (z. B. aus einem festgelegten Log-Verzeichnis)
$logDir = __DIR__ . '/../logs/';
$logFiles = glob($logDir . '*.log');
?>

<div class="dashboard-container">
    <?php include __DIR__ . '/../includes/admin/sidebar.php'; ?>

    <div class="content">
        <h1>Verfügbare Logdateien</h1>

        <?php if (empty($logFiles)): ?>
            <p>❌ Keine Logdateien gefunden im Verzeichnis <code>/logs/</code>.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($logFiles as $logPath):
                    $filename = basename($logPath); ?>
                    <li><a href="log_viewer.php?file=<?= urlencode($filename) ?>"><?= htmlspecialchars($filename) ?></a></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>