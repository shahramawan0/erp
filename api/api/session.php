<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

require_once __DIR__ . '/../app/config/Session.php';

$session = new Session();
if ($session->isAuthenticated()) {
    $user = $session->getUserSession();
    if ($user && in_array($user['role_status'] ?? '', ['SUA', 'A'])) {
        echo json_encode(['success' => true, 'user' => $user]);
        exit;
    }
}
http_response_code(401);
echo json_encode(['success' => false, 'error' => 'Not authenticated']);
?>
