<?php
require_once __DIR__ . '/BaseModel.php';

class ItemSkuSequence extends BaseModel {
    protected $table = 'item_sku_sequences';
    protected $primaryKey = 'id';

    public function getNextSequence($categoryId, $groupId, $subGroupId) {
        $this->db->beginTransaction();
        try {
            $sql = "INSERT INTO {$this->table} (category_id, group_id, sub_group_id, last_sequence, updated_at) VALUES (?, ?, ?, 1, NOW()) ON DUPLICATE KEY UPDATE last_sequence = last_sequence + 1, updated_at = NOW()";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$categoryId, $groupId, $subGroupId]);
            $sql = "SELECT last_sequence FROM {$this->table} WHERE category_id = ? AND group_id = ? AND sub_group_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$categoryId, $groupId, $subGroupId]);
            $row = $stmt->fetch();
            $this->db->commit();
            return $row ? (int)$row['last_sequence'] : 1;
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
?>
