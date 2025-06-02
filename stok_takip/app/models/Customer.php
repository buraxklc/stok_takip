<?php
// app/models/Customer.php

class Customer extends Model {
    protected $table = 'customers';
    
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
    
    public function create($data) {
        try {
            $query = "INSERT INTO " . $this->table . " (code, name, contact_person, phone, email, address, tax_office, tax_number, status) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            
            $stmt->bindParam(1, $data['code']);
            $stmt->bindParam(2, $data['name']);
            $stmt->bindParam(3, $data['contact_person']);
            $stmt->bindParam(4, $data['phone']);
            $stmt->bindParam(5, $data['email']);
            $stmt->bindParam(6, $data['address']);
            $stmt->bindParam(7, $data['tax_office']);
            $stmt->bindParam(8, $data['tax_number']);
            $stmt->bindParam(9, $data['status']);
            
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function update($id, $data) {
        try {
            $query = "UPDATE " . $this->table . " 
                     SET code = ?, name = ?, contact_person = ?, phone = ?, email = ?, 
                     address = ?, tax_office = ?, tax_number = ?, status = ? 
                     WHERE id = ?";
            $stmt = $this->db->prepare($query);
            
            $stmt->bindParam(1, $data['code']);
            $stmt->bindParam(2, $data['name']);
            $stmt->bindParam(3, $data['contact_person']);
            $stmt->bindParam(4, $data['phone']);
            $stmt->bindParam(5, $data['email']);
            $stmt->bindParam(6, $data['address']);
            $stmt->bindParam(7, $data['tax_office']);
            $stmt->bindParam(8, $data['tax_number']);
            $stmt->bindParam(9, $data['status']);
            $stmt->bindParam(10, $id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function delete($id) {
        try {
            // Müşteriyi kullanan satış kayıtlarını kontrol et
            $query = "SELECT COUNT(*) as count FROM sales WHERE customer_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($result['count'] > 0) {
                return false; // Müşteriyi kullanan satış kayıtları varsa silme
            }
            
            // Müşteriyi sil
            $query = "DELETE FROM " . $this->table . " WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function searchCustomers($keyword) {
        try {
            $keyword = "%{$keyword}%";
            $query = "SELECT * FROM " . $this->table . " 
                     WHERE name LIKE ? OR code LIKE ? OR phone LIKE ? OR email LIKE ?
                     ORDER BY name ASC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $keyword);
            $stmt->bindParam(2, $keyword);
            $stmt->bindParam(3, $keyword);
            $stmt->bindParam(4, $keyword);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function getCustomerBalance($id) {
        try {
            $query = "SELECT balance FROM " . $this->table . " WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['balance'] : 0;
        } catch (PDOException $e) {
            return 0;
        }
    }
    
    public function updateBalance($id, $amount) {
        try {
            $query = "UPDATE " . $this->table . " SET balance = balance + ? WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $amount);
            $stmt->bindParam(2, $id);
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
    
    public function generateCustomerCode() {
        try {
            $query = "SELECT MAX(CAST(SUBSTRING(code, 2) AS UNSIGNED)) as max_code FROM " . $this->table . " WHERE code LIKE 'C%'";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $next_code = (int)$result['max_code'] + 1;
            return 'C' . str_pad($next_code, 5, '0', STR_PAD_LEFT);
        } catch (PDOException $e) {
            return 'C00001'; // Varsayılan ilk kod
        }
    }
}