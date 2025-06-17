<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

$pageTitle = 'Login - GETFILES';

// Optional: Theme setzen, falls noch nicht vorhanden
if (!isset($_SESSION['theme'])) {
  $_SESSION['theme'] = 'theme_first';
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if ($_POST["password"] === "Kunstklub") {
    $_SESSION["logged_in"] = true;
    header("Location: index.php");
    exit;
  } else {
    $error = "Incorrect password.";
  }
}

include $_SERVER['DOCUMENT_ROOT'] . '/v/includes/header.php';
?>

<div class="login-wrapper">
  <form method="POST" class="login-form">
    <input type="password" name="password" placeholder="Password" required class="login-input" />
    <button type="submit" class="button">Login</button>

    <?php if (isset($error)) : ?>
      <p class="error-message"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
  </form>
</div>

<?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) : ?>
  <p class="login-hint">
    ✅ Logged in, but redirect failed.
    <a href="index.php">Go to index manually</a>
  </p>
<?php endif; ?>

</body>

</html>