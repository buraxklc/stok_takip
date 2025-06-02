<?php
// app/models/SaleDetail.php

class SaleDetail extends Model {
    protected $table = 'sale_details';
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getBySaleId($sale_id) {
        try {
            $query = "SELECT sd.*, p.name as product_name, p.code as product_code, p.unit 
                     FROM " . $this->table . " sd
                     LEFT JOIN products p ON sd.product_id = p.id
                     WHERE sd.sale_id = ?
                     ORDER BY sd.id ASC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $sale_id);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function create($data) {
        try {
            $query = "INSERT INTO " . $this->table . " (sale_id, product_id, quantity, unit_price, discount_rate, tax_rate, total_amount) 
                     VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            
            $stmt->bindParam(1, $data['sale_id']);
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
    
    public function deleteBySaleId($sale_id) {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE sale_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $sale_id);
            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function getMostSoldProducts($limit = 10) {
        try {
            $query = "SELECT p.id, p.name, p.code, SUM(sd.quantity) as total_quantity, 
                     COUNT(DISTINCT sd.sale_id) as sale_count,
                     SUM(sd.total_amount) as total_amount
                     FROM " . $this->table . " sd
                     LEFT JOIN products p ON sd.product_id = p.id
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