<?php
session_start();

require_once __DIR__ . '/../modules/log_helper.php';
require_once __DIR__ . '/../modules/config.php';

log_action("Logout durchgeführt");

session_destroy();
header("Location: " . LOGIN_PAGE);
exit;