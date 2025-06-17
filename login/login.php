<?php
// Basis-Pfad zu allen Theme-CSS
$theme_path = '/v/css/themes/theme_first/css/';
?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Login</title>

    <!-- Google-Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@400;700&family=Roboto+Mono&display=swap" rel="stylesheet">

    <!-- Core-Styles -->
    <link rel="stylesheet" href="<?= $theme_path ?>base.css">
    <link rel="stylesheet" href="<?= $theme_path ?>theme.css">
    <link rel="stylesheet" href="<?= $theme_path ?>components.css">

    <!-- Logo-Layer (SVGs übereinander) -->
    <link rel="stylesheet" href="<?= $theme_path ?>logo.css">

    <!-- Seite-spezifisch -->
    <link rel="stylesheet" href="<?= $theme_path ?>login.css">
</head>

<body class="login-page">

    <div class="center-wrapper center-login-tight">

        <!-- Logo -->
        <div class="logo-center-wrapper">
            <a href="/v/index.php">
                <?php include __DIR__ . '/../includes/ci/logo.php'; ?>
            </a>
        </div>

        <!-- Login-Formular -->
        <div class="login-container">
            <form action="login_check.php" method="post" class="login-form">
                <h2>Login</h2>

                <label for="username">Benutzername:</label>
                <input type="text" id="username" name="username" required class="login-input">

                <label for="password">Passwort:</label>
                <input type="password" id="password" name="password" required class="login-input">

                <div class="button-group">
                    <button type="submit" class="button">Anmelden</button>
                    <a href="/v/register/register.php" class="button">Registrieren</a>
                </div>
            </form>
        </div>

    </div>

</body>

</html>