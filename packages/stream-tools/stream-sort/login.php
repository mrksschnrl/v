<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $password = "Kunstklub"; // <<< Passwort hier anpassen

    if (isset($_POST["pw"]) && $_POST["pw"] === $password) {
        $_SESSION["authenticated"] = true;
        $_SESSION["zeit"] = time();
        header("Location: stream-sort-server.php");
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
    <title>Login</title>
    <style>
        body {
            font-family: 'Roboto Mono', monospace;
            background-color: #000;
            color: white;
            text-align: center;
            padding: 100px;
        }
        h1 {
            font-size: 42px;
            margin-bottom: 40px;
        }
        .form-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
        }
        .form-container input,
        .form-container button {
            font-size: 20px;
            padding: 10px 20px;
            border-radius: 8px;
            border: 2px solid #888;
            background: #222;
            color: white;
            cursor: pointer;
        }
        .form-container button {
            background: #555;
            font-weight: bold;
        }
        .form-container button:hover {
            background: whitesmoke;
            color: black;
        }
        .form-container input:hover,
        .form-container input:focus {
            background: #333;
            border-color: whitesmoke;
            outline: none;
        }
        .error-message {
            color: red;
            font-size: 16px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>🔐 Login</h1>
    <div class="form-container">
        <form method="POST">
            <input type="password" name="pw" placeholder="Passwort" required>
            <button type="submit">Login</button>
        </form>
    </div>
    <?php if (isset($error)) echo "<p class='error-message'>$error</p>"; ?>
</body>
</html>
