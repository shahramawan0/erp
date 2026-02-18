<?php
require_once __DIR__ . '/BaseModel.php';

class Rack extends BaseModel {
    protected $table = 'racks';
    protected $primaryKey = 'id';

    public function getAllByCompany($companyId, $filters = []) {
        $where = ['is_deleted = 0', 'company_id = ?'];
        $params = [$companyId];
        foreach ($filters as $k => $v) {
            if ($v !== null && $v !== '') {
                $where[] = "$k = ?";
                $params[] = $v;
            }
        }
        $sql = "SELECT id, name, unit_id FROM {$this->table} WHERE " . implode(' AND ', $where) . " ORDER BY name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByIdAndCompany($id, $companyId) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? AND company_id = ? AND is_deleted = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id, $companyId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}
?>
