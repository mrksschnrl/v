<?php
require_once __DIR__ . '/../modules/config.php';
require_once __DIR__ . '/../modules/db.php';
require_once __DIR__ . '/../modules/secure_admin.php';

$pageTitle = "Admin – Tools";
$activePage = 'admin_tools.php'; // Wichtig für aktive Sidebar-Kennzeichnung
$loadThemeCSS = true;

include __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-container">
    <?php include __DIR__ . '/../includes/admin/sidebar.php'; ?>

    <div class="content">
        <h1>Dateiverwaltung & Tools</h1>

        <p>Hier könnten Dateiuploads, Wartungsskripte oder andere Admin-Werkzeuge eingebunden werden.</p>

        <ul>
            <li><a href="log_viewer.php">📜 Logdateien anzeigen</a></li>
            <!-- <li><a href="backup_tool.php">🗄️ Backup erstellen</a></li> -->
        </ul>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>