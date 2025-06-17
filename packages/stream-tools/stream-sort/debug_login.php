<?php
// Diese Zeile nur aktivieren, wenn du Session-Probleme hast:
// session_save_path("/tmp");

session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h3>🛠️ DEBUG: Session-Status</h3>";
echo "<pre>";
echo "Session ID: " . session_id() . "\n";
echo "Session Save Path: " . session_save_path() . "\n";
print_r($_SESSION);
echo "</pre>";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = "Kunstklub";

    if (isset($_POST["pw"]) && $_POST["pw"] === $password) {
        $_SESSION["authenticated"] = true;
        $_SESSION["zeit"] = time();

        echo "<p>✅ Login erfolgreich – Weiterleitung...</p>";
        header("Refresh: 2; URL=stream-sort-server.php");
        exit();
    } else {
        $error = "❌ Falsches Passwort!";
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>DEBUG Login</title>
</head>
<body>
    <h2>🔐 DEBUG Login</h2>
    <form method="POST">
        <input type="password" name="pw" placeholder="Passwort" required>
        <button type="submit">Login</button>
    </form>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
</body>
</html>
