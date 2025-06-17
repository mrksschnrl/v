<?php include __DIR__ . '/../includes/header.php'; ?>
<?php
require_once __DIR__ . '/../modules/db.php';

$message = '';
$isError = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        $token_raw = bin2hex(random_bytes(32));
        $token_hash = hash('sha256', $token_raw);
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?");
        $stmt->execute([$token_hash, $expires, $user['id']]);

        $resetLink = "http://116.202.165.101/v/login/reset_password.php?token=$token_raw";

        // In Produktion durch SMTP ersetzen
        mail(
            $email,
            "Passwort zurücksetzen",
            "Hallo {$user['username']},\n\nDu kannst dein Passwort hier zurücksetzen:\n$resetLink\n\nDer Link ist 1 Stunde gültig.",
            "From: support@kraeftner.at"
        );

        $message = "Link zum Zurücksetzen wurde an deine E-Mail gesendet.";
    } else {
        $message = "Kein Benutzer mit dieser E-Mail gefunden.";
        $isError = true;
    }
}
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Passwort vergessen</title>
</head>

<body>

    <div class="login-wrapper">
        <div class="login-container">
            <h1>Passwort zurücksetzen</h1>

            <form method="POST">
                <input type="email" name="email" placeholder="Deine E-Mail-Adresse" required>
                <button type="submit">Link senden</button>
            </form>

            <?php if ($message): ?>
                <p class="<?= $isError ? 'error-message' : 'success-message' ?>"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

            <div class="message">
                <a href="/v/login.php" class="button-link">Zurück zum Login</a>
            </div>
        </div>
    </div>

</body>

</html>
<?php include __DIR__ . '/../includes/footer.php'; ?>