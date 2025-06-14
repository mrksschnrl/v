<?php
require_once __DIR__ . '/../modules/config.php';
require_once __DIR__ . '/../modules/db.php';
require_once __DIR__ . '/../modules/secure_admin.php';

$pageTitle = "Benutzer verwalten";
$loadThemeCSS = true;
include __DIR__ . '/../includes/header.php';

// Verifizierung und Löschung
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['verify_id'])) {
        $id = (int) $_POST['verify_id'];
        $pdo->prepare("UPDATE users SET is_verified = 1 WHERE id = ?")->execute([$id]);
    }
    if (isset($_POST['delete_id'])) {
        $id = (int) $_POST['delete_id'];
        if ($id === $_SESSION['user_id']) {
            echo "<script>alert('Du kannst dich nicht selbst löschen.');</script>";
        } else {
            $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
        }
    }
}

// Alle Benutzer abrufen
$stmt = $pdo->query("SELECT id, username, email, role, is_verified, created_at FROM users ORDER BY id DESC");
$users = $stmt->fetchAll();
?>

<div class="dashboard-container">
    <?php include __DIR__ . '/../includes/admin/sidebar.php'; ?>

    <div class="content">
        <h1>Benutzerverwaltung</h1>

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
                        <th>Aktionen</th>
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
                            <td>
                                <form method="post" style="display:inline;">
                                    <?php if (!$user['is_verified']): ?>
                                        <input type="hidden" name="verify_id" value="<?= $user['id'] ?>">
                                        <button type="submit" title="Verifizieren">✅</button>
                                    <?php endif; ?>
                                </form>
                                <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                                    <form method="post" onsubmit="return confirm('Diesen Benutzer wirklich löschen?');" style="display:inline;">
                                        <input type="hidden" name="delete_id" value="<?= $user['id'] ?>">
                                        <button type="submit" title="Löschen">🗑️</button>
                                    </form>
                                <?php else: ?>
                                    <em>(eigener Account)</em>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>