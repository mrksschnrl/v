<?php
require_once __DIR__ . '/config.php';

function log_action(string $message): void
{
    $logfile = defined('LOG_PATH') ? LOG_PATH : (__DIR__ . '/../logs/admin_actions.log');
    $logdir = dirname($logfile);

    if (!is_dir($logdir)) {
        mkdir($logdir, 0775, true);
    }

    $timestamp = date('[Y-m-d H:i:s]');
    $user = $_SESSION['username'] ?? 'Gast';
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

    $entry = "$timestamp [$user] [$ip] $message" . PHP_EOL;
    file_put_contents($logfile, $entry, FILE_APPEND);
}
