<?php
// app/models/Purchase.php

class Purchase extends Model {
    protected $table = 'purchases';
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getAll() {
        try {
            $query = "SELECT p.*, s.name as supplier_name 
                     FROM " . $this->table . " p
                     LEFT JOIN suppliers s ON p.supplier_id = s.id
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
            $query = "SELECT p.*, s.name as supplier_name, s.code as supplier_code 
                     FROM " . $this->table . " p
                     LEFT JOIN suppliers s ON p.supplier_id = s.id
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
            $this->db->beginTransaction();
            
            // Alım kaydı oluştur
            $query = "INSERT INTO " . $this->table . " (invoice_no, supplier_id, purchase_date, due_date, total_amount, discount_amount, tax_amount, net_amount, paid_amount, due_amount, payment_status, note, user_id) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            
            $stmt->bindParam(1, $data['invoice_no']);
            $stmt->bindParam(2, $data['supplier_id']);
            $stmt->bindParam(3, $data['purchase_date']);
            $stmt->bindParam(4, $data['due_date']);
            $stmt->bindParam(5, $data['total_amount']);
            $stmt->bindParam(6, $data['discount_amount']);
            $stmt->bindParam(7, $data['tax_amount']);
            $stmt->bindParam(8, $data['net_amount']);
            $stmt->bindParam(9, $data['paid_amount']);
            $stmt->bindParam(10, $data['due_amount']);
            $stmt->bindParam(11, $data['payment_status']);
            $stmt->bindParam(12, $data['note']);
            $stmt->bindParam(13, $data['user_id']);
            
            $stmt->execute();
            $purchase_id = $this->db->lastInsertId();
            
            // Alım detaylarını kaydet
            foreach($data['items'] as $item) {
                $query = "INSERT INTO purchase_details (purchase_id, product_id, quantity, unit_price, discount_rate, tax_rate, total_amount) 
                         VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $this->db->prepare($query);
                
                $stmt->bindParam(1, $purchase_id);
                $stmt->bindParam(2, $item['product_id']);
                $stmt->bindParam(3, $item['quantity']);
                $stmt->bindParam(4, $item['unit_price']);
                $stmt->bindParam(5, $item['discount_rate']);
                $stmt->bindParam(6, $item['tax_rate']);
                $stmt->bindParam(7, $item['total_amount']);
                
                $stmt->execute();
                
                // Ürün stok miktarını güncelle
                $query = "UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(1, $item['quantity']);
                $stmt->bindParam(2, $item['product_id']);
                $stmt->execute();
                
                // Stok hareketi ekle
                $query = "INSERT INTO stock_movements (product_id, quantity, movement_type, reference_type, reference_id, user_id) 
                         VALUES (?, ?, 'in', 'purchase', ?, ?)";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(1, $item['product_id']);
                $stmt->bindParam(2, $item['quantity']);
                $stmt->bindParam(3, $purchase_id);
                $stmt->bindParam(4, $data['user_id']);
                $stmt->execute();
            }
            
            // Tedarikçi bakiyesini güncelle (ödenmemiş tutar kadar)
            if($data['due_amount'] > 0) {
                $query = "UPDATE suppliers SET balance = balance + ? WHERE id = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(1, $data['due_amount']);
                $stmt->bindParam(2, $data['supplier_id']);
                $stmt->execute();
                
                // Tedarikçi cari hesap hareketi ekle
                $query = "INSERT INTO supplier_account_movements (supplier_id, amount, movement_type, reference_type, reference_id, description, user_id) 
                         VALUES (?, ?, 'debit', 'purchase', ?, ?, ?)";
                $stmt = $this->db->prepare($query);
                $debit_amount = $data['net_amount'];
                $description = "Alım: " . $data['invoice_no'];
                $stmt->bindParam(1, $data['supplier_id']);
                $stmt->bindParam(2, $debit_amount);
                $stmt->bindParam(3, $purchase_id);
                $stmt->bindParam(4, $description);
                $stmt->bindParam(5, $data['user_id']);
                $stmt->execute();
            }
            
            // Ön ödeme yapıldıysa tedarikçi cari hesap hareketine ekle
            if($data['paid_amount'] > 0) {
                $query = "INSERT INTO supplier_account_movements (supplier_id, amount, movement_type, reference_type, reference_id, description, user_id) 
                         VALUES (?, ?, 'credit', 'payment', ?, ?, ?)";
                $stmt = $this->db->prepare($query);
                $credit_amount = $data['paid_amount'];
                $description = "Ödeme: " . $data['invoice_no'];
                $stmt->bindParam(1, $data['supplier_id']);
                $stmt->bindParam(2, $credit_amount);
                $stmt->bindParam(3, $purchase_id);
                $stmt->bindParam(4, $description);
                $stmt->bindParam(5, $data['user_id']);
                $stmt->execute();
                
                // Tedarikçi ödemesi ekle
                $query = "INSERT INTO supplier_payments (supplier_id, purchase_id, amount, payment_date, payment_method, notes, user_id) 
                         VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $this->db->prepare($query);
                $payment_date = date('Y-m-d');
                $payment_method = 'cash'; // Varsayılan ödeme yöntemi
                $notes = "Alım sırasında ön ödeme: " . $data['invoice_no'];
                $stmt->bindParam(1, $data['supplier_id']);
                $stmt->bindParam(2, $purchase_id);
                $stmt->bindParam(3, $credit_amount);
                $stmt->bindParam(4, $payment_date);
                $stmt->bindParam(5, $payment_method);
                $stmt->bindParam(6, $notes);
                $stmt->bindParam(7, $data['user_id']);
                $stmt->execute();
            }
            
            $this->db->commit();
            return $purchase_id;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }
    
    public function getPurchaseDetails($purchase_id) {
        try {
            $query = "SELECT pd.*, p.name as product_name, p.code as product_code, p.unit 
                     FROM purchase_details pd
                     LEFT JOIN products p ON pd.product_id = p.id
                     WHERE pd.purchase_id = ?
                     ORDER BY pd.id ASC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $purchase_id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function delete($id) {
        try {
            $this->db->beginTransaction();
            
            // Alım detaylarını al
            $purchase_details = $this->getPurchaseDetails($id);
            
            // Alım bilgilerini al
            $purchase = $this->getById($id);
            
            if(!$purchase) {
                return false;
            }
            
            // Stok hareketlerini sil
            $query = "DELETE FROM stock_movements WHERE reference_type = 'purchase' AND reference_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            
            // Tedarikçi ödemelerini sil
            $query = "DELETE FROM supplier_payments WHERE purchase_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            
            // Tedarikçi cari hesap hareketlerini sil
            $query = "DELETE FROM supplier_account_movements WHERE reference_type IN ('purchase', 'payment') AND reference_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            
            // Tedarikçi bakiyesini güncelle
            if($purchase['due_amount'] > 0) {
                $query = "UPDATE suppliers SET balance = balance - ? WHERE id = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(1, $purchase['due_amount']);
                $stmt->bindParam(2, $purchase['supplier_id']);
                $stmt->execute();
            }
            
            // Ürün stoklarını güncelle
            foreach($purchase_details as $item) {
                $query = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(1, $item['quantity']);
                $stmt->bindParam(2, $item['product_id']);
                $stmt->execute();
            }
            
            // Alım detaylarını sil
            $query = "DELETE FROM purchase_details WHERE purchase_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            
            // Alım kaydını sil
            $query = "DELETE FROM " . $this->table . " WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }
    
    public function getRecentPurchases($limit = 5) {
        try {
            $query = "SELECT p.*, s.name as supplier_name 
                     FROM " . $this->table . " p
                     LEFT JOIN suppliers s ON p.supplier_id = s.id
                     ORDER BY p.id DESC
                     LIMIT ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function getPurchasesBySupplier($supplier_id) {
        try {
            $query = "SELECT p.*, s.name as supplier_name 
                     FROM " . $this->table . " p
                     LEFT JOIN suppliers s ON p.supplier_id = s.id
                     WHERE p.supplier_id = ?
                     ORDER BY p.id DESC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $supplier_id);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function getPurchasesByDateRange($start_date, $end_date) {
        try {
            $query = "SELECT p.*, s.name as supplier_name 
                     FROM " . $this->table . " p
                     LEFT JOIN suppliers s ON p.supplier_id = s.id
                     WHERE p.purchase_date BETWEEN ? AND ?
                     ORDER BY p.id DESC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $start_date);
            $stmt->bindParam(2, $end_date);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function generateInvoiceNo() {
        try {
            $query = "SELECT MAX(CAST(SUBSTRING(invoice_no, 4) AS UNSIGNED)) as max_no FROM " . $this->table . " WHERE invoice_no LIKE 'PUR%'";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $next_no = (int)$result['max_no'] + 1;
            return 'PUR' . str_pad($next_no, 6, '0', STR_PAD_LEFT);
        } catch (PDOException $e) {
            return 'PUR000001'; // Varsayılan ilk numara
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
    
    public function getTotalPurchasesAmount() {
        try {
            $query = "SELECT SUM(net_amount) as total FROM " . $this->table;
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'] ? $row['total'] : 0;
        } catch (PDOException $e) {
            return 0;
        }
    }
}