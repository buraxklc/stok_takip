<?php
// app/models/PurchaseDetail.php

class PurchaseDetail extends Model {
    protected $table = 'purchase_details';
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getByPurchaseId($purchase_id) {
        try {
            $query = "SELECT pd.*, p.name as product_name, p.code as product_code, p.unit 
                     FROM " . $this->table . " pd
                     LEFT JOIN products p ON pd.product_id = p.id
                     WHERE pd.purchase_id = ?
                     ORDER BY pd.id ASC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $purchase_id);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function create($data) {
        try {
            $query = "INSERT INTO " . $this->table . " (purchase_id, product_id, quantity, unit_price, discount_rate, tax_rate, total_amount) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            
            $stmt->bindParam(1, $data['purchase_id']);
            $stmt->bindParam(2, $data['product_id']);
            $stmt->bindParam(3, $data['quantity']);
            $stmt->bindParam(4, $data['unit_price']);
            $stmt->bindParam(5, $data['discount_rate']);
            $stmt->bindParam(6, $data['tax_rate']);
            $stmt->bindParam(7, $data['total_amount']);
            
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function deleteByPurchaseId($purchase_id) {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE purchase_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $purchase_id);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function getMostPurchasedProducts($limit = 10) {
        try {
            $query = "SELECT p.id, p.name, p.code, SUM(pd.quantity) as total_quantity, 
                     COUNT(DISTINCT pd.purchase_id) as purchase_count,
                     SUM(pd.total_amount) as total_amount
                     FROM " . $this->table . " pd
                     LEFT JOIN products p ON pd.product_id = p.id
                     GROUP BY p.id, p.name, p.code
                     ORDER BY total_quantity DESC
                     LIMIT ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }
}