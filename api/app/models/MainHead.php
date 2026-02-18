<?php
require_once __DIR__ . '/BaseModel.php';

class MainHead extends BaseModel {
    protected $table = 'main_heads';
    protected $primaryKey = 'id';

    public function getAllByCompany($companyId, $filters = []) {
        $whereConditions = ['is_deleted = 0', 'company_id = ?'];
        $params = [$companyId];
        foreach ($filters as $key => $value) {
            if ($value !== null && $value !== '') {
                $whereConditions[] = "$key = ?";
                $params[] = $value;
            }
        }
        $whereClause = implode(' AND ', $whereConditions);
        $sql = "SELECT * FROM {$this->table} WHERE $whereClause ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getByIdAndCompany($id, $companyId) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? AND company_id = ? AND is_deleted = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id, $companyId]);
        return $stmt->fetch();
    }

    public function createForCompany($data, $companyId) {
        $data['company_id'] = $companyId;
        return $this->create($data);
    }

    public function updateForCompany($id, $data, $companyId) {
        return $this->update($id, $data);
    }

    public function softDeleteForCompany($id, $companyId) {
        $sql = "UPDATE {$this->table} SET is_deleted = 1 WHERE id = ? AND company_id = ? AND is_deleted = 0";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id, $companyId]);
    }

    public function getNamesByTypeForCompany($companyId, $type, $status = null) {
        $where = 'company_id = ? AND type = ? AND is_deleted = 0';
        $params = [$companyId, $type];
        if ($status !== null && $status !== '') {
            $where .= ' AND status = ?';
            $params[] = $status;
        }
        $sql = "SELECT id, name, name_in_urdu FROM {$this->table} WHERE $where ORDER BY name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchForCompany($companyId, $filters = [], $page = 1, $limit = 10, $searchTerm = '') {
        $offset = ($page - 1) * $limit;
        $whereConditions = ['is_deleted = 0', 'company_id = ?'];
        $params = [$companyId];
        $defaultType = $filters['type'] ?? 'item';
        $defaultStatus = $filters['status'] ?? ($defaultType === 'item' ? 'I' : 'A');
        $whereConditions[] = 'type = ?';
        $params[] = $defaultType;
        $whereConditions[] = 'status = ?';
        $params[] = $defaultStatus;
        foreach ($filters as $key => $value) {
            if (in_array($key, ['type', 'status'])) continue;
            if ($value !== null && $value !== '') {
                $whereConditions[] = "$key = ?";
                $params[] = $value;
            }
        }
        if (!empty($searchTerm)) {
            $whereConditions[] = "(name LIKE ? OR name_in_urdu LIKE ? OR description LIKE ?)";
            $searchPattern = "%$searchTerm%";
            $params[] = $searchPattern;
            $params[] = $searchPattern;
            $params[] = $searchPattern;
        }
        $whereClause = implode(' AND ', $whereConditions);
        $sql = "SELECT * FROM {$this->table} WHERE $whereClause ORDER BY id DESC LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $records = $stmt->fetchAll();
        $countSql = "SELECT COUNT(*) as total FROM {$this->table} WHERE $whereClause";
        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = $countStmt->fetch()['total'];
        return ['records' => $records, 'total' => $total, 'page' => $page, 'limit' => $limit, 'total_pages' => ceil($total / $limit)];
    }
}
?>
