<?php
session_start();
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header("Location: login.php");
    exit;
}
?>


<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

set_time_limit(0);
function listDirs($dir) {
    return array_filter(glob("$dir/*"), 'is_dir');
}


$currentDir = isset($_GET['dir']) ? $_GET['dir'] : '/';
$selectFor = isset($_GET['select']) ? $_GET['select'] : '';
$sourceDir = isset($_GET['sourceDir']) ? $_GET['sourceDir'] : '';
$targetDir = isset($_GET['targetDir']) ? $_GET['targetDir'] : '';
$log = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ob_start();
    $sourceDir = rtrim($_POST['sourceDir'], '/');
    $targetDir = rtrim($_POST['targetDir'], '/');

    $m3u8Files = glob("$sourceDir/*.m3u8");
    $log[] = "Starte Sortierung...";

    foreach ($m3u8Files as $m3u8File) {
        $baseName = basename($m3u8File, '.m3u8');
        $folderPath = "$targetDir/$baseName";
        $log[] = "Bearbeite: ".$baseName;

        if (!is_dir($folderPath)) mkdir($folderPath, 0755, true);
        rename($m3u8File, "$folderPath/" . basename($m3u8File));
        $log[] = "✔️ M3U8 verschoben.";

        $tsFiles = glob("$sourceDir/$baseName*.ts");
        if (empty($tsFiles)) {
            $log[] = "⚠️ Keine TS-Dateien gefunden für ".$baseName.".";
        }
        foreach ($tsFiles as $tsFile) {
            rename($tsFile, "$folderPath/" . basename($tsFile));
            $log[] = "✔️ ".basename($tsFile)." verschoben.";
        }
    }
    $log[] = "✅ Sortierung abgeschlossen!";
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8"><title>M3U8 Sortierer (final korrigiert)</title>
<style>
body { font-family:Arial; margin:20px; }
input, button { padding:5px; width:400px; margin-bottom:10px; }
.browser, .log { padding:10px; border:1px solid #ccc; background:#f9f9f9; margin-top:20px; }
.browser a { text-decoration:none; }
</style>
</head>
<body>
<h2>M3U8 Sortierer (Ordnername ohne angehängte 0)</h2>

<form method="post">
<label>Quellordner:</label><br>
<input type="text" name="sourceDir" id="sourceDir" value="<?=$sourceDir?>" required>
<a href="?select=sourceDir&sourceDir=<?=urlencode($sourceDir)?>&targetDir=<?=urlencode($targetDir)?>">📁 Browse</a><br>

<label>Zielordner:</label><br>
<input type="text" name="targetDir" id="targetDir" value="<?=$targetDir?>" required>
<a href="?select=targetDir&sourceDir=<?=urlencode($sourceDir)?>&targetDir=<?=urlencode($targetDir)?>">📁 Browse</a><br>

<button type="submit">Sortieren</button>
</form>

<?php if($selectFor): ?>
<div class="browser">
<strong>Aktuelles Verzeichnis:</strong> <?=$currentDir?><br><br>
<a href="?dir=<?=dirname($currentDir)?>&select=<?=$selectFor?>&sourceDir=<?=urlencode($sourceDir)?>&targetDir=<?=urlencode($targetDir)?>">⬆️ Übergeordnet</a><br>
<?php foreach(listDirs($currentDir) as $dir): ?>
<a href="?dir=<?=$dir?>&select=<?=$selectFor?>&sourceDir=<?=urlencode($sourceDir)?>&targetDir=<?=urlencode($targetDir)?>">📂 <?=basename($dir)?></a><br>
<?php endforeach; ?>
<br>
<a href="?<?=$selectFor?>=<?=urlencode($currentDir)?>&sourceDir=<?=urlencode($selectFor=='sourceDir'?$currentDir:$sourceDir)?>&targetDir=<?=urlencode($selectFor=='targetDir'?$currentDir:$targetDir)?>">✅ Diesen Ordner auswählen</a>
</div>
<?php endif; ?>

<?php if(!empty($log)): ?>
<div class="log">
<strong>📄 Sortier-Report:</strong><br>
<?=implode('<br>', $log)?>
</div>
<?php endif; ?>

</body>
</html>
