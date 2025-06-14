<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['username']) && preg_match('/^[a-zA-Z0-9_-]+$/', $_SESSION['username'])) {
    echo json_encode(["username" => $_SESSION['username']]);
} else {
    http_response_code(403);
    echo json_encode(["error" => "Nicht eingeloggt"]);
}
