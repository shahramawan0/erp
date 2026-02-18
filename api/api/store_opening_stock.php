<?php

require_once __DIR__ . '/../app/config/Database.php';
require_once __DIR__ . '/../app/controllers/StoreOpeningStockController.php';

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

class StoreOpeningStockRouter {
    private $controller;
    
    public function __construct() {
        $this->controller = new StoreOpeningStockController();
    }
    
    public function route() {
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $pathParts = explode('/', trim($path, '/'));
        
        // Remove project name if present
        if (isset($pathParts[0]) && $pathParts[0] === 'khawaja_traders') {
            array_shift($pathParts);
        }
        
        // Remove 'api' if present
        if (isset($pathParts[0]) && $pathParts[0] === 'api') {
            array_shift($pathParts);
        }
        
        // Remove 'api' again if present (for nested api folder)
        if (isset($pathParts[0]) && $pathParts[0] === 'api') {
            array_shift($pathParts);
        }
        
        // Remove 'store_opening_stock.php' if present
        if (isset($pathParts[0]) && $pathParts[0] === 'store_opening_stock.php') {
            array_shift($pathParts);
        }
        
        // Remove endpoint name if present
        if (isset($pathParts[0]) && $pathParts[0] === 'store-opening-stock') {
            array_shift($pathParts);
        }
        
        $method = $_SERVER['REQUEST_METHOD'];
        $action = $pathParts[0] ?? '';
        
        error_log("Store Opening Stock API - Method: {$method}, Action: {$action}, Path: " . json_encode($pathParts));
        error_log("Store Opening Stock API - Full URI: " . $_SERVER['REQUEST_URI']);
        error_log("Store Opening Stock API - Path parts after processing: " . json_encode($pathParts));
        
        try {
            switch ($method) {
                case 'GET':
                    if ($action === 'next-voucher') {
                        $this->controller->getNextVoucherNumber();
                    } elseif ($action === 'units') {
                        $this->controller->getUnits();
                    } elseif ($action === 'items') {
                        $this->controller->getItems();
                    } elseif ($action === 'search-items') {
                        $this->controller->searchItems();
                    } elseif ($action === 'racks') {
                        $this->controller->getRacks();
                    } elseif ($action === 'listing') {
                        $this->controller->getListing();
                    } elseif ($action === 'voucher') {
                        // Extract voucher number from path and pass it to controller
                        $voucherNo = $pathParts[1] ?? null;
                        if ($voucherNo) {
                            // Set voucher_no in GET parameters so controller can access it
                            $_GET['voucher_no'] = $voucherNo;
                        }
                        $this->controller->getVoucherDetails();
                    } else {
                        // Default: get next voucher number
                        $this->controller->getNextVoucherNumber();
                    }
                    break;
                
                case 'POST':
                    if ($action === 'create') {
                        $this->controller->create();
                    } elseif ($action === 'check-existing') {
                        $this->controller->checkExisting();
                    } else {
                        // Default: create new opening stock entry
                        $this->controller->create();
                    }
                    break;
                
                case 'PUT':
                    if ($action === 'update') {
                        $this->controller->update();
                    } elseif ($action === 'update-voucher') {
                        $this->controller->updateVoucher();
                    } else {
                        $this->sendError('Method not allowed', 405);
                    }
                    break;
                
                case 'DELETE':
                    if ($action === 'delete-voucher') {
                        $this->controller->deleteVoucher();
                    } else {
                        $this->sendError('Method not allowed', 405);
                    }
                    break;
                    
                default:
                    $this->sendError('Method not allowed', 405);
                    break;
            }
        } catch (Exception $e) {
            error_log("Store Opening Stock API - Error: " . $e->getMessage());
            $this->sendError('Internal server error: ' . $e->getMessage(), 500);
        }
    }
    
    private function sendError($message, $statusCode = 400) {
        http_response_code($statusCode);
        echo json_encode([
            'success' => false, 
            'error' => $message, 
            'timestamp' => date('Y-m-d H:i:s')
        ]);
    }
}

$router = new StoreOpeningStockRouter();
$router->route();
?>
