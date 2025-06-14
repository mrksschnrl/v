<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../modules/db.php';
require_once __DIR__ . '/../modules/config.php';

$token = $_GET['token'] ?? '';
$message = '';
$isError = false;

if (empty($token)) {
    $message = "Kein Verifizierungs-Token angegeben.";
    $isError = true;
} else {
    $stmt = $pdo->prepare("SELECT id, is_verified FROM users WHERE verify_token = ? LIMIT 1");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if (!$user) {
        $message = "Ungültiger oder abgelaufener Verifizierungslink.";
        $isError = true;
    } elseif ($user['is_verified']) {
        $message = "ℹDein Konto wurde bereits bestätigt.";
    } else {
        $stmt = $pdo->prepare("UPDATE users SET is_verified = 1, verify_token = NULL WHERE id = ?");
        $stmt->execute([$user['id']]);
        $message = "Dein Konto wurde erfolgreich verifiziert. Du wirst in Kürze weitergeleitet...";
        header("refresh:5;url=" . LOGIN_PAGE);
    }
}

$pageTitle = "Verifizierung";
$loadThemeCSS = true;
include __DIR__ . '/../includes/header.php';
?>

<div class="container">
    <h1>Verifizierung</h1>
    <p class="<?= $isError ? 'error-message' : 'success-message' ?>"><?= $message ?></p>

    <?php if (!$isError): ?>
        <p>Falls du nicht automatisch weitergeleitet wirst, <a href="<?= LOGIN_PAGE ?>">klicke hier</a>.</p>
    <?php endif; ?>

    <?php if (IS_DEV_MODE): ?>
        <p style="color:orange;">Dev-Modus aktiv – Token war: <code><?= htmlspecialchars($token) ?></code></p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>