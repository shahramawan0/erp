<?php
require_once __DIR__ . '/BaseModel.php';

class ControlHead extends BaseModel {
    protected $table = 'control_heads';
    protected $primaryKey = 'id';

    public function getAllByCompany($companyId, $filters = []) {
        $filters['company_id'] = $companyId;
        return $this->getAll($filters);
    }

    public function getByIdAndCompany($id, $companyId) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? AND company_id = ? AND is_deleted = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id, $companyId]);
        return $stmt->fetch();
    }

    public function createForCompany($data, $companyId) {
        $data['company_id'] = $companyId;
        return $this->create($data);
    }

    public function updateForCompany($id, $data, $companyId) {
        unset($data['type']);
        $sql = "UPDATE {$this->table} SET ";
        $setClause = [];
        $params = [];
        foreach ($data as $column => $value) {
            $setClause[] = "$column = ?";
            $params[] = $value;
        }
        $sql .= implode(', ', $setClause) . " WHERE {$this->primaryKey} = ? AND company_id = ? AND is_deleted = 0";
        $params[] = $id;
        $params[] = $companyId;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function softDeleteForCompany($id, $companyId) {
        $sql = "UPDATE {$this->table} SET is_deleted = 1 WHERE {$this->primaryKey} = ? AND company_id = ? AND is_deleted = 0";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id, $companyId]);
    }

    public function getByMainHead($mainHeadId, $companyId) {
        return $this->findBy(['main_head_id' => $mainHeadId, 'company_id' => $companyId]);
    }

    public function searchForCompanyWithMainHead($companyId, $filters = [], $page = 1, $limit = 10, $searchTerm = '') {
        $offset = ($page - 1) * $limit;
        $whereConditions = ['ch.is_deleted = 0', 'ch.company_id = ?'];
        $params = [$companyId];
        if (!empty($filters['type'])) { $whereConditions[] = "ch.type = ?"; $params[] = $filters['type']; }
        if (!empty($filters['status'])) { $whereConditions[] = "ch.status = ?"; $params[] = $filters['status']; }
        if (!empty($filters['main_head_id'])) { $whereConditions[] = "ch.main_head_id = ?"; $params[] = $filters['main_head_id']; }
        if (!empty($searchTerm)) {
            $whereConditions[] = "(ch.name LIKE ? OR ch.name_in_urdu LIKE ? OR mh.name LIKE ?)";
            $p = "%$searchTerm%";
            $params[] = $p; $params[] = $p; $params[] = $p;
        }
        $whereClause = implode(' AND ', $whereConditions);
        $sql = "SELECT ch.*, mh.name as main_head_name, mh.name_in_urdu as main_head_name_in_urdu FROM {$this->table} ch LEFT JOIN main_heads mh ON ch.main_head_id = mh.id AND mh.is_deleted = 0 WHERE $whereClause ORDER BY ch.created_at DESC LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $records = $stmt->fetchAll();
        $countSql = "SELECT COUNT(*) as total FROM {$this->table} ch LEFT JOIN main_heads mh ON ch.main_head_id = mh.id AND mh.is_deleted = 0 WHERE $whereClause";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = $countStmt->fetch()['total'];
        return ['records' => $records, 'total' => $total, 'page' => $page, 'limit' => $limit, 'total_pages' => ceil($total / $limit)];
    }
}
?>
