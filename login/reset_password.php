<?php include __DIR__ . '/../includes/header.php'; ?>
<?php
require_once __DIR__ . '/../modules/db.php';

$token_raw = $_GET['token'] ?? '';
$message = '';
$isError = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token_raw = $_POST['token'] ?? '';
    $new_pw = $_POST['new_pw'] ?? '';
    $confirm_pw = $_POST['confirm_pw'] ?? '';

    if ($new_pw !== $confirm_pw) {
        $message = "Die Passwörter stimmen nicht überein.";
        $isError = true;
    } else {
        $token_hash = hash('sha256', $token_raw);

        $stmt = $pdo->prepare("SELECT * FROM users WHERE reset_token = ? AND reset_expires > NOW()");
        $stmt->execute([$token_hash]);
        $user = $stmt->fetch();

        if ($user) {
            $hash = password_hash($new_pw, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
            $stmt->execute([$hash, $user['id']]);

            $message = "Passwort wurde erfolgreich geändert. Du kannst dich jetzt einloggen.";
        } else {
            $message = "Ungültiger oder abgelaufener Link.";
            $isError = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Neues Passwort</title>
    <style>
        :root {
            --success-color: #4caf50;
            --error-color: #ff4c4c;
        }
    </style>
</head>

<body>

    <div class="login-wrapper">
        <div class="login-container">
            <h1>Neues Passwort setzen</h1>

            <form method="POST">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token_raw) ?>">
                <input type="password" name="new_pw" placeholder="Neues Passwort" required>
                <input type="password" name="confirm_pw" placeholder="Passwort wiederholen" required>
                <button type="submit">Speichern</button>
            </form>

            <?php if ($message): ?>
                <p class="<?= $isError ? 'error-message' : 'success-message' ?>"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

            <div class="message">
                <a href="/v/login.php" class="button-link">Zurück zum Login</a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const pw = document.querySelector('input[name="new_pw"]');
            const confirm = document.querySelector('input[name="confirm_pw"]');

            const status = document.createElement('div');
            status.style.marginTop = '0.5rem';
            confirm.insertAdjacentElement('afterend', status);

            function validatePasswords() {
                if (!pw.value || !confirm.value) {
                    status.textContent = '';
                    return;
                }

                if (pw.value === confirm.value) {
                    status.textContent = 'Passwörter stimmen überein';
                    status.style.color = 'var(--success-color)';
                } else {
                    status.textContent = 'Passwörter stimmen nicht überein';
                    status.style.color = 'var(--error-color)';
                }
            }

            pw.addEventListener('input', validatePasswords);
            confirm.addEventListener('input', validatePasswords);
        });
    </script>

</body>

</html>
<?php include __DIR__ . '/../includes/footer.php'; ?>