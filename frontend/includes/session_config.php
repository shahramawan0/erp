<?php
/**
 * Session Configuration for Frontend
 * Ensures consistent session handling between API and frontend
 */

// Include the permission helper
require_once __DIR__ . '/PermissionHelper.php';

// Set session configuration only if headers haven't been sent and session hasn't started
if (!headers_sent() && session_status() === PHP_SESSION_NONE) {
    // Use default session save path to ensure sharing between API and frontend
    ini_set('session.gc_maxlifetime', 86400); // 24 hours
    ini_set('session.cookie_lifetime', 86400);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.use_cookies', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 1 : 0);
    ini_set('session.cookie_samesite', 'Lax');
    
    // Set session cookie path to root to ensure sharing between API and frontend
    ini_set('session.cookie_path', '/');
    
    // Use default session save path
    ini_set('session.save_handler', 'files');
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is authenticated
 * @return bool
 */
function isAuthenticated() {
    return isset($_SESSION['is_authenticated']) && $_SESSION['is_authenticated'];
}

/**
 * Get current user data
 * @return array|null
 */
function getCurrentUser() {
    if (!isAuthenticated()) {
        return null;
    }
    
    return [
        'user_id' => $_SESSION['user_id'] ?? null,
        'username' => $_SESSION['username'] ?? null,
        'email' => $_SESSION['email'] ?? null,
        'role_name' => $_SESSION['role_name'] ?? null,
        'role_status' => $_SESSION['role_status'] ?? null,
        'company_id' => $_SESSION['company_id'] ?? null,
        'first_name' => $_SESSION['first_name'] ?? null,
        'last_name' => $_SESSION['last_name'] ?? null,
        'unit_id' => $_SESSION['unit_id'] ?? null,
        'unit_name' => $_SESSION['unit_name'] ?? null,
        'unit_name_urdu' => $_SESSION['unit_name_urdu'] ?? null,
        'unit_short_name' => $_SESSION['unit_short_name'] ?? null,
        'site_id' => $_SESSION['site_id'] ?? null,
        'site_name' => $_SESSION['site_name'] ?? null,
        'department_id' => $_SESSION['department_id'] ?? null,
        'department_name' => $_SESSION['department_name'] ?? null,
        'profile_picture' => $_SESSION['profile_picture'] ?? null,
        'dtype' => $_SESSION['dtype'] ?? null,
        'login_time' => $_SESSION['login_time'] ?? null
    ];
}

/**
 * Redirect to login if not authenticated
 */
function requireAuth() {
    if (!isAuthenticated()) {
        header('Location: sign-in.php');
        exit;
    }
}

/**
 * Logout user
 */
function logout() {
    session_unset();
    session_destroy();
    header('Location: sign-in.php');
    exit;
}
?> 