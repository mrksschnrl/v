<?php
require_once __DIR__ . '/../modules/config.php';
require_once __DIR__ . '/../modules/db.php';
require_once __DIR__ . '/../modules/secure_admin.php';

$pageTitle = "Benutzer verifizieren";
$loadThemeCSS = true;
include __DIR__ . '/../includes/header.php';

// Feedback-Variable
$feedback = '';

// Wenn ein Verifizierungs-Request kommt
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $userId = (int) $_POST['user_id'];
    $stmt = $pdo->prepare("UPDATE users SET is_verified = 1 WHERE id = ?");
    $stmt->execute([$userId]);
    $feedback = "✅ Benutzer mit ID $userId wurde verifiziert.";
}

// Nicht verifizierte Benutzer abrufen
$stmt = $pdo->query("SELECT id, username, email FROM users WHERE is_verified = 0 ORDER BY id DESC");
$unverifiedUsers = $stmt->fetchAll();
?>

<div class="dashboard-container">
    <?php include __DIR__ . '/../includes/admin/sidebar.php'; ?>

    <div class="content">
        <h1>Unverifizierte Benutzer</h1>

        <?php if (!empty($feedback)): ?>
            <p style="color: lime; font-weight: bold;"><?= $feedback ?></p>
        <?php endif; ?>

        <?php if (empty($unverifiedUsers)): ?>
            <p>🎉 Alle Benutzer sind bereits verifiziert.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Benutzername</th>
                        <th>Email</th>
                        <th>Aktion</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($unverifiedUsers as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <form method="post" style="margin:0;">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <button type="submit" class="button">✅ Verifizieren</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>