<?php
require_once __DIR__ . '/BaseModel.php';

class ItemRackAssignment extends BaseModel {
    protected $table = 'item_rack_assignments';
    protected $primaryKey = 'id';

    public function assignItemToRack($itemId, $rackId, $unitId, $companyId, $isPrimary = true) {
        try {
            $this->db->beginTransaction();
            if ($isPrimary) {
                $sql = "UPDATE {$this->table} SET is_primary = 0 WHERE item_id = ? AND unit_id = ? AND company_id = ? AND is_deleted = 0";
                $this->db->prepare($sql)->execute([$itemId, $unitId, $companyId]);
            }
            $sql = "SELECT id FROM {$this->table} WHERE item_id = ? AND rack_id = ? AND unit_id = ? AND company_id = ? AND is_deleted = 0";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$itemId, $rackId, $unitId, $companyId]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($existing) {
                $sql = "UPDATE {$this->table} SET is_primary = ?, status = 'A', is_deleted = 0 WHERE id = ?";
                $this->db->prepare($sql)->execute([$isPrimary ? 1 : 0, $existing['id']]);
                $aid = $existing['id'];
            } else {
                $sql = "INSERT INTO {$this->table} (item_id, rack_id, unit_id, company_id, is_primary, status) VALUES (?, ?, ?, ?, ?, 'A')";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$itemId, $rackId, $unitId, $companyId, $isPrimary ? 1 : 0]);
                $aid = $this->db->lastInsertId();
            }
            $this->db->commit();
            return $aid;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function getPrimaryRackForItemInUnit($itemId, $unitId, $companyId) {
        $sql = "SELECT ira.*, r.name as rack_name FROM {$this->table} ira INNER JOIN racks r ON ira.rack_id = r.id WHERE ira.item_id = ? AND ira.unit_id = ? AND ira.company_id = ? AND ira.is_primary = 1 AND ira.is_deleted = 0 AND ira.status = 'A'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$itemId, $unitId, $companyId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function updatePrimaryRackAssignment($itemId, $newRackId, $unitId, $companyId) {
        try {
            $this->db->beginTransaction();
            $sql = "UPDATE {$this->table} SET is_primary = 0 WHERE item_id = ? AND unit_id = ? AND company_id = ? AND is_deleted = 0";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$itemId, $unitId, $companyId]);
            $sql = "SELECT id FROM {$this->table} WHERE item_id = ? AND rack_id = ? AND unit_id = ? AND company_id = ? AND is_deleted = 0";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$itemId, $newRackId, $unitId, $companyId]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($existing) {
                $sql = "UPDATE {$this->table} SET is_primary = 1, status = 'A', is_deleted = 0 WHERE id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$existing['id']]);
            } else {
                $sql = "INSERT INTO {$this->table} (item_id, rack_id, unit_id, company_id, is_primary, status) VALUES (?, ?, ?, ?, 1, 'A')";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$itemId, $newRackId, $unitId, $companyId]);
            }
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
}
?>
