<?php
require_once __DIR__ . '/../modules/config.php';
require_once __DIR__ . '/../modules/db.php';
require_once __DIR__ . '/../modules/secure_admin.php';

$pageTitle = "Adminbereich";
include __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-container">
    <div class="sidebar">
        <h2>Admin-Menü</h2>
        <ul>
            <li><a href="admin_dashboard.php">📊 Dashboard</a></li>
            <li><a href="admin_tools.php">🛠️ Tools</a></li>
            <li><a href="manage_users.php">👥 Benutzerverwaltung</a></li>
            <li><a href="verify_users.php">✅ Verifizieren</a></li>
            <li><a href="admin_logs.php">🧾 Logs</a></li>
            <li><a href="<?= LOGOUT_PAGE ?>">🚪 Logout</a></li>
        </ul>
    </div>

    <div class="content">
        <h1>👑 Adminbereich</h1>
        <p>Willkommen, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>!</p>
        <p>Nutze das Menü links, um administrative Funktionen aufzurufen.</p>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>