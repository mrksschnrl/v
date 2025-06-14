<?php
// list_input_files.php

$directory = __DIR__ . '/input_mp4/';
$files = array_diff(scandir($directory), array('..', '.'));

header('Content-Type: application/json');
echo json_encode(array_values($files));
