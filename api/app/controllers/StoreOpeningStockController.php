<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/StoreOpeningStock.php';

class StoreOpeningStockController extends BaseController
{
    private $storeOpeningStockModel;

    public function __construct()
    {
        parent::__construct();
        $this->storeOpeningStockModel = new StoreOpeningStock();
    }

    /**
     * Override to allow public access for testing
     */
    protected function isPublicEndpoint()
    {
        return true;
    }

    /**
     * Get the next voucher number for store opening stock
     */
    public function getNextVoucherNumber()
    {
        try {
            $nextVoucher = $this->storeOpeningStockModel->getNextVoucherNumber();
            $this->sendSuccess(['voucher_no' => $nextVoucher]);
        } catch (Exception $e) {
            error_log("StoreOpeningStockController::getNextVoucherNumber() - Error: " . $e->getMessage());
            $this->sendError($e->getMessage());
        }
    }

    /**
     * Get units list for dropdown
     */
    public function getUnits()
    {
        try {
            $units = $this->storeOpeningStockModel->getUnits();
            $this->sendSuccess(['units' => $units]);
        } catch (Exception $e) {
            error_log("StoreOpeningStockController::getUnits() - Error: " . $e->getMessage());
            $this->sendError($e->getMessage());
        }
    }

    /**
     * Get items list for dropdown (limited to 50 items)
     */
    public function getItems()
    {
        try {
            // Get unit_id from query parameter or from current user
            $unitId = $_GET['unit_id'] ?? null;
            
            // If no unit_id in query, try to get from current user
            if (!$unitId) {
                $currentUser = $this->getCurrentUser();
                if ($currentUser && isset($currentUser['unit_id']) && $currentUser['unit_id']) {
                    $unitId = $currentUser['unit_id'];
                }
            }
            
            $items = $this->storeOpeningStockModel->getItems(50, $unitId);
            $this->sendSuccess(['items' => $items]);
        } catch (Exception $e) {
            error_log("StoreOpeningStockController::getItems() - Error: " . $e->getMessage());
            $this->sendError($e->getMessage());
        }
    }

    /**
     * Search items by ID or name
     */
    public function searchItems()
    {
        try {
            $search = $_GET['search'] ?? '';
            if (empty($search)) {
                $this->sendError('Search term is required');
                return;
            }
            
            // Get unit_id from query parameter or from current user
            $unitId = $_GET['unit_id'] ?? null;
            
            // If no unit_id in query, try to get from current user
            if (!$unitId) {
                $currentUser = $this->getCurrentUser();
                if ($currentUser && isset($currentUser['unit_id']) && $currentUser['unit_id']) {
                    $unitId = $currentUser['unit_id'];
                }
            }
            
            $items = $this->storeOpeningStockModel->searchItems($search, $unitId);
            $this->sendSuccess(['items' => $items]);
        } catch (Exception $e) {
            error_log("StoreOpeningStockController::searchItems() - Error: " . $e->getMessage());
            $this->sendError($e->getMessage());
        }
    }

    /**
     * Get racks list for dropdown filtered by unit_id
     */
    public function getRacks()
    {
        try {
            // Get unit_id from query parameter or from current user
            $unitId = $_GET['unit_id'] ?? null;
            
            // If no unit_id in query, try to get from current user
            if (!$unitId) {
                $currentUser = $this->getCurrentUser();
                if ($currentUser && isset($currentUser['unit_id']) && $currentUser['unit_id']) {
                    $unitId = $currentUser['unit_id'];
                }
            }
            
            $racks = $this->storeOpeningStockModel->getRacks($unitId);
            $this->sendSuccess(['racks' => $racks]);
        } catch (Exception $e) {
            error_log("StoreOpeningStockController::getRacks() - Error: " . $e->getMessage());
            $this->sendError($e->getMessage());
        }
    }

    /**
     * Check if item with rack already exists in opening stock
     */
    public function checkExisting()
    {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($input['item_id']) || !isset($input['rack_id'])) {
                $this->sendError('Item ID and Rack ID are required');
                return;
            }

            $result = $this->storeOpeningStockModel->checkExistingOpeningStock($input['item_id'], $input['rack_id']);
            
            if ($result) {
                $this->sendSuccess([
                    'exists' => true,
                    'voucher_no' => $result['voucher_no']
                ]);
            } else {
                $this->sendSuccess([
                    'exists' => false
                ]);
            }
            
        } catch (Exception $e) {
            error_log("StoreOpeningStockController::checkExisting() - Error: " . $e->getMessage());
            $this->sendError($e->getMessage());
        }
    }

    /**
     * Create new store opening stock entry
     */
    public function create()
    {
        try {
            
            // Get JSON input
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                $this->sendError('Invalid input data');
                return;
            }
                        
            // Validate required fields (voucher_no is auto-generated by backend, not required from frontend)
            $requiredFields = ['voucher_date', 'unit_id', 'items'];
            foreach ($requiredFields as $field) {
                if (!isset($input[$field]) || empty($input[$field])) {
                    $this->sendError("Missing required field: {$field}");
                    return;
                }
            }
            
            if (!is_array($input['items']) || empty($input['items'])) {
                $this->sendError('No items provided for store opening stock');
                return;
            }
            
            // Create the store opening stock entry
            $result = $this->storeOpeningStockModel->create($input);
            
            if ($result) {
                $this->sendSuccess([
                    'id' => $result['id'],
                    'voucher_no' => $result['voucher_no'],
                    'message' => 'Store opening stock created successfully with voucher #' . $result['voucher_no']
                ]);
            } else {
                $this->sendError('Failed to create store opening stock');
            }
            
        } catch (Exception $e) {
            $this->sendError($e->getMessage());
        }
    }

    /**
     * Get store opening stock listing with filtering
     */
    public function getListing()
    {
        try {
            $filters = [
                'unit_id' => $_GET['unit_id'] ?? null,
                'date' => $_GET['date'] ?? null,
                'search' => $_GET['search'] ?? null
            ];
            
            $page = (int)($_GET['page'] ?? 1);
            $limit = (int)($_GET['limit'] ?? 10);
            
            // Get current user and apply default unit filter if available
            $currentUser = $this->getCurrentUser();
            if ($currentUser && isset($currentUser['unit_id']) && $currentUser['unit_id']) {
                $filters['unit_id'] = $currentUser['unit_id'];
                error_log("StoreOpeningStockController::getListing() - Using default unit_id: " . $currentUser['unit_id']);
            }
            
            // Debug: Log the request parameters
            error_log("StoreOpeningStockController::getListing() - GET params: " . json_encode($_GET));
            error_log("StoreOpeningStockController::getListing() - Filters: " . json_encode($filters));
            error_log("StoreOpeningStockController::getListing() - Page: $page, Limit: $limit");
            
            $result = $this->storeOpeningStockModel->getListing($filters, $page, $limit);
            $this->sendSuccess($result);
            
        } catch (Exception $e) {
            error_log("StoreOpeningStockController::getListing() - Error: " . $e->getMessage());
            $this->sendError($e->getMessage());
        }
    }

    /**
     * Get voucher details for editing
     */
    public function getVoucherDetails()
    {
        try {
            $voucherNo = $_GET['voucher_no'] ?? null;
            error_log("StoreOpeningStockController::getVoucherDetails() - Received voucher_no: " . ($voucherNo ?? 'NULL'));
            error_log("StoreOpeningStockController::getVoucherDetails() - GET parameters: " . json_encode($_GET));
            
            if (empty($voucherNo)) {
                $this->sendError('Voucher number is required');
                return;
            }
            
            $result = $this->storeOpeningStockModel->getVoucherDetails($voucherNo);
            $this->sendSuccess($result);
            
        } catch (Exception $e) {
            error_log("StoreOpeningStockController::getVoucherDetails() - Error: " . $e->getMessage());
            $this->sendError($e->getMessage());
        }
    }

    /**
     * Update existing store opening stock entry
     */
    public function update()
    {
        try {
            // Get JSON input
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                $this->sendError('Invalid input data');
                return;
            }
                        
            // Validate required fields
            $requiredFields = ['id', 'unit_id', 'item_id', 'rack_id', 'qty'];
            foreach ($requiredFields as $field) {
                if (!isset($input[$field]) || empty($input[$field])) {
                    $this->sendError("Missing required field: {$field}");
                    return;
                }
            }
            
            // Update the store opening stock entry
            $id = $input['id'];
            unset($input['id']); // Remove ID from data array
            $result = $this->storeOpeningStockModel->update($id, $input);
            
            if ($result) {
                $this->sendSuccess(['message' => 'Store opening stock updated successfully']);
            } else {
                $this->sendError('Failed to update store opening stock');
            }
            
        } catch (Exception $e) {
            $this->sendError($e->getMessage());
        }
    }

    /**
     * Update entire voucher with all its items
     */
    public function updateVoucher()
    {
        try {
            // Get JSON input
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                $this->sendError('Invalid input data');
                return;
            }
            
            // Debug: Log the received data
            error_log("StoreOpeningStockController::updateVoucher() - Received data: " . json_encode($input));
                        
            // Validate required fields
            $requiredFields = ['voucher_no', 'voucher_date', 'unit_id', 'items'];
            foreach ($requiredFields as $field) {
                if (!isset($input[$field]) || empty($input[$field])) {
                    $this->sendError("Missing required field: {$field}");
                    return;
                }
            }
            
            // Debug: Log items array
            error_log("StoreOpeningStockController::updateVoucher() - Items count: " . count($input['items']));
            foreach ($input['items'] as $index => $item) {
                error_log("StoreOpeningStockController::updateVoucher() - Item {$index}: " . json_encode($item));
            }
            
            // Update the entire voucher
            $result = $this->storeOpeningStockModel->updateVoucher($input);
            
            if ($result) {
                $this->sendSuccess(['message' => 'Voucher updated successfully']);
            } else {
                $this->sendError('Failed to update voucher');
            }
            
        } catch (Exception $e) {
            error_log("StoreOpeningStockController::updateVoucher() - Error: " . $e->getMessage());
            $this->sendError($e->getMessage());
        }
    }

    /**
     * Delete entire voucher with all its items
     */
    public function deleteVoucher()
    {
        try {
            // Get JSON input
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                $this->sendError('Invalid input data');
                return;
            }
            
            // Validate required fields
            if (!isset($input['voucher_no']) || empty($input['voucher_no'])) {
                $this->sendError('Missing required field: voucher_no');
                return;
            }
            
            $voucherNo = $input['voucher_no'];
            
            // Delete the entire voucher
            $result = $this->storeOpeningStockModel->deleteVoucher($voucherNo);
            
            if ($result) {
                $this->sendSuccess(['message' => 'Voucher deleted successfully']);
            } else {
                $this->sendError('Failed to delete voucher');
            }
            
        } catch (Exception $e) {
            error_log("StoreOpeningStockController::deleteVoucher() - Error: " . $e->getMessage());
            $this->sendError($e->getMessage());
        }
    }

    /**
     * Default index method
     */
    public function index()
    {
        $this->sendError('Method not allowed');
    }
}
?>
