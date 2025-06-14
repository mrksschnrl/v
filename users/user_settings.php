<?php
session_start();
require_once __DIR__ . '/../modules/config.php';
require_once __DIR__ . '/../modules/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: " . LOGIN_PAGE);
    exit;
}

$userId = $_SESSION['user_id'];
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Passwort ändern
    if (!empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
        $new_pw = $_POST['new_password'];
        $confirm_pw = $_POST['confirm_password'];
        if ($new_pw !== $confirm_pw) {
            $error = "❌ Passwörter stimmen nicht überein.";
        } else {
            $hash = password_hash($new_pw, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hash, $userId]);
            $success = "✅ Passwort erfolgreich geändert.";
        }
    }

    // E-Mail ändern
    if (!empty($_POST['email'])) {
        $email = trim($_POST['email']);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "❌ Ungültige E-Mail-Adresse.";
        } else {
            $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
            $stmt->execute([$email, $userId]);
            $success = "✅ E-Mail erfolgreich geändert.";
        }
    }
}

// Nutzername laden
$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$userId]);
$username = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <title>Benutzereinstellungen</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

    <h2>Einstellungen für <?= htmlspecialchars($username) ?></h2>

    <form method="post">
        <label>Neues Passwort:</label>
        <input type="password" name="new_password" placeholder="Neues Passwort">

        <label>Passwort wiederholen:</label>
        <input type="password" name="confirm_password" placeholder="Wiederholen">

        <label>Neue E-Mail-Adresse:</label>
        <input type="email" name="email" placeholder="E-Mail eingeben">

        <button type="submit">Änderungen speichern</button>
    </form>

    <?php if ($error): ?>
        <p class="message" style="color:red;"><?= htmlspecialchars($error) ?></p>
    <?php elseif ($success): ?>
        <p class="message" style="color:lightgreen;"><?= htmlspecialchars($success) ?></p>
    <?php endif; ?>

    <p class="message">
        <a href="<?= USER_DASHBOARD ?>" style="color: #0bf;">⬅ Zurück zum Dashboard</a>
    </p>

</body>

</html>