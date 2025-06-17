<?php
header('Content-Type: application/json');
$data = file_get_contents('php://input');
echo json_encode([
    'ok' => true,
    'raw' => $data,
    'json' => json_decode($data, true),
    'error' => json_last_error_msg()
]);
