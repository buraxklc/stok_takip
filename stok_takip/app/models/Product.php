<?php
// app/models/Product.php

class Product extends Model {
    protected $table = 'products';
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getAll() {
        try {
            $query = "SELECT p.*, c.name as category_name 
                     FROM " . $this->table . " p
                     LEFT JOIN categories c ON p.category_id = c.id
                     ORDER BY p.id DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function getById($id) {
        try {
            $query = "SELECT p.*, c.name as category_name 
                     FROM " . $this->table . " p
                     LEFT JOIN categories c ON p.category_id = c.id
                     WHERE p.id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function create($data) {
        try {
            $query = "INSERT INTO " . $this->table . " (code, barcode, name, category_id, purchase_price, sale_price, stock_quantity, min_stock_level, unit, description, image, status) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            
            $stmt->bindParam(1, $data['code']);
            $stmt->bindParam(2, $data['barcode']);
            $stmt->bindParam(3, $data['name']);
            $stmt->bindParam(4, $data['category_id']);
            $stmt->bindParam(5, $data['purchase_price']);
            $stmt->bindParam(6, $data['sale_price']);
            $stmt->bindParam(7, $data['stock_quantity']);
            $stmt->bindParam(8, $data['min_stock_level']);
            $stmt->bindParam(9, $data['unit']);
            $stmt->bindParam(10, $data['description']);
            $stmt->bindParam(11, $data['image']);
            $stmt->bindParam(12, $data['status']);
            
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function update($id, $data) {
        try {
            $query = "UPDATE " . $this->table . " 
                     SET code = ?, barcode = ?, name = ?, category_id = ?, purchase_price = ?, 
                     sale_price = ?, stock_quantity = ?, min_stock_level = ?, unit = ?, 
                     description = ?, status = ?";
            
            // Eğer yeni resim yüklendiyse
            if(!empty($data['image'])) {
                $query .= ", image = ?";
                $params = [
                    $data['code'], $data['barcode'], $data['name'], $data['category_id'], $data['purchase_price'],
                    $data['sale_price'], $data['stock_quantity'], $data['min_stock_level'], $data['unit'],
                    $data['description'], $data['status'], $data['image'], $id
                ];
            } else {
                $params = [
                    $data['code'], $data['barcode'], $data['name'], $data['category_id'], $data['purchase_price'],
                    $data['sale_price'], $data['stock_quantity'], $data['min_stock_level'], $data['unit'],
                    $data['description'], $data['status'], $id
                ];
            }
            
            $query .= " WHERE id = ?";
            $stmt = $this->db->prepare($query);
            
            for($i = 0; $i < count($params); $i++) {
                $stmt->bindParam($i+1, $params[$i]);
            }
            
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function delete($id) {
        try {
            // Ürünü kullanan satış/alım detaylarını kontrol et
            $query = "SELECT COUNT(*) as count FROM sale_details WHERE product_id = ? 
                     UNION ALL 
                     SELECT COUNT(*) as count FROM purchase_details WHERE product_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id);
            $stmt->bindParam(2, $id);
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if($results[0]['count'] > 0 || $results[1]['count'] > 0) {
                return false; // Ürünü kullanan kayıtlar varsa silme
            }
            
            // Ürünü sil
            $query = "DELETE FROM " . $this->table . " WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function updateStock($id, $quantity) {
        try {
            $query = "UPDATE " . $this->table . " SET stock_quantity = stock_quantity + ? WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $quantity);
            $stmt->bindParam(2, $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function getLowStock() {
        try {
            $query = "SELECT p.*, c.name as category_name 
                     FROM " . $this->table . " p
                     LEFT JOIN categories c ON p.category_id = c.id
                     WHERE p.stock_quantity <= p.min_stock_level AND p.status = 1
                     ORDER BY p.stock_quantity ASC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function searchProducts($keyword) {
        try {
            $keyword = "%{$keyword}%";
            $query = "SELECT p.*, c.name as category_name 
                     FROM " . $this->table . " p
                     LEFT JOIN categories c ON p.category_id = c.id
                     WHERE p.name LIKE ? OR p.code LIKE ? OR p.barcode LIKE ?
                     ORDER BY p.name ASC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $keyword);
            $stmt->bindParam(2, $keyword);
            $stmt->bindParam(3, $keyword);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function getByCategory($category_id) {
        try {
            $query = "SELECT p.*, c.name as category_name 
                     FROM " . $this->table . " p
                     LEFT JOIN categories c ON p.category_id = c.id
                     WHERE p.category_id = ?
                     ORDER BY p.name ASC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $category_id);
            $stmt->execute();
            return $stmt;
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
    
    public function generateProductCode() {
        try {
            $query = "SELECT MAX(CAST(SUBSTRING(code, 2) AS UNSIGNED)) as max_code FROM " . $this->table . " WHERE code LIKE 'P%'";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $next_code = (int)$result['max_code'] + 1;
            return 'P' . str_pad($next_code, 5, '0', STR_PAD_LEFT);
        } catch (PDOException $e) {
            return 'P00001'; // Varsayılan ilk kod
        }
    }
}