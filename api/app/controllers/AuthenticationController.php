<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/User.php';

class AuthenticationController extends BaseController {

    protected function isPublicEndpoint() {
        return true; // login is public
    }

    public function login() {
        if ($this->requestMethod !== 'POST') {
            $this->sendError('Method not allowed', 405);
        }
        $username = $this->requestData['username'] ?? $this->requestData['email'] ?? null;
        $password = $this->requestData['password'] ?? null;
        if (!$username || !$password) {
            $this->sendError('Email and password are required', 400);
        }
        $userModel = new User();
        $user = $userModel->authenticate($username, $password);
        if (!$user) {
            $this->sendError('Invalid email or password', 401);
        }
        // Khawaja Traders: Only allow SUA and A roles
        $roleStatus = $user['role_status'] ?? '';
        if (!in_array($roleStatus, ['SUA', 'A'])) {
            $this->sendError('Access denied. Only Admin or Super Admin can login.', 403);
        }
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + 43200);
        $userModel->updateLoginToken($user['id'], $token, $expires);
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->session->setUserSession($user);
        $this->session->regenerate();
        $this->sendSuccess([
            'user' => [
                'id' => $user['id'],
                'first_name' => $user['first_name'],
                'last_name' => $user['last_name'],
                'email' => $user['email'],
                'role_name' => $user['role_name'],
                'role_status' => $user['role_status'],
                'company_id' => $user['company_id'],
                'unit_id' => $user['unit_id'],
                'unit_name' => $user['unit_name'],
                'unit_name_urdu' => $user['unit_name_urdu'] ?? null,
                'unit_short_name' => $user['unit_short_name'] ?? null,
            ],
            'token' => $token,
            'token_expires' => $expires
        ]);
    }
}
?>
