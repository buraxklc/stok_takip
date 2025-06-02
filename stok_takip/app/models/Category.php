<?php
// app/models/Category.php

class Category extends Model {
    protected $table = 'categories';
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getAll() {
        try {
            $query = "SELECT * FROM " . $this->table . " ORDER BY name ASC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function getAllWithParentName() {
        try {
            $query = "SELECT c.*, p.name as parent_name 
                    FROM " . $this->table . " c
                    LEFT JOIN " . $this->table . " p ON c.parent_id = p.id
                    ORDER BY c.name ASC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function getById($id) {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function getMainCategories() {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE parent_id IS NULL ORDER BY name ASC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function getSubCategories($parent_id) {
        try {
            $query = "SELECT * FROM " . $this->table . " WHERE parent_id = ? ORDER BY name ASC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $parent_id);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function create($data) {
        try {
            $query = "INSERT INTO " . $this->table . " (name, parent_id, description, status) VALUES (?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            
            // NULL değeri kontrol et
            $parent_id = !empty($data['parent_id']) ? $data['parent_id'] : NULL;
            
            $stmt->bindParam(1, $data['name']);
            $stmt->bindParam(2, $parent_id);
            $stmt->bindParam(3, $data['description']);
            $stmt->bindParam(4, $data['status']);
            
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function update($id, $data) {
        try {
            $query = "UPDATE " . $this->table . " SET name = ?, parent_id = ?, description = ?, status = ? WHERE id = ?";
            $stmt = $this->db->prepare($query);
            
            // NULL değeri kontrol et
            $parent_id = !empty($data['parent_id']) ? $data['parent_id'] : NULL;
            
            $stmt->bindParam(1, $data['name']);
            $stmt->bindParam(2, $parent_id);
            $stmt->bindParam(3, $data['description']);
            $stmt->bindParam(4, $data['status']);
            $stmt->bindParam(5, $id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function delete($id) {
        try {
            // Önce alt kategorileri kontrol et
            $query = "SELECT COUNT(*) as count FROM " . $this->table . " WHERE parent_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($result['count'] > 0) {
                return false; // Alt kategoriler varsa silme
            }
            
            // Kategoriyi kullanan ürünleri kontrol et
            $query = "SELECT COUNT(*) as count FROM products WHERE category_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($result['count'] > 0) {
                return false; // Ürünler varsa silme
            }
            
            // Kategoriyi sil
            $query = "DELETE FROM " . $this->table . " WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function getCount() {
        try {
            $query = "SELECT COUNT(*) as total FROM " . $this->table;
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'];
        } catch (PDOException $e) {
            return 0;
        }
    }
}