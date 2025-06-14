<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

require_once __DIR__ . '/../modules/db.php';
require_once __DIR__ . '/../modules/config.php';
require_once __DIR__ . '/../modules/log_helper.php';

/* Pfade (alles kleingeschrieben) */
$theme_path = '/v/css/themes/theme_first/css/';
$logo_css   = $theme_path . 'logo.css';

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username   = trim($_POST['username'] ?? '');
    $email      = trim($_POST['email'] ?? '');
    $pw         = $_POST['pw'] ?? '';
    $pw_confirm = $_POST['pw_confirm'] ?? '';

    if (!$username || !$email || !$pw || !$pw_confirm) {
        $error = 'Bitte alle Felder ausfüllen.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Ungültige E-Mail-Adresse.';
    } elseif ($pw !== $pw_confirm) {
        $error = 'Passwörter stimmen nicht überein.';
    } elseif (!preg_match('/^[a-zA-Z0-9_-]{3,}$/', $username)) {
        $error = 'Benutzername enthält ungültige Zeichen.';
    } else {
        $check = $pdo->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
        $check->execute([$username, $email]);

        if ($check->fetch()) {
            $error = 'Benutzername oder E-Mail bereits vergeben.';
        } else {
            $hashed = password_hash($pw, PASSWORD_DEFAULT);
            $ins    = $pdo->prepare(
                "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')"
            );
            $ins->execute([$username, $email, $hashed]);

            // 📁 Benutzerordner + Unterordner anlegen
            $userFolder = __DIR__ . "/../users/accounts/" . $username;
            if (!is_dir($userFolder)) {
                mkdir($userFolder, 0775, true);
            }

            $subdirs = ['uploads', 'meta', 'presets'];
            foreach ($subdirs as $dir) {
                $path = $userFolder . '/' . $dir;
                if (!is_dir($path)) {
                    mkdir($path, 0775, true);
                }
            }

            log_action("Neuer Benutzer registriert: $username");
            $success = "Registrierung erfolgreich. Du kannst dich jetzt <a href='/v/login.php'>einloggen</a>.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Registrierung</title>

    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@400;700&family=Roboto+Mono&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $theme_path ?>base.css">
    <link rel="stylesheet" href="<?= $theme_path ?>theme.css">
    <link rel="stylesheet" href="<?= $theme_path ?>components.css">
    <link rel="stylesheet" href="<?= $logo_css ?>">
    <link rel="stylesheet" href="<?= $theme_path ?>login.css">
</head>

<body class="login-page">
    <div class="center-wrapper">
        <div class="logo-center-wrapper">
            <a href="/v/index.php">
                <?php include __DIR__ . '/../includes/ci/logo.php'; ?>
            </a>
        </div>

        <div class="login-container">
            <h2>➕ Registrierung</h2>

            <?php if ($error): ?>
                <p class="error-message"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <?php if ($success): ?>
                <p class="success-message"><?= $success ?></p>
            <?php endif; ?>

            <form method="POST" class="login-form">
                <input type="text" name="username" placeholder="Benutzername" required>
                <input type="email" name="email" placeholder="E-Mail-Adresse" required>
                <input type="password" name="pw" placeholder="Passwort" required>
                <input type="password" name="pw_confirm" placeholder="Passwort bestätigen" required>

                <div class="button-group">
                    <button type="submit" class="button">Registrieren</button>
                    <a href="/v/login.php" class="button">Zum Login</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>