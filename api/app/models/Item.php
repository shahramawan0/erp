<?php
require_once __DIR__ . '/BaseModel.php';

class Item extends BaseModel {
    protected $table = 'items';

    public function getByIdAndCompany($id, $companyId) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? AND company_id = ? AND is_deleted = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id, $companyId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function createForCompany($data, $companyId) {
        $data['company_id'] = $companyId;
        return $this->create($data);
    }

    public function updateForCompany($id, $data, $companyId) {
        $sql = "UPDATE {$this->table} SET ";
        $setClause = [];
        $params = [];
        foreach ($data as $column => $value) {
            $setClause[] = "$column = ?";
            $params[] = $value;
        }
        $sql .= implode(', ', $setClause) . " WHERE id = ? AND company_id = ? AND is_deleted = 0";
        $params[] = $id;
        $params[] = $companyId;
        return $this->db->prepare($sql)->execute($params);
    }

    public function softDeleteForCompany($id, $companyId) {
        $sql = "UPDATE {$this->table} SET is_deleted = 1, deleted_at = NOW() WHERE id = ? AND company_id = ? AND is_deleted = 0";
        return $this->db->prepare($sql)->execute([$id, $companyId]);
    }

    public function searchWithDetailsForCompanyAndUnit($companyId, $unitId = null, $filters = [], $page = 1, $limit = 10, $searchTerm = '') {
        $offset = ($page - 1) * $limit;
        $whereConditions = ['i.is_deleted = 0', 'i.company_id = ?'];
        $params = [$companyId];
        $status = isset($filters['status']) && $filters['status'] !== '' ? $filters['status'] : 'I';
        $whereConditions[] = 'i.status = ?';
        $params[] = $status;
        foreach ($filters as $key => $value) {
            if ($value !== null && $value !== '' && !in_array($key, ['status'])) {
                $whereConditions[] = "i.$key = ?";
                $params[] = $value;
            }
        }
        if (!empty($searchTerm)) {
            $whereConditions[] = "(i.source_id LIKE ? OR i.name LIKE ? OR i.name_in_urdu LIKE ? OR i.description LIKE ? OR i.normalized_sku LIKE ?)";
            $searchPattern = "%$searchTerm%";
            for ($i = 0; $i < 5; $i++) $params[] = $searchPattern;
        }
        $whereClause = implode(' AND ', $whereConditions);

        $iraJoin = $unitId
            ? "LEFT JOIN item_rack_assignments ira ON ira.item_id = i.id AND ira.unit_id = ? AND ira.is_primary = 1 AND ira.is_deleted = 0"
            : "LEFT JOIN item_rack_assignments ira ON ira.item_id = i.id AND ira.is_primary = 1 AND ira.is_deleted = 0";
        $execParams = $params;
        if ($unitId) array_unshift($execParams, $unitId);

        $sql = "SELECT i.*, mh.name as main_head_name, mh.name_in_urdu as main_head_name_in_urdu,
                ch.name as control_head_name, ch.name_in_urdu as control_head_name_in_urdu,
                ic.name as category_name, ig.name as group_name, isg.name as sub_group_name,
                r.name as rack_name, ut.name as unit_type_name, ut.name_in_urdu as unit_type_name_in_urdu
                FROM {$this->table} i
                LEFT JOIN main_heads mh ON i.main_head_id = mh.id AND mh.is_deleted = 0
                LEFT JOIN control_heads ch ON i.control_head_id = ch.id AND ch.is_deleted = 0
                LEFT JOIN item_categories ic ON i.category_id = ic.id AND ic.is_deleted = 0
                LEFT JOIN item_groups ig ON i.group_id = ig.id AND ig.is_deleted = 0
                LEFT JOIN item_sub_groups isg ON i.sub_group_id = isg.id AND isg.is_deleted = 0
                $iraJoin
                LEFT JOIN racks r ON ira.rack_id = r.id AND r.is_deleted = 0
                LEFT JOIN unit_types ut ON i.unit_type_id = ut.id AND ut.is_deleted = 0
                WHERE $whereClause AND i.status = 'I'
                ORDER BY i.created_at DESC LIMIT " . (int)$limit . " OFFSET " . (int)$offset;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($execParams);
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $countSql = "SELECT COUNT(*) as total FROM {$this->table} i WHERE $whereClause";
        $cStmt = $this->db->prepare($countSql);
        $cStmt->execute($params);
        $total = $cStmt->fetch(PDO::FETCH_ASSOC)['total'];

        return ['records' => $records, 'total' => $total, 'page' => $page, 'limit' => $limit, 'total_pages' => ceil($total / $limit)];
    }
}
?>
