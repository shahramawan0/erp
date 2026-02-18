<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

require_once __DIR__ . '/../app/controllers/ItemController.php';

class ItemRouter {
    private $controller;

    public function __construct() {
        $this->controller = new ItemController();
    }

    private function getPathParts() {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $parts = array_values(array_filter(explode('/', trim($path, '/'))));
        if (isset($parts[0]) && $parts[0] === 'khawaja_traders') array_shift($parts);
        if (isset($parts[0]) && $parts[0] === 'api') array_shift($parts);
        if (isset($parts[0]) && $parts[0] === 'items') array_shift($parts);
        return $parts;
    }

    public function route() {
        $parts = $this->getPathParts();
        $method = $_SERVER['REQUEST_METHOD'];

        switch ($method) {
            case 'GET':
                if (empty($parts)) {
                    $this->controller->index();
                } elseif (is_numeric($parts[0])) {
                    if (isset($parts[1]) && $parts[1] === 'rack-assignment') {
                        $this->controller->getRackAssignment();
                    } else {
                        $this->controller->show();
                    }
                } elseif ($parts[0] === 'main-heads') {
                    $this->controller->getMainHeads();
                } elseif ($parts[0] === 'control-heads') {
                    $this->controller->getControlHeads();
                } elseif ($parts[0] === 'categories') {
                    $this->controller->getCategories();
                } elseif ($parts[0] === 'groups') {
                    $this->controller->getGroups();
                } elseif ($parts[0] === 'sub-groups') {
                    $this->controller->getSubGroups();
                } elseif ($parts[0] === 'attributes') {
                    $this->controller->getAttributes();
                } else {
                    $this->sendErr('Invalid endpoint', 404);
                }
                break;
            case 'POST':
                if (empty($parts)) {
                    $this->controller->store();
                } elseif ($parts[0] === 'generate-sku') {
                    $this->controller->generateSku();
                } elseif ($parts[0] === 'categories') {
                    $this->controller->storeCategory();
                } elseif ($parts[0] === 'groups') {
                    $this->controller->storeGroup();
                } elseif ($parts[0] === 'sub-groups') {
                    $this->controller->storeSubGroup();
                } elseif ($parts[0] === 'attributes') {
                    $this->controller->storeAttribute();
                } else {
                    $this->sendErr('Invalid endpoint', 404);
                }
                break;
            case 'PUT':
                if (is_numeric($parts[0])) {
                    if (isset($parts[1]) && $parts[1] === 'rack-assignment') {
                        $this->controller->updateRackAssignment();
                    } else {
                        $this->controller->update();
                    }
                } elseif (isset($parts[0]) && isset($parts[1]) && is_numeric($parts[1])) {
                    if ($parts[0] === 'categories') $this->controller->updateCategory();
                    elseif ($parts[0] === 'groups') $this->controller->updateGroup();
                    elseif ($parts[0] === 'sub-groups') $this->controller->updateSubGroup();
                    elseif ($parts[0] === 'attributes') $this->controller->updateAttribute();
                    else $this->sendErr('Invalid endpoint', 404);
                } else {
                    $this->sendErr('Invalid endpoint', 404);
                }
                break;
            case 'DELETE':
                if (is_numeric($parts[0])) {
                    $this->controller->destroy();
                } elseif (isset($parts[0]) && isset($parts[1]) && is_numeric($parts[1])) {
                    if ($parts[0] === 'categories') $this->controller->destroyCategory();
                    elseif ($parts[0] === 'groups') $this->controller->destroyGroup();
                    elseif ($parts[0] === 'sub-groups') $this->controller->destroySubGroup();
                    elseif ($parts[0] === 'attributes') $this->controller->destroyAttribute();
                    else $this->sendErr('Invalid endpoint', 404);
                } else {
                    $this->sendErr('Invalid endpoint', 404);
                }
                break;
            default:
                $this->sendErr('Method not allowed', 405);
        }
    }

    private function sendErr($msg, $code) {
        http_response_code($code);
        echo json_encode(['success' => false, 'error' => $msg]);
    }
}

$router = new ItemRouter();
$router->route();
