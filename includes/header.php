<?php
require_once __DIR__ . '/../modules/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$theme = $_SESSION['theme'] ?? $_GET['theme'] ?? DEFAULT_THEME;
$themeBase = "/v/css/themes/$theme/css"; // neues css-Verzeichnis beachten

$page = basename($_SERVER['PHP_SELF'], '.php');
$pageCssPath = "$themeBase/pages/$page.css";
$pageCssFullPath = $_SERVER['DOCUMENT_ROOT'] . $pageCssPath;

$css_path_ci = '/v/includes/ci/css/';
?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="robots" content="noindex, nofollow" />
    <title><?= $pageTitle ?? 'Seite' ?></title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Slab:wght@400;700&family=Roboto+Mono&display=swap" rel="stylesheet" />


    <?php
    $themeBase = "/v/css/themes/$theme/css";   // zeigt jetzt auf .../css/
    ?>

    <!-- Theme & CI Styles -->
    <link rel="stylesheet" href="<?= $themeBase ?>/base.css">
    <link rel="stylesheet" href="<?= $themeBase ?>/theme.css">
    <link rel="stylesheet" href="<?= $themeBase ?>/components.css">
    <link rel="stylesheet" href="<?= $themeBase ?>/format.css">
    <link rel="stylesheet" href="<?= $themeBase ?>/header.css"> <!-- CI -->
    <link rel="stylesheet" href="<?= $themeBase ?>/logo.css"> <!-- CI -->


    <!-- Page-spezifisches CSS -->
    <?php if (file_exists($pageCssFullPath)) : ?>
        <link rel=" stylesheet" href="<?= $pageCssPath ?>">
    <?php endif; ?>
</head>

<body>

    <header class="header-container">
        <div class="header-title">
            <span><?= $pageTitle ?? 'Seite' ?></span>
        </div>
        <div class="header-logo-wrapper">
            <?php include __DIR__ . '/ci/logo.php'; ?>
        </div>
    </header>