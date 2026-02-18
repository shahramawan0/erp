<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

require_once __DIR__ . '/../app/controllers/AuthenticationController.php';

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$parts = explode('/', trim($path, '/'));
$action = end($parts);
$action = explode('?', $action)[0];

$controller = new AuthenticationController();
if ($action === 'login' && method_exists($controller, 'login')) {
    $controller->login();
} else {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Invalid endpoint']);
}
?>
