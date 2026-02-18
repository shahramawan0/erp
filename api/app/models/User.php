<?php
require_once __DIR__ . '/BaseModel.php';

class User extends BaseModel {
    protected $table = 'users';

    public function authenticate($username, $password) {
        $sql = "SELECT u.*, r.name as role_name, r.status as role_status, r.permissions,
                un.id as unit_id, un.name as unit_name, un.name_in_urdu as unit_name_urdu, un.short_name as unit_short_name,
                s.id as site_id, s.name as site_name,
                d.id as department_id, d.name as department_name
                FROM users u
                LEFT JOIN roles r ON u.role_id = r.id
                LEFT JOIN units un ON u.unit_id = un.id
                LEFT JOIN sites s ON u.site_id = s.id
                LEFT JOIN departments d ON u.department_id = d.id
                WHERE u.email = ? AND u.status = 'active'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password_hash'])) {
            unset($user['password_hash']);
            return $user;
        }
        return false;
    }

    public function updateLoginToken($userId, $token, $expires) {
        $sql = "UPDATE users SET last_login = NOW(), login_token = ?, login_token_expires = ? WHERE id = ?";
        return $this->db->prepare($sql)->execute([$token, $expires, $userId]);
    }

    public function validateLoginToken($token) {
        $sql = "SELECT u.*, r.name as role_name, r.status as role_status,
                un.id as unit_id, un.name as unit_name, un.name_in_urdu as unit_name_urdu, un.short_name as unit_short_name
                FROM users u LEFT JOIN roles r ON u.role_id = r.id LEFT JOIN units un ON u.unit_id = un.id
                WHERE u.login_token = ? AND u.login_token_expires > NOW() AND u.status = 'active'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            unset($user['password_hash'], $user['login_token']);
            return $user;
        }
        return false;
    }

    public function getTokenExpiration($token) {
        $stmt = $this->db->prepare("SELECT login_token_expires FROM users WHERE login_token = ? AND status = 'active'");
        $stmt->execute([$token]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return $r ? $r['login_token_expires'] : false;
    }

    public function getUserWithRole($userId) {
        $sql = "SELECT u.*, r.name as role_name FROM users u LEFT JOIN roles r ON u.role_id = r.id WHERE u.id = ? AND u.is_deleted = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
