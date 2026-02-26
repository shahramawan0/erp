<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Item.php';
require_once __DIR__ . '/../models/MainHead.php';
require_once __DIR__ . '/../models/ControlHead.php';
require_once __DIR__ . '/../models/ItemRackAssignment.php';
require_once __DIR__ . '/../models/ItemCategory.php';
require_once __DIR__ . '/../models/ItemGroup.php';
require_once __DIR__ . '/../models/ItemSubGroup.php';
require_once __DIR__ . '/../models/ItemAttribute.php';
require_once __DIR__ . '/../models/ItemAttributeValue.php';
require_once __DIR__ . '/../models/ItemSkuSequence.php';

class ItemController extends BaseController {
    private $itemModel, $mainHeadModel, $controlHeadModel, $itemRackAssignmentModel;
    private $itemCategoryModel, $itemGroupModel, $itemSubGroupModel, $itemAttributeModel, $itemAttributeValueModel, $itemSkuSequenceModel;

    public function __construct() {
        parent::__construct();
        $this->itemModel = new Item();
        $this->mainHeadModel = new MainHead();
        $this->controlHeadModel = new ControlHead();
        $this->itemRackAssignmentModel = new ItemRackAssignment();
        $this->itemCategoryModel = new ItemCategory();
        $this->itemGroupModel = new ItemGroup();
        $this->itemSubGroupModel = new ItemSubGroup();
        $this->itemAttributeModel = new ItemAttribute();
        $this->itemAttributeValueModel = new ItemAttributeValue();
        $this->itemSkuSequenceModel = new ItemSkuSequence();
    }

    protected function isPublicEndpoint() { return true; }

    /**
     * Validate and sanitize purchase_rate and sale_rate. Mutates $data. Returns list of errors.
     */
    private function validateAndSanitizeRates(array &$data) {
        $errors = [];
        foreach (['purchase_rate' => 'Purchase rate', 'sale_rate' => 'Sale rate'] as $key => $label) {
            if (!array_key_exists($key, $data)) continue;
            $v = $data[$key];
            if ($v === null || $v === '' || (is_string($v) && trim($v) === '')) {
                $data[$key] = null;
                continue;
            }
            if (!is_numeric($v) || (float)$v < 0) {
                $errors[] = "$label must be a non-negative number";
                continue;
            }
            $data[$key] = round((float)$v, 4);
        }
        return $errors;
    }

