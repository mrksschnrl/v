<?php
require_once __DIR__ . '/../modules/config.php';
require_once __DIR__ . '/../modules/db.php';
require_once __DIR__ . '/../modules/secure_admin.php';

$pageTitle = "Userliste – erweitert";
$activePage = 'users_extended.php'; // Passe ggf. Dateinamen an
$loadThemeCSS = true;

include __DIR__ . '/../includes/header.php';

// Benutzer abrufen
$stmt = $pdo->query("SELECT id, username, email, role, is_verified, created_at FROM users ORDER BY id DESC");
$users = $stmt->fetchAll();
?>

<div class="dashboard-container">
    <?php include __DIR__ . '/../includes/admin/sidebar.php'; ?>

    <div class="content">
        <h1>Benutzerliste – erweitert</h1>

        <?php if (empty($users)): ?>
            <p>Keine Benutzer gefunden.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Benutzername</th>
                        <th>Email</th>
                        <th>Rolle</th>
                        <th>Verifiziert</th>
                        <th>Erstellt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['role']) ?></td>
                            <td><?= $user['is_verified'] ? '✅' : '❌' ?></td>
                            <td><?= htmlspecialchars($user['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>