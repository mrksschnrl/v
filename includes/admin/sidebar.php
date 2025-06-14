<?php
// muss außerhalb von HTML stehen
$current = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">
    <h2>Admin-Menü</h2>
    <ul>
        <li><a href="admin_dashboard.php" class="<?= $current === 'admin_dashboard.php' ? 'active' : '' ?>">Übersicht</a></li>
        <li><a href="admin_tools.php" class="<?= $current === 'admin_tools.php' ? 'active' : '' ?>">Dateiverwaltung & Tools</a></li>
        <li><a href="manage_users.php" class="<?= $current === 'manage_users.php' ? 'active' : '' ?>">Benutzer verwalten</a></li>
        <li><a href="verify_users.php" class="<?= $current === 'verify_users.php' ? 'active' : '' ?>">Benutzer verifizieren</a></li>
        <li><a href="admin_logs.php" class="<?= $current === 'admin_logs.php' ? 'active' : '' ?>">Logs</a></li>
        <li><a href="admin_userlist.php" class="<?= $activePage === 'admin_userlist.php' ? 'active' : '' ?>">Userliste (einfach)</a></li>

        <li><a href="<?= LOGOUT_PAGE ?>">Logout</a></li>
    </ul>
</div>