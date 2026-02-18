<?php
require_once __DIR__ . '/../config/Database.php';

abstract class BaseModel {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getAll($filters = []) {
        $sql = "SELECT * FROM {$this->table} WHERE is_deleted = 0";
        $params = [];
        if (!empty($filters)) {
            foreach ($filters as $key => $value) {
                $sql .= " AND $key = ?";
                $params[] = $value;
            }
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? AND is_deleted = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(array_values($data)) ? $this->db->lastInsertId() : false;
    }

    public function update($id, $data) {
        $set = [];
        foreach (array_keys($data) as $col) $set[] = "$col = ?";
        $values = array_values($data);
        $values[] = $id;
        $sql = "UPDATE {$this->table} SET " . implode(', ', $set) . " WHERE {$this->primaryKey} = ? AND is_deleted = 0";
        return $this->db->prepare($sql)->execute($values);
    }

    public function findOneBy($criteria) {
        $where = [];
        $params = [];
        foreach ($criteria as $k => $v) {
            $where[] = "$k = ?";
            $params[] = $v;
        }
        $sql = "SELECT * FROM {$this->table} WHERE " . implode(' AND ', $where) . " LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findBy($criteria) {
        $where = [];
        $params = [];
        foreach ($criteria as $k => $v) {
            $where[] = "$k = ?";
            $params[] = $v;
        }
        $sql = "SELECT * FROM {$this->table} WHERE is_deleted = 0 AND " . implode(' AND ', $where);
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
