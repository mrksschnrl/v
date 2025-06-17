<?php
session_start();
session_destroy();
header("Location: login.php");
exit();
?>

<p><a href="logout.php">🚪 Logout</a></p>