    public function index() {
        try {
            $page = $_GET['page'] ?? 1;
            $limit = $_GET['limit'] ?? 10;
            $searchTerm = $_GET['search'] ?? '';
            $includeAttributes = isset($_GET['include_attributes']) && $_GET['include_attributes'] === '1';
            $filters = [
                'main_head_id' => $_GET['main_head_id'] ?? null,
                'control_head_id' => $_GET['control_head_id'] ?? null,
                'category_id' => $_GET['category_id'] ?? null,
                'group_id' => $_GET['group_id'] ?? null,
                'sub_group_id' => $_GET['sub_group_id'] ?? null,
                'unit_type_id' => $_GET['unit_type_id'] ?? null,
                'status' => $_GET['status'] ?? 'I',
            ];
            $companyId = $this->getCurrentUser() ? $this->getCurrentUser()['company_id'] : 1;
            $unitId = $this->getCurrentUser() ? $this->getCurrentUser()['unit_id'] : null;
            $result = $this->itemModel->searchWithDetailsForCompanyAndUnit($companyId, $unitId, $filters, $page, $limit, $searchTerm);
            if ($includeAttributes && !empty($result['records'])) {
                $itemIds = array_column($result['records'], 'id');
                $allAttr = $this->itemAttributeValueModel->getByItemIds($itemIds);
                $byItem = [];
                foreach ($allAttr as $av) {
                    $byItem[$av['item_id']][] = $av;
                }
                foreach ($result['records'] as &$r) {
                    $r['attribute_values'] = $byItem[$r['id']] ?? [];
                }
                unset($r);
            }
            $this->sendSuccess($result);
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function show($id = null) {
        try {
            if ($id === null) $id = $this->getRequestId();
            $companyId = $this->getCurrentUser() ? $this->getCurrentUser()['company_id'] : 1;
            $item = $this->itemModel->getByIdAndCompany($id, $companyId);
            if (!$item) {
                $this->sendError('Item not found', 404);
                return;
            }
            if (!empty($item['sub_group_id'])) {
                $item['attribute_values'] = $this->itemAttributeValueModel->getByItemId($id);
            }
            $this->sendSuccess($item);
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function store() {
        try {
            $data = $this->getRequestData();
            $required = ['name', 'name_in_urdu'];
            $hasErp = !empty($data['category_id']) && !empty($data['group_id']) && !empty($data['sub_group_id']);
            if (!$hasErp) {
                $required[] = 'main_head_id';
                $required[] = 'control_head_id';
            }
            $errors = $this->validateRequired($data, $required);
            if (!empty($errors)) {
                $this->sendError('Validation failed', 400, $errors);
                return;
            }
            $rateErrors = $this->validateAndSanitizeRates($data);
            if (!empty($rateErrors)) {
                $this->sendError('Validation failed', 400, $rateErrors);
                return;
            }
            $companyId = $this->getCurrentUser() ? $this->getCurrentUser()['company_id'] : 1;
            $data['company_id'] = $companyId;
            $unitId = $data['unit_id'] ?? null;
            $rackId = $data['rack_id'] ?? null;
            unset($data['unit_id']);
            $attributeValues = $data['attribute_values'] ?? [];
            unset($data['attribute_values']);
            if (!empty($data['normalized_sku'])) {
                $data['source_id'] = $data['normalized_sku'];
            }
            $createdId = $this->itemModel->createForCompany($data, $companyId);
            if (!$createdId) {
                $this->sendError('Failed to create item');
                return;
            }
            if (!empty($data['normalized_sku'])) {
                $this->itemModel->updateForCompany($createdId, ['source_id' => $data['normalized_sku']], $companyId);
            }
            foreach ($attributeValues as $av) {
                if (!empty($av['attribute_id']) && isset($av['value'])) {
                    $this->itemAttributeValueModel->create([
                        'item_id' => $createdId,
                        'attribute_id' => (int)$av['attribute_id'],
                        'value' => $av['value']
                    ]);
                }
            }
            if (!empty($rackId) && !empty($unitId)) {
                $this->itemRackAssignmentModel->assignItemToRack($createdId, $rackId, $unitId, $companyId, true);
            }
            $created = $this->itemModel->getByIdAndCompany($createdId, $companyId);
            $this->sendSuccess($created, 201);
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function update($id = null) {
        try {
            if ($id === null) $id = $this->getRequestId();
            $data = $this->getRequestData();
            $required = ['name', 'name_in_urdu'];
            $hasErp = !empty($data['category_id']) && !empty($data['group_id']) && !empty($data['sub_group_id']);
            if (!$hasErp) {
                $required[] = 'main_head_id';
                $required[] = 'control_head_id';
            }
            $errors = $this->validateRequired($data, $required);
            if (!empty($errors)) {
                $this->sendError('Validation failed', 400, $errors);
                return;
            }
            $rateErrors = $this->validateAndSanitizeRates($data);
            if (!empty($rateErrors)) {
                $this->sendError('Validation failed', 400, $rateErrors);
                return;
            }
            $companyId = $this->getCurrentUser() ? $this->getCurrentUser()['company_id'] : 1;
            $existing = $this->itemModel->getByIdAndCompany($id, $companyId);
            if (!$existing) {
                $this->sendError('Item not found', 404);
                return;
            }
            if (empty($data['normalized_sku']) && !empty($existing['source_id'])) {
                $data['normalized_sku'] = $existing['source_id'];
                $data['source_id'] = $existing['source_id'];
            }
            $unitId = $data['unit_id'] ?? null;
            $rackId = $data['rack_id'] ?? null;
            $attributeValues = $data['attribute_values'] ?? null;
            unset($data['unit_id'], $data['attribute_values']);
            if (!empty($data['normalized_sku'])) $data['source_id'] = $data['normalized_sku'];
            $this->itemModel->updateForCompany($id, $data, $companyId);
            if ($attributeValues !== null && is_array($attributeValues)) {
                $this->itemAttributeValueModel->deleteByItemId($id);
                foreach ($attributeValues as $av) {
                    if (!empty($av['attribute_id']) && isset($av['value'])) {
                        $this->itemAttributeValueModel->create([
                            'item_id' => $id,
                            'attribute_id' => (int)$av['attribute_id'],
                            'value' => $av['value']
                        ]);
                    }
                }
            }
            if (!empty($rackId) && !empty($unitId)) {
                $this->itemRackAssignmentModel->updatePrimaryRackAssignment($id, $rackId, $unitId, $companyId);
            }
            $this->sendSuccess(['message' => 'Updated']);
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function destroy($id = null) {
        try {
            if ($id === null) $id = $this->getRequestId();
            $companyId = $this->getCurrentUser() ? $this->getCurrentUser()['company_id'] : 1;
            $this->itemModel->softDeleteForCompany($id, $companyId);
            $this->sendSuccess(['message' => 'Item deleted']);
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function getMainHeads() {
        try {
            $companyId = $this->getCurrentUser() ? $this->getCurrentUser()['company_id'] : 1;
            $rows = $this->mainHeadModel->getNamesByTypeForCompany($companyId, 'item', 'I');
            $this->sendSuccess($rows);
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function getControlHeads() {
        try {
            $mainHeadId = $_GET['main_head_id'] ?? null;
            if (!$mainHeadId) {
                $this->sendError('Main head ID is required');
                return;
            }
            $companyId = $this->getCurrentUser() ? $this->getCurrentUser()['company_id'] : 1;
            $rows = $this->controlHeadModel->findBy([
                'main_head_id' => $mainHeadId,
                'company_id' => $companyId,
                'type' => 'item',
                'status' => 'I'
            ]);
            $this->sendSuccess($rows);
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function getRackAssignment() {
        try {
            $itemId = $this->getRequestId();
            $unitId = isset($_GET['unit_id']) ? (int)$_GET['unit_id'] : null;
            if (!$itemId || !$unitId) {
                $this->sendError('Item ID and Unit ID required', 400);
                return;
            }
            $companyId = $this->getCurrentUser() ? $this->getCurrentUser()['company_id'] : 1;
            $ra = $this->itemRackAssignmentModel->getPrimaryRackForItemInUnit($itemId, $unitId, $companyId);
            $this->sendSuccess($ra ? [
                'rack_id' => $ra['rack_id'],
                'rack_name' => $ra['rack_name'],
                'rack_name_in_urdu' => $ra['rack_name_in_urdu'] ?? null,
                'unit_id' => $ra['unit_id'],
                'unit_name' => $ra['unit_name'] ?? null,
                'is_primary' => $ra['is_primary']
            ] : ['rack_id' => null, 'rack_name' => null, 'unit_id' => $unitId]);
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function updateRackAssignment() {
        try {
            $itemId = $this->getRequestId();
            $data = $this->getRequestData();
            $rackId = (int)($data['rack_id'] ?? 0);
            $unitId = (int)($data['unit_id'] ?? 0);
            if (!$itemId || !$rackId || !$unitId) {
                $this->sendError('Item ID, Rack ID and Unit ID required', 400);
                return;
            }
            $companyId = $this->getCurrentUser() ? $this->getCurrentUser()['company_id'] : 1;
            $this->itemRackAssignmentModel->updatePrimaryRackAssignment($itemId, $rackId, $unitId, $companyId);
            $ra = $this->itemRackAssignmentModel->getPrimaryRackForItemInUnit($itemId, $unitId, $companyId);
            $this->sendSuccess($ra ? [
                'rack_id' => $ra['rack_id'],
                'rack_name' => $ra['rack_name'],
                'rack_name_in_urdu' => $ra['rack_name_in_urdu'] ?? null,
                'unit_id' => $ra['unit_id'],
                'unit_name' => $ra['unit_name'] ?? null,
                'is_primary' => $ra['is_primary']
            ] : []);
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function getCategories() {
        try {
            $companyId = $this->getCurrentUser() ? $this->getCurrentUser()['company_id'] : 1;
            $this->sendSuccess($this->itemCategoryModel->getAllByCompany($companyId));
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function getGroups() {
        try {
            $categoryId = $_GET['category_id'] ?? null;
            if (!$categoryId) {
                $this->sendError('category_id required');
                return;
            }
            $companyId = $this->getCurrentUser() ? $this->getCurrentUser()['company_id'] : 1;
            $this->sendSuccess($this->itemGroupModel->getByCategoryId($categoryId, $companyId));
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function getSubGroups() {
        try {
            $groupId = $_GET['group_id'] ?? null;
            if (!$groupId) {
                $this->sendError('group_id required');
                return;
            }
            $companyId = $this->getCurrentUser() ? $this->getCurrentUser()['company_id'] : 1;
            $this->sendSuccess($this->itemSubGroupModel->getByGroupId($groupId, $companyId));
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function getAttributes() {
        try {
            $subGroupId = $_GET['sub_group_id'] ?? null;
            if (!$subGroupId) {
                $this->sendError('sub_group_id required');
                return;
            }
            $this->sendSuccess($this->itemAttributeModel->getBySubGroupId($subGroupId));
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function generateSku() {
        try {
            $data = $this->getRequestData();
            $categoryId = $data['category_id'] ?? null;
            $groupId = $data['group_id'] ?? null;
            $subGroupId = $data['sub_group_id'] ?? null;
            if (!$categoryId || !$groupId || !$subGroupId) {
                $this->sendError('category_id, group_id, sub_group_id required');
                return;
            }
            $cat = $this->itemCategoryModel->getById($categoryId);
            $grp = $this->itemGroupModel->getById($groupId);
            $sg = $this->itemSubGroupModel->getById($subGroupId);
            if (!$cat || !$grp || !$sg) {
                $this->sendError('Invalid category/group/sub-group');
                return;
            }
            $seq = $this->itemSkuSequenceModel->getNextSequence($categoryId, $groupId, $subGroupId);
            $normalizedSku = $cat['code'] . '-' . $grp['code'] . '-' . $sg['code'] . '-' . $seq;
            $this->sendSuccess(['normalized_sku' => $normalizedSku]);
        } catch (Exception $e) {
            $this->sendError($e->getMessage(), 500);
        }
    }

    public function storeCategory() {
        try {
            $data = $this->getRequestData();
            $errors = $this->validateRequired($data, ['name', 'code']);
            if (!empty($errors)) { $this->sendError('Validation failed', 400, $errors); return; }
            $companyId = $this->getCurrentUser() ? $this->getCurrentUser()['company_id'] : 1;
            $id = $this->itemCategoryModel->createForCompany($data, $companyId);
            $this->sendSuccess($this->itemCategoryModel->getByIdAndCompany($id, $companyId), 201);
        } catch (Exception $e) { $this->sendError($e->getMessage(), 500); }
    }

    public function updateCategory() {
        try {
            $id = $this->getRequestId();
            $data = $this->getRequestData();
            $companyId = $this->getCurrentUser() ? $this->getCurrentUser()['company_id'] : 1;
            $this->itemCategoryModel->updateForCompany($id, $data, $companyId);
            $this->sendSuccess($this->itemCategoryModel->getByIdAndCompany($id, $companyId));
        } catch (Exception $e) { $this->sendError($e->getMessage(), 500); }
    }

    public function destroyCategory() {
        try {
            $id = $this->getRequestId();
            $companyId = $this->getCurrentUser() ? $this->getCurrentUser()['company_id'] : 1;
            $this->itemCategoryModel->softDeleteForCompany($id, $companyId);
            $this->sendSuccess(['message' => 'Deleted']);
        } catch (Exception $e) { $this->sendError($e->getMessage(), 500); }
    }

    public function storeGroup() {
        try {
            $data = $this->getRequestData();
            $errors = $this->validateRequired($data, ['name', 'code', 'category_id']);
            if (!empty($errors)) { $this->sendError('Validation failed', 400, $errors); return; }
            $companyId = $this->getCurrentUser() ? $this->getCurrentUser()['company_id'] : 1;
            $id = $this->itemGroupModel->createForCompany($data, $companyId);
            $this->sendSuccess($this->itemGroupModel->getByIdAndCompany($id, $companyId) ?: ['id' => $id], 201);
        } catch (Exception $e) { $this->sendError($e->getMessage(), 500); }
    }

    public function updateGroup() {
        try {
            $id = $this->getRequestId();
            $data = $this->getRequestData();
            $companyId = $this->getCurrentUser() ? $this->getCurrentUser()['company_id'] : 1;
            $this->itemGroupModel->updateForCompany($id, $data, $companyId);
            $this->sendSuccess(['message' => 'Updated']);
        } catch (Exception $e) { $this->sendError($e->getMessage(), 500); }
    }

    public function destroyGroup() {
        try {
            $id = $this->getRequestId();
            $companyId = $this->getCurrentUser() ? $this->getCurrentUser()['company_id'] : 1;
            $this->itemGroupModel->softDeleteForCompany($id, $companyId);
            $this->sendSuccess(['message' => 'Deleted']);
        } catch (Exception $e) { $this->sendError($e->getMessage(), 500); }
    }

    public function storeSubGroup() {
        try {
            $data = $this->getRequestData();
            $errors = $this->validateRequired($data, ['name', 'code', 'group_id']);
            if (!empty($errors)) { $this->sendError('Validation failed', 400, $errors); return; }
            $companyId = $this->getCurrentUser() ? $this->getCurrentUser()['company_id'] : 1;
            $id = $this->itemSubGroupModel->createForCompany($data, $companyId);
            $this->sendSuccess($this->itemSubGroupModel->getByIdAndCompany($id, $companyId) ?: ['id' => $id], 201);
        } catch (Exception $e) { $this->sendError($e->getMessage(), 500); }
    }

    public function updateSubGroup() {
        try {
            $id = $this->getRequestId();
            $data = $this->getRequestData();
            $companyId = $this->getCurrentUser() ? $this->getCurrentUser()['company_id'] : 1;
            $this->itemSubGroupModel->updateForCompany($id, $data, $companyId);
            $this->sendSuccess(['message' => 'Updated']);
        } catch (Exception $e) { $this->sendError($e->getMessage(), 500); }
    }

    public function destroySubGroup() {
        try {
            $id = $this->getRequestId();
            $companyId = $this->getCurrentUser() ? $this->getCurrentUser()['company_id'] : 1;
            $this->itemSubGroupModel->softDeleteForCompany($id, $companyId);
            $this->sendSuccess(['message' => 'Deleted']);
        } catch (Exception $e) { $this->sendError($e->getMessage(), 500); }
    }

    public function storeAttribute() {
        try {
            $data = $this->getRequestData();
            $errors = $this->validateRequired($data, ['attribute_name', 'sub_group_id']);
            if (!empty($errors)) { $this->sendError('Validation failed', 400, $errors); return; }
            $id = $this->itemAttributeModel->create($data);
            $this->sendSuccess($this->itemAttributeModel->getById($id) ?: ['id' => $id], 201);
        } catch (Exception $e) { $this->sendError($e->getMessage(), 500); }
    }

    public function updateAttribute() {
        try {
            $id = $this->getRequestId();
            $data = $this->getRequestData();
            $this->itemAttributeModel->updateById($id, $data);
            $this->sendSuccess(['message' => 'Updated']);
        } catch (Exception $e) { $this->sendError($e->getMessage(), 500); }
    }

    public function destroyAttribute() {
        try {
            $id = $this->getRequestId();
            $this->itemAttributeModel->deleteById($id);
            $this->sendSuccess(['message' => 'Deleted']);
        } catch (Exception $e) { $this->sendError($e->getMessage(), 500); }
    }
}
?>
