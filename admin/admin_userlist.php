<?php
require_once __DIR__ . '/../modules/config.php';
require_once __DIR__ . '/../modules/db.php';
require_once __DIR__ . '/../modules/secure_admin.php';

$pageTitle = "Benutzerliste (einfach)";
$activePage = 'admin_userlist.php';
$loadThemeCSS = true;

include __DIR__ . '/../includes/header.php';

// Benutzer abrufen
$stmt = $pdo->query("SELECT id, username, email FROM users ORDER BY id DESC");
$users = $stmt->fetchAll();
?>

<div class="dashboard-container">
    <?php include __DIR__ . '/../includes/admin/sidebar.php'; ?>

    <div class="content">
        <h1>Benutzerliste (einfach)</h1>

        <?php if (empty($users)): ?>
            <p>Keine Benutzer gefunden.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($users as $user): ?>
                    <li>
                        #<?= $user['id'] ?> – <?= htmlspecialchars($user['username']) ?> (<?= htmlspecialchars($user['email']) ?>)
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>