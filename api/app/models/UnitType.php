<?php
require_once __DIR__ . '/BaseModel.php';

class UnitType extends BaseModel {
    protected $table = 'unit_types';
    protected $primaryKey = 'id';

    public function getAllByCompany($companyId) {
        $sql = "SELECT * FROM {$this->table} WHERE company_id = ? AND is_deleted = 0 ORDER BY name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$companyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByIdAndCompany($id, $companyId) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? AND company_id = ? AND is_deleted = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id, $companyId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function searchForCompany($companyId, $filters = [], $page = 1, $limit = 50, $searchTerm = '') {
        $offset = ($page - 1) * $limit;
        $where = ['is_deleted = 0', 'company_id = ?'];
        $params = [$companyId];
        foreach ($filters as $k => $v) {
            if ($v !== null && $v !== '') { $where[] = "$k = ?"; $params[] = $v; }
        }
        if (!empty($searchTerm)) {
            $where[] = "(name LIKE ? OR name_in_urdu LIKE ?)";
            $p = "%$searchTerm%";
            $params[] = $p; $params[] = $p;
        }
        $whereClause = implode(' AND ', $where);
        $sql = "SELECT * FROM {$this->table} WHERE $whereClause ORDER BY name LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $countSql = "SELECT COUNT(*) as total FROM {$this->table} WHERE $whereClause";
        $cStmt = $this->db->prepare($countSql);
        $cStmt->execute($params);
        $total = $cStmt->fetch(PDO::FETCH_ASSOC)['total'];
        return ['records' => $records, 'total' => $total, 'page' => $page, 'limit' => $limit, 'total_pages' => ceil($total / $limit)];
    }
}
?>
