<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/ControlHead.php';
require_once __DIR__ . '/../models/MainHead.php';

class ControlHeadController extends BaseController {
    private $model;

    public function __construct() {
        parent::__construct();
        $this->model = new ControlHead();
    }

    protected function isPublicEndpoint() { return true; }

    public function index() {
        try {
            $companyId = $this->getCurrentUser() ? $this->getCurrentUser()['company_id'] : 1;
            $filters = ['type' => $_GET['type'] ?? 'item', 'status' => $_GET['status'] ?? 'I', 'main_head_id' => $_GET['main_head_id'] ?? null];
            $result = $this->model->searchForCompanyWithMainHead($companyId, $filters, $_GET['page'] ?? 1, $_GET['limit'] ?? 10, $_GET['search'] ?? '');
            $this->sendSuccess($result);
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function show() {
        try {
            $id = $this->getRequestId();
            $companyId = $this->getCurrentUser() ? $this->getCurrentUser()['company_id'] : 1;
            $row = $this->model->getByIdAndCompany($id, $companyId);
            if (!$row) { $this->sendError('Not found', 404); return; }
            $this->sendSuccess($row);
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function store() {
        try {
            $data = $this->getRequestData();
            $errors = $this->validateRequired($data, ['name', 'name_in_urdu']);
            if (!empty($errors)) { $this->sendError('Validation failed', 400, $errors); return; }
            $companyId = $this->getCurrentUser() ? $this->getCurrentUser()['company_id'] : 1;
            $data['type'] = $data['type'] ?? 'item';
            $data['status'] = $data['status'] ?? 'I';
            $id = $this->model->createForCompany($data, $companyId);
            $this->sendSuccess($this->model->getByIdAndCompany($id, $companyId), 201);
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function update() {
        try {
            $id = $this->getRequestId();
            $data = $this->getRequestData();
            $companyId = $this->getCurrentUser() ? $this->getCurrentUser()['company_id'] : 1;
            $this->model->updateForCompany($id, $data, $companyId);
            $this->sendSuccess($this->model->getByIdAndCompany($id, $companyId));
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function destroy() {
        try {
            $id = $this->getRequestId();
            $companyId = $this->getCurrentUser() ? $this->getCurrentUser()['company_id'] : 1;
            $this->model->softDeleteForCompany($id, $companyId);
            $this->sendSuccess(['message' => 'Deleted']);
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    /** Return main heads as array for dropdown (control-heads/main-heads sub-route) */
    public function getMainHeads() {
        try {
            $companyId = $this->getCurrentUser() ? $this->getCurrentUser()['company_id'] : 1;
            $type = $_GET['type'] ?? 'item';
            $status = $_GET['status'] ?? 'I';
            $mainHeadModel = new MainHead();
            $rows = $mainHeadModel->getNamesByTypeForCompany($companyId, $type, $status);
            $this->sendSuccess($rows);
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }
}
?>
