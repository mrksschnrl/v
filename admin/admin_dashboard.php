<?php
require_once __DIR__ . '/../modules/config.php';
require_once __DIR__ . '/../modules/db.php';
require_once __DIR__ . '/../modules/secure_admin.php'; // enthält session_start & Zugriffskontrolle

$pageTitle = "Admin-Dashboard";
$loadThemeCSS = true; // ⬅️ Wichtig: Theme laden
include __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-container">
    <?php include __DIR__ . '/../includes/admin/sidebar.php'; ?>

    <!-- Hauptinhalt -->
    <div class="content">
        <h1>Willkommen im Adminbereich</h1>
        <p>Hallo <?= htmlspecialchars($_SESSION['username']) ?></p>
        <p>Du bist eingeloggt als <strong><?= htmlspecialchars($_SESSION['role']) ?></strong>.</p>
        <p>Nutze das Menü links, um administrative Aufgaben durchzuführen.</p>
    </div>
</div> <!-- ❗ Wichtig: dieser div schließt die dashboard-container -->

<?php include __DIR__ . '/../includes/footer.php'; ?>