<?php
/**
 * Simple Permission Helper for KMI
 * Just handles basic permission checking - no complex mappings
 */

class PermissionHelper {
    private static $instance = null;
    private $userPermissions = null;
    private $userRole = null;

    private function __construct() {
        $this->loadUserPermissions();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function loadUserPermissions() {
        if (isset($_SESSION['user_id'])) {
            $this->loadPermissionsFromDatabase($_SESSION['user_id']);
        }
    }

    private function loadPermissionsFromDatabase($userId) {
        try {
            require_once dirname(__DIR__) . '/config.php';
            $db = new PDO(
                "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4",
                $dbUser, $dbPass
            );
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Get user's role permissions
            $stmt = $db->prepare("
                SELECT r.permissions, r.name as role_name
                FROM users u
                JOIN roles r ON u.role_id = r.id
                WHERE u.id = ? AND u.is_deleted = 0 AND r.is_deleted = 0
            ");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $this->userRole = $result['role_name'];
                $this->userPermissions = json_decode($result['permissions'], true);
                $_SESSION['role_permissions'] = $this->userPermissions;
                $_SESSION['role_name'] = $this->userRole;
            }
        } catch (PDOException $e) {
            if (isset($_SESSION['role_permissions'])) {
                $this->userPermissions = $_SESSION['role_permissions'];
            }
        }
    }

    /**
     * Check if user has permission for a module.action
     * Usage: hasPermission('unit', 'view') or hasPermission('unit.*')
     */
    public function hasPermission($module, $action = null) {
        if ($this->userRole === 'Super Admin') {
            return true;
        }

        if (!$this->userPermissions) {
            return false;
        }

        if ($action === null) {
            // Check if user has any permission for this module
            foreach ($this->userPermissions as $permission) {
                if (strpos($permission, $module . '.') === 0) {
                    return true;
                }
            }
            return false;
        }

        // Check specific permission
        $permission = $module . '.' . $action;
        return in_array($permission, $this->userPermissions);
    }

    /**
     * Check if user can access a page (by checking if they have any permission for the page's main module)
     */
    public function canAccessPage($pageName) {
        // Extract module name from page name
        $module = $this->getModuleFromPage($pageName);
        return $this->hasPermission($module);
    }

    private function getModuleFromPage($pageName) {
        // Simple mapping - you can modify this as needed
        $pageName = str_replace('.php', '', $pageName);
        
        // Convert page name to module name
        $module = str_replace('_', ' ', $pageName);
        $module = strtolower($module);
        
        // Special case for misc_entries -> misc entries (keep the 's')
        if ($pageName === 'misc_entries') {
            return 'misc entries';
        }
        
        // Remove 's' at end if plural (but not for special cases)
        if (substr($module, -1) === 's' && strlen($module) > 1) {
            $module = substr($module, 0, -1);
        }
        
        return $module;
    }

    public function getUserRole() { 
        return $this->userRole; 
    }

    public function getAllPermissions() { 
        return $this->userPermissions; 
    }
}

// ========================================
// SUPER SIMPLE HELPER FUNCTIONS
// ========================================

/**
 * Check if user has permission for module.action
 * Usage: if (hasPermission('unit', 'view')) { ... }
 */
function hasPermission($module, $action = null) {
    return PermissionHelper::getInstance()->hasPermission($module, $action);
}

/**
 * Check if user can access a page
 * Usage: if (canAccess('misc_entries.php')) { ... }
 */
function canAccess($pageName) {
    return PermissionHelper::getInstance()->canAccessPage($pageName);
}

/**
 * Require access to page - redirect if no access
 * Usage: requireAccess('misc_entries.php');
 */
function requireAccess($pageName) {
    if (!canAccess($pageName)) {
        $_SESSION['error_message'] = 'You do not have permission to access this page.';
        header("Location: index.php");
        exit;
    }
}

/**
 * Show content only if user has permission
 * Usage: showIf('unit', '<button>Create Unit</button>');
 */
function showIf($module, $content) {
    if (hasPermission($module)) {
        echo $content;
    }
}

/**
 * Show menu item only if user has access to page
 * Usage: showMenu('misc_entries.php', 'Misc Entries', 'misc_entries.php');
 */
function showMenu($pageName, $menuText, $link) {
    if (canAccess($pageName)) {
        echo "<li><a href='$link'>$menuText</a></li>";
    }
}
?>
