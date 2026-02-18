<?php
require_once __DIR__ . '/BaseModel.php';

class ItemSubGroup extends BaseModel {
    protected $table = 'item_sub_groups';
    protected $primaryKey = 'id';

    public function getByIdAndCompany($id, $companyId) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? AND company_id = ? AND is_deleted = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id, $companyId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByGroupId($groupId, $companyId, $activeOnly = true) {
        $sql = "SELECT * FROM {$this->table} WHERE group_id = ? AND company_id = ? AND is_deleted = 0";
        $params = [$groupId, $companyId];
        if ($activeOnly) $sql .= " AND is_active = 1";
        $sql .= " ORDER BY name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function createForCompany($data, $companyId) {
        $data['company_id'] = $companyId;
        $data['is_deleted'] = 0;
        if (!isset($data['is_active'])) $data['is_active'] = 1;
        if (!isset($data['code'])) $data['code'] = (string)(time() % 10000);
        return $this->create($data);
    }

    public function updateForCompany($id, $data, $companyId) {
        return $this->update($id, $data);
    }

    public function softDeleteForCompany($id, $companyId) {
        $sql = "UPDATE {$this->table} SET is_deleted = 1 WHERE id = ? AND company_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id, $companyId]);
    }
}
?>
