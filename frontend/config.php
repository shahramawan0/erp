<?php
// Database config for frontend (PermissionHelper etc)
$envFile = file_exists(__DIR__ . '/.env') ? __DIR__ . '/.env' : dirname(__DIR__) . '/.env';
$dbHost = 'localhost';
$dbName = 'khawaja_traders';
$dbUser = 'root';
$dbPass = '';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($k, $v) = explode('=', $line, 2);
            $k = trim($k); $v = trim(trim($v), '"\'');
            if ($k === 'DB_HOST') $dbHost = $v;
            if ($k === 'DB_NAME') $dbName = $v;
            if ($k === 'DB_USERNAME') $dbUser = $v;
            if ($k === 'DB_PASSWORD') $dbPass = $v;
        }
    }
}
