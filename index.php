<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

$theme_path = '/v/css/themes/theme_first/css/';
$logo_css   = $theme_path . 'logo.css';
$username   = htmlspecialchars($_SESSION['user'] ?? '');
?>
<!DOCTYPE html>
<html lang="de">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="css/file-sort.css" />
  <title>File Sort</title>
</head>

<body>
  <div class="container">
    <form id="file-sort-form">
      <div>
        <label for="from-date">Von:</label>
        <input type="date" id="from-date" />
        <input type="text" id="from-raw" placeholder="YYYYMMDD" maxlength="8" />
      </div>
      <div>
        <label for="to-date">Bis:</label>
        <input type="date" id="to-date" />
        <input type="text" id="to-raw" placeholder="YYYYMMDD" maxlength="8" />
      </div>
      <div>
        <label for="custom-date">Benutzerdefiniert:</label>
        <input type="date" id="custom-date" />
        <input type="text" id="custom-raw" placeholder="YYYYMMDD" maxlength="8" />
      </div>
    </form>

    <div class="quick-buttons">
      <button id="btn-today" type="button">Heute</button>
      <button id="btn-yesterday" type="button">Gestern</button>
      <button id="btn-last7" type="button">Letzte 7 Tage</button>
    </div>

    <div class="workflow-buttons">
      <button id="btn-gen-meta" type="button">Metadaten erstellen</button>
      <button id="btn-sort-files" type="button" disabled>Dateien sortieren</button>
    </div>

    <div id="log" class="log"></div>
  </div>

  <!-- Service Worker Registration -->
  <script>
    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.register('sw.js', {
          scope: './'
        })
        .then(reg => console.log('SW registered, scope:', reg.scope))
        .catch(err => console.error('SW registration failed:', err));
    }
  </script>

  <!-- Lade deine Module -->
  <script type="module" src="js/ui_handlers.js" defer></script>
</body>

</html>