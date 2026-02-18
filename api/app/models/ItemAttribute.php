<?php
require_once __DIR__ . '/BaseModel.php';

class ItemAttribute extends BaseModel {
    protected $table = 'item_attributes';
    protected $primaryKey = 'id';

    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? AND (is_deleted = 0 OR is_deleted IS NULL)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getBySubGroupId($subGroupId) {
        $sql = "SELECT * FROM {$this->table} WHERE sub_group_id = ? AND (is_deleted = 0 OR is_deleted IS NULL) ORDER BY sort_order ASC, attribute_name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$subGroupId]);
        return $stmt->fetchAll();
    }

    public function create($data) {
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_required'] = $data['is_required'] ?? 0;
        $data['is_deleted'] = 0;
        return parent::create($data);
    }

    public function updateById($id, $data) {
        $setClause = [];
        $params = [];
        foreach (['attribute_name', 'is_required', 'sort_order'] as $col) {
            if (array_key_exists($col, $data)) {
                $setClause[] = "$col = ?";
                $params[] = $data[$col];
            }
        }
        if (empty($setClause)) return false;
        $params[] = $id;
        $sql = "UPDATE {$this->table} SET " . implode(', ', $setClause) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function deleteById($id) {
        $sql = "UPDATE {$this->table} SET is_deleted = 1 WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}
?>
