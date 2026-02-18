<?php
require_once __DIR__ . '/BaseModel.php';

class ItemAttributeValue extends BaseModel {
    protected $table = 'item_attribute_values';
    protected $primaryKey = 'id';

    public function getByItemId($itemId) {
        $sql = "SELECT iav.*, ia.attribute_name, ia.is_required FROM {$this->table} iav INNER JOIN item_attributes ia ON ia.id = iav.attribute_id WHERE iav.item_id = ? ORDER BY ia.sort_order, ia.attribute_name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$itemId]);
        return $stmt->fetchAll();
    }

    public function getByItemIds($itemIds) {
        if (empty($itemIds)) return [];
        $placeholders = implode(',', array_fill(0, count($itemIds), '?'));
        $sql = "SELECT iav.item_id, iav.attribute_id, iav.value as attribute_value, ia.attribute_name, ia.is_required FROM {$this->table} iav INNER JOIN item_attributes ia ON ia.id = iav.attribute_id WHERE iav.item_id IN ($placeholders) ORDER BY iav.item_id, ia.sort_order, ia.attribute_name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($itemIds);
        return $stmt->fetchAll();
    }

    public function upsertForItem($itemId, $attributeValues) {
        foreach ($attributeValues as $attributeId => $value) {
            $sql = "INSERT INTO {$this->table} (item_id, attribute_id, value, updated_at) VALUES (?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE value = VALUES(value), updated_at = NOW()";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$itemId, $attributeId, $value]);
        }
    }

    public function deleteByItemId($itemId) {
        $sql = "DELETE FROM {$this->table} WHERE item_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$itemId]);
    }
}
?>
