<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

require_once __DIR__ . '/../app/controllers/AccountController.php';

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$parts = array_values(array_filter(explode('/', trim($path, '/'))));
if (isset($parts[0]) && $parts[0] === 'khawaja_traders') array_shift($parts);
if (isset($parts[0]) && $parts[0] === 'api') array_shift($parts);
if (isset($parts[0]) && $parts[0] === 'accounts') array_shift($parts);

$ctrl = new AccountController();
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    if (empty($parts)) {
        $ctrl->index();
    } elseif (isset($parts[0]) && $parts[0] === 'main-heads') {
        $ctrl->getMainHeads();
    } elseif (isset($parts[0]) && $parts[0] === 'control-heads') {
        $ctrl->getControlHeads();
    } elseif (isset($parts[0]) && is_numeric($parts[0])) {
        $ctrl->show();
    } else {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Not found']);
    }
} elseif ($method === 'POST' && empty($parts)) {
    $ctrl->store();
} elseif ($method === 'PUT' && isset($parts[0]) && is_numeric($parts[0])) {
    $ctrl->update();
} elseif ($method === 'DELETE' && isset($parts[0]) && is_numeric($parts[0])) {
    $ctrl->destroy();
} else {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Not found']);
}
