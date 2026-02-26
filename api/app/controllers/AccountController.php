<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Account.php';

class AccountController extends BaseController {
    private $accountModel;

    public function __construct() {
        parent::__construct();
        $this->accountModel = new Account();
    }

    protected function isPublicEndpoint() { return true; }

    public function index() {
        try {
            $companyId = $this->getCurrentUser() ? $this->getCurrentUser()['company_id'] : 1;
            $page = (int)($_GET['page'] ?? 1);
            $limit = (int)($_GET['limit'] ?? 10);
            $search = $_GET['search'] ?? '';
            $filters = [
                'main_head_id' => $_GET['main_head_id'] ?? null,
                'control_head_id' => $_GET['control_head_id'] ?? null,
                'account_type' => $_GET['account_type'] ?? null
            ];
            $result = $this->accountModel->searchForCompany($companyId, $filters, $page, $limit, $search);
            $this->sendSuccess($result);
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function show() {
        try {
            $id = $this->getRequestId();
            $companyId = $this->getCurrentUser() ? $this->getCurrentUser()['company_id'] : 1;
            $row = $this->accountModel->getByIdAndCompany($id, $companyId);
            if (!$row) {
                $this->sendError('Account not found', 404);
                return;
            }
            $this->sendSuccess($row);
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function store() {
        try {
            $data = $this->getRequestData();
            $errors = $this->validateRequired($data, ['name', 'main_head_id', 'control_head_id']);
            if (!empty($errors)) {
                $this->sendError('Validation failed', 400, $errors);
                return;
            }
            $companyId = $this->getCurrentUser() ? $this->getCurrentUser()['company_id'] : 1;
            $allowed = ['main_head_id','control_head_id','account_type','name','name_in_urdu','description','address','cell','ptcl','city_id','company_name','company_address','ntn','stn','bank_id','company_type_id','payment_term_id','opening_balance','code','source_id'];
            $payload = ['company_id' => $companyId];
            foreach ($allowed as $k) {
                if (array_key_exists($k, $data)) {
                    $v = $data[$k];
                    if ($v === '' || $v === null) {
                        if (in_array($k, ['main_head_id','control_head_id','name'])) continue;
                        $payload[$k] = null;
                    } else {
                        $payload[$k] = in_array($k, ['main_head_id','control_head_id','city_id','bank_id','company_type_id','payment_term_id','source_id']) ? (int)$v : $v;
                    }
                }
            }
            if (isset($data['opening_balance']) && $data['opening_balance'] !== '' && $data['opening_balance'] !== null) {
                $payload['opening_balance'] = (float)$data['opening_balance'];
            }
            $payload['status'] = $data['status'] ?? 'A';
            $result = $this->accountModel->createForCompany($payload, $companyId);
            if ($result) {
                $this->sendSuccess($result, 201);
            } else {
                $this->sendError('Failed to create account', 500);
            }
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function update() {
        try {
            $id = $this->getRequestId();
            $data = $this->getRequestData();
            $companyId = $this->getCurrentUser() ? $this->getCurrentUser()['company_id'] : 1;
            $existing = $this->accountModel->getByIdAndCompany($id, $companyId);
            if (!$existing) {
                $this->sendError('Account not found', 404);
                return;
            }
            $errors = $this->validateRequired($data, ['name', 'main_head_id', 'control_head_id']);
            if (!empty($errors)) {
                $this->sendError('Validation failed', 400, $errors);
                return;
            }
            $allowed = ['main_head_id','control_head_id','account_type','name','name_in_urdu','description','address','cell','ptcl','city_id','company_name','company_address','ntn','stn','bank_id','company_type_id','payment_term_id','opening_balance','code'];
            $payload = [];
            foreach ($allowed as $k) {
                if (array_key_exists($k, $data)) {
                    $v = $data[$k];
                    if ($v === '' || $v === null) {
                        $payload[$k] = null;
                    } else {
                        $payload[$k] = in_array($k, ['main_head_id','control_head_id','city_id','bank_id','company_type_id','payment_term_id']) ? (int)$v : $v;
                    }
                }
            }
            if (array_key_exists('opening_balance', $data)) {
                $payload['opening_balance'] = ($data['opening_balance'] === '' || $data['opening_balance'] === null) ? null : (float)$data['opening_balance'];
            }
            $result = $this->accountModel->updateForCompany($id, $payload, $companyId);
            if ($result) {
                $this->sendSuccess($result);
            } else {
                $this->sendError('Failed to update account', 500);
            }
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function destroy() {
        try {
            $id = $this->getRequestId();
            $companyId = $this->getCurrentUser() ? $this->getCurrentUser()['company_id'] : 1;
            $ok = $this->accountModel->softDeleteForCompany($id, $companyId);
            if ($ok) {
                $this->sendSuccess(['message' => 'Account deleted']);
            } else {
                $this->sendError('Account not found or already deleted', 404);
            }
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function getMainHeads() {
        try {
            $companyId = $this->getCurrentUser() ? $this->getCurrentUser()['company_id'] : 1;
            $rows = $this->accountModel->getMainHeadsForCompany($companyId);
            $this->sendSuccess($rows);
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function getControlHeads() {
        try {
            $mainHeadId = $_GET['main_head_id'] ?? null;
            if (!$mainHeadId) {
                $this->sendError('main_head_id required', 400);
                return;
            }
            $companyId = $this->getCurrentUser() ? $this->getCurrentUser()['company_id'] : 1;
            $rows = $this->accountModel->getControlHeadsByMainHead($mainHeadId, $companyId);
            $this->sendSuccess($rows);
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }
}
