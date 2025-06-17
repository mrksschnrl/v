<?php
$folder = __DIR__ . '/bootlegs-mp4';
$base_url = 'https://videomat.org/filestream/kraeftner/bootlegs-mp4';

$videos = array_filter(scandir($folder), function ($file) {
    return pathinfo($file, PATHINFO_EXTENSION) === 'mp4';
});

sort($videos); // optional: sort alphabetically

header('Content-Type: application/json');
echo json_encode(array_values($videos));
