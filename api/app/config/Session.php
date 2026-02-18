<?php
class Session {
    private $sessionLifetime = 86400;

    public function __construct() {
        if (!headers_sent() && session_status() === PHP_SESSION_NONE) {
            ini_set('session.gc_maxlifetime', $this->sessionLifetime);
            ini_set('session.cookie_path', '/');
        }
        if (session_status() === PHP_SESSION_NONE) session_start();
    }

    public function set($key, $value) { $_SESSION[$key] = $value; }
    public function get($key, $default = null) { return $_SESSION[$key] ?? $default; }
    public function has($key) { return isset($_SESSION[$key]); }
    public function remove($key) { unset($_SESSION[$key]); }
    public function clear() { session_unset(); session_destroy(); }
    public function regenerate() { if (!headers_sent()) @session_regenerate_id(true); }

    public function setUserSession($userData) {
        $this->set('user_id', $userData['id']);
        $this->set('username', $userData['email']);
        $this->set('email', $userData['email']);
        $this->set('role_name', $userData['role_name']);
        $this->set('role_status', $userData['role_status']);
        $this->set('company_id', $userData['company_id']);
        $this->set('first_name', $userData['first_name']);
        $this->set('last_name', $userData['last_name']);
        $this->set('unit_id', $userData['unit_id'] ?? null);
        $this->set('unit_name', $userData['unit_name'] ?? null);
        $this->set('unit_name_urdu', $userData['unit_name_urdu'] ?? null);
        $this->set('unit_short_name', $userData['unit_short_name'] ?? null);
        $this->set('site_id', $userData['site_id'] ?? null);
        $this->set('site_name', $userData['site_name'] ?? null);
        $this->set('department_id', $userData['department_id'] ?? null);
        $this->set('department_name', $userData['department_name'] ?? null);
        $this->set('last_activity', time());
        $this->set('is_authenticated', true);
    }

    public function getUserSession() {
        if (!$this->has('is_authenticated') || !$this->get('is_authenticated')) return null;
        return [
            'user_id' => $this->get('user_id'),
            'email' => $this->get('email'),
            'role_name' => $this->get('role_name'),
            'role_status' => $this->get('role_status'),
            'company_id' => $this->get('company_id'),
            'first_name' => $this->get('first_name'),
            'last_name' => $this->get('last_name'),
            'unit_id' => $this->get('unit_id'),
            'unit_name' => $this->get('unit_name'),
            'unit_name_urdu' => $this->get('unit_name_urdu'),
            'unit_short_name' => $this->get('unit_short_name'),
        ];
    }

    public function isAuthenticated() { return $this->has('is_authenticated') && $this->get('is_authenticated'); }
    public function updateActivity() { $this->set('last_activity', time()); }
    public function isExpired() { $last = $this->get('last_activity', 0); return (time() - $last) > $this->sessionLifetime; }
}
?>
