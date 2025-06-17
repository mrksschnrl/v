<?php
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

if ($_ENV['APP_ENV'] !== 'production') {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

define('UPLOAD_ROOT', rtrim($_ENV['UPLOAD_ROOT'] ?? __DIR__ . '/../output', '/'));
define('LOG_PATH',    rtrim($_ENV['LOG_PATH']    ?? __DIR__ . '/../log',    '/'));

ini_set('display_errors', 1);
error_reporting(E_ALL);

/* Theme-Ordner mit allen CSS-Dateien */
$themeBase = '/v/css/themes/theme_first/css';
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Startseite</title>

    <!-- Google-Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@400;700&family=Roboto+Mono&display=swap" rel="stylesheet">

    <!-- Core-Styles -->
    <link rel="stylesheet" href="<?= $themeBase ?>/base.css">
    <link rel="stylesheet" href="<?= $themeBase ?>/theme.css">

    <!-- Intro / Logo-Styles (V über O) -->
    <link rel="stylesheet" href="<?= $themeBase ?>/logo-start.css">
</head>

<!-- Klasse steuert das Flex-Zentrieren nur hier -->

<body class="logo-start-page">

    <div class="logo-group">
        <div class="logo-inner">
            <!-- V über O -->
            <img src="/v/includes/ci/o.svg" alt="Kreis" class="logo-circle">
            <img src="/v/includes/ci/v.svg" alt="V" class="logo-v">
        </div>
    </div>

    <!-- Klick führt zum Login -->
    <script>
        document.querySelector('.logo-group')
            .addEventListener('click', () => location.href = '/v/login.php');
    </script>

</body>

</html>