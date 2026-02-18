<?php
require_once __DIR__ . '/../config/Session.php';

abstract class BaseController {
    protected $requestMethod;
    protected $requestData;
    protected $userId;
    protected $userRole;
    protected $session;

    public function __construct() {
        $this->session = new Session();
        $this->requestMethod = $_SERVER['REQUEST_METHOD'];
        $this->requestData = $this->getRequestData();
        $this->authenticateRequest();
    }

    protected function getRequestData() {
        $data = [];
        switch ($this->requestMethod) {
            case 'GET': $data = $_GET; break;
            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                if (!$data) $data = $_POST;
                break;
            case 'PUT':
            case 'PATCH':
                $data = json_decode(file_get_contents('php://input'), true);
                if (!$data) $data = $_POST;
                break;
            case 'DELETE': $data = $_GET; break;
        }
        return $data ?: [];
    }

    protected function authenticateRequest() {
        $token = null;
        if (function_exists('getallheaders')) {
            foreach (getallheaders() as $k => $v) {
                if (strtolower($k) === 'authorization' && preg_match('/Bearer\s+(.*)$/i', $v, $m))
                    $token = $m[1];
                if (strtolower($k) === 'x-auth-token') $token = $v;
            }
        }
        if ($token) {
            require_once __DIR__ . '/../models/User.php';
            $user = (new User())->validateLoginToken($token);
            if ($user && in_array($user['role_status'] ?? '', ['SUA', 'A'])) {
                $this->userId = $user['id'];
                $this->userRole = $user['role_name'];
                return;
            }
        }
        if ($this->session->isAuthenticated()) {
            if ($this->session->isExpired()) {
                $this->session->clear();
                if (!$this->isPublicEndpoint()) $this->sendError('Session expired', 401);
                return;
            }
            $this->session->updateActivity();
            $us = $this->session->getUserSession();
            if ($us) {
                $this->userId = $us['user_id'];
                $this->userRole = $us['role_name'];
            }
        } elseif (!$this->isPublicEndpoint()) {
            $this->sendError('Authentication required', 401);
        }
    }

    protected function isPublicEndpoint() { return false; }

    protected function sendResponse($code, $data) {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        if (ob_get_level()) ob_clean();
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function sendSuccess($data = [], $code = 200) {
        $payload = ['success' => true, 'timestamp' => date('Y-m-d H:i:s')];
        if (is_array($data)) {
            $payload['data'] = $data;
            $payload = array_merge($payload, $data); // Also top-level for auth etc
        } else {
            $payload['message'] = $data;
        }
        $this->sendResponse($code, $payload);
    }

    protected function validateRequired($data, $required) {
        $errors = [];
        foreach ($required as $field) {
            if (!isset($data[$field]) || (is_string($data[$field]) && trim($data[$field]) === '')) {
                $errors[] = "Field '$field' is required";
            }
        }
        return $errors;
    }

    protected function sendError($msg, $code = 400, $errors = []) {
        $r = ['success' => false, 'error' => $msg, 'timestamp' => date('Y-m-d H:i:s')];
        if (!empty($errors)) $r['errors'] = $errors;
        $this->sendResponse($code, $r);
    }

    protected function getCurrentUser() { return $this->session->getUserSession(); }

    protected function getRequestId() {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $parts = array_values(array_filter(explode('/', trim($path, '/'))));
        if (isset($parts[0]) && $parts[0] === 'khawaja_traders') array_shift($parts);
        if (isset($parts[0]) && $parts[0] === 'api') array_shift($parts);
        if (isset($parts[0]) && strpos($parts[0], '.php') !== false) array_shift($parts);
        while (isset($parts[0]) && !is_numeric($parts[0])) array_shift($parts);
        return isset($parts[0]) && is_numeric($parts[0]) ? (int)$parts[0] : null;
    }
}
?>
