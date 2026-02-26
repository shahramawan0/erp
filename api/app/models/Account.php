<?php
require_once __DIR__ . '/BaseModel.php';

/**
 * Account model - party accounts (sale and purchase) in account_codes table.
 */
class Account extends BaseModel {
    protected $table = 'account_codes';
    protected $primaryKey = 'id';

    public function getByIdAndCompany($id, $companyId) {
        $sql = "SELECT ac.*, mh.name as main_head_name, mh.name_in_urdu as main_head_name_in_urdu,
                       ch.name as control_head_name, ch.name_in_urdu as control_head_name_in_urdu,
                       c.name as city_name, c.name_in_urdu as city_name_in_urdu,
                       b.name as bank_name, ct.name as company_type_name, pt.name as payment_term_name
                FROM {$this->table} ac
                LEFT JOIN main_heads mh ON ac.main_head_id = mh.id AND mh.is_deleted = 0
                LEFT JOIN control_heads ch ON ac.control_head_id = ch.id AND ch.is_deleted = 0
                LEFT JOIN cities c ON ac.city_id = c.id
                LEFT JOIN banks b ON ac.bank_id = b.id
                LEFT JOIN company_types ct ON ac.company_type_id = ct.id
                LEFT JOIN payment_terms pt ON ac.payment_term_id = pt.id
                WHERE ac.id = ? AND ac.company_id = ? AND ac.is_deleted = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id, $companyId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function createForCompany($data, $companyId) {
        $data['company_id'] = $companyId;
        if (!isset($data['source_id']) || $data['source_id'] === '') {
            $max = $this->getMaxSourceId();
            $data['source_id'] = ($max !== null) ? $max + 1 : 1;
        }
        if (empty($data['code']) && !empty($data['source_id'])) {
            $data['code'] = (string)$data['source_id'];
        }
        $id = $this->create($data);
        return $id ? $this->getByIdAndCompany($id, $companyId) : false;
    }

    public function updateForCompany($id, $data, $companyId) {
        unset($data['id'], $data['company_id']);
        $cols = [];
        $params = [];
        foreach ($data as $k => $v) {
            $cols[] = "`$k` = ?";
            $params[] = $v;
        }
        $params[] = $id;
        $params[] = $companyId;
        $sql = "UPDATE {$this->table} SET " . implode(', ', $cols) . " WHERE id = ? AND company_id = ? AND is_deleted = 0";
        $stmt = $this->db->prepare($sql);
        if (!$stmt->execute($params)) return false;
        return $this->getByIdAndCompany($id, $companyId);
    }

    public function softDeleteForCompany($id, $companyId) {
        $sql = "UPDATE {$this->table} SET is_deleted = 1, deleted_at = NOW() WHERE id = ? AND company_id = ? AND is_deleted = 0";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id, $companyId]);
    }

    public function searchForCompany($companyId, $filters = [], $page = 1, $limit = 10, $searchTerm = '') {
        $offset = (int)(($page - 1) * $limit);
        $limit = (int)$limit;
        $where = ['ac.is_deleted = 0', 'ac.company_id = ?'];
        $params = [$companyId];
        if (!empty($filters['main_head_id'])) { $where[] = 'ac.main_head_id = ?'; $params[] = $filters['main_head_id']; }
        if (!empty($filters['control_head_id'])) { $where[] = 'ac.control_head_id = ?'; $params[] = $filters['control_head_id']; }
        if (isset($filters['account_type']) && $filters['account_type'] !== '') { $where[] = 'ac.account_type = ?'; $params[] = $filters['account_type']; }
        if (!empty($searchTerm)) {
            $where[] = "(ac.name LIKE ? OR ac.name_in_urdu LIKE ? OR ac.company_name LIKE ? OR ac.cell LIKE ? OR ac.code LIKE ? OR CAST(ac.source_id AS CHAR) LIKE ?)";
            $p = '%' . $searchTerm . '%';
            $params[] = $p; $params[] = $p; $params[] = $p; $params[] = $p; $params[] = $p; $params[] = $p;
        }
        $whereClause = implode(' AND ', $where);
        $sql = "SELECT ac.*, mh.name as main_head_name, mh.name_in_urdu as main_head_name_in_urdu,
                       ch.name as control_head_name, ch.name_in_urdu as control_head_name_in_urdu,
                       c.name as city_name
                FROM {$this->table} ac
                LEFT JOIN main_heads mh ON ac.main_head_id = mh.id AND mh.is_deleted = 0
                LEFT JOIN control_heads ch ON ac.control_head_id = ch.id AND ch.is_deleted = 0
                LEFT JOIN cities c ON ac.city_id = c.id
                WHERE $whereClause ORDER BY ac.id DESC LIMIT $limit OFFSET $offset";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $countSql = "SELECT COUNT(*) FROM {$this->table} ac WHERE $whereClause";
        $cStmt = $this->db->prepare($countSql);
        $cStmt->execute($params);
        $total = (int)$cStmt->fetchColumn();
        return [
            'records' => $records,
            'total' => $total,
            'page' => (int)$page,
            'limit' => $limit,
            'total_pages' => ceil($total / $limit)
        ];
    }

    public function getMainHeadsForCompany($companyId) {
        $sql = "SELECT id, name, name_in_urdu FROM main_heads WHERE company_id = ? AND type = 'account' AND is_deleted = 0 ORDER BY name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$companyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getControlHeadsByMainHead($mainHeadId, $companyId) {
        $sql = "SELECT id, name, name_in_urdu FROM control_heads WHERE main_head_id = ? AND company_id = ? AND type = 'account' AND is_deleted = 0 ORDER BY name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$mainHeadId, $companyId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMaxSourceId() {
        $sql = "SELECT MAX(source_id) FROM {$this->table} WHERE is_deleted = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $v = $stmt->fetchColumn();
        return $v !== null ? (int)$v : null;
    }
}
