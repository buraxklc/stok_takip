<?php
// app/models/Sale.php

class Sale extends Model {
    protected $table = 'sales';
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getAll() {
        try {
            $query = "SELECT s.*, c.name as customer_name 
                     FROM " . $this->table . " s
                     LEFT JOIN customers c ON s.customer_id = c.id
                     ORDER BY s.id DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function getById($id) {
        try {
            $query = "SELECT s.*, c.name as customer_name, c.code as customer_code 
                     FROM " . $this->table . " s
                     LEFT JOIN customers c ON s.customer_id = c.id
                     WHERE s.id = ?";
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
            
            // Satış kaydı oluştur
            $query = "INSERT INTO " . $this->table . " (invoice_no, customer_id, sale_date, due_date, total_amount, discount_amount, tax_amount, net_amount, paid_amount, due_amount, payment_status, note, user_id) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            
            $stmt->bindParam(1, $data['invoice_no']);
            $stmt->bindParam(2, $data['customer_id']);
            $stmt->bindParam(3, $data['sale_date']);
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
            $sale_id = $this->db->lastInsertId();
            
            // Satış detaylarını kaydet
            foreach($data['items'] as $item) {
                $query = "INSERT INTO sale_details (sale_id, product_id, quantity, unit_price, discount_rate, tax_rate, total_amount) 
                         VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $this->db->prepare($query);
                
                $stmt->bindParam(1, $sale_id);
                $stmt->bindParam(2, $item['product_id']);
                $stmt->bindParam(3, $item['quantity']);
                $stmt->bindParam(4, $item['unit_price']);
                $stmt->bindParam(5, $item['discount_rate']);
                $stmt->bindParam(6, $item['tax_rate']);
                $stmt->bindParam(7, $item['total_amount']);
                
                $stmt->execute();
                
                // Ürün stok miktarını güncelle
                $query = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(1, $item['quantity']);
                $stmt->bindParam(2, $item['product_id']);
                $stmt->execute();
                
                // Stok hareketi ekle
                $query = "INSERT INTO stock_movements (product_id, quantity, movement_type, reference_type, reference_id, user_id) 
                         VALUES (?, ?, 'out', 'sale', ?, ?)";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(1, $item['product_id']);
                $stmt->bindParam(2, $item['quantity']);
                $stmt->bindParam(3, $sale_id);
                $stmt->bindParam(4, $data['user_id']);
                $stmt->execute();
            }
            
            // Müşteri bakiyesini güncelle (ödenmemiş tutar kadar)
            if($data['due_amount'] > 0) {
                $query = "UPDATE customers SET balance = balance + ? WHERE id = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(1, $data['due_amount']);
                $stmt->bindParam(2, $data['customer_id']);
                $stmt->execute();
                
                // Müşteri cari hesap hareketi ekle
                $query = "INSERT INTO customer_account_movements (customer_id, amount, movement_type, reference_type, reference_id, description, user_id) 
                         VALUES (?, ?, 'debit', 'sale', ?, ?, ?)";
                $stmt = $this->db->prepare($query);
                $debit_amount = $data['net_amount'];
                $description = "Satış: " . $data['invoice_no'];
                $stmt->bindParam(1, $data['customer_id']);
                $stmt->bindParam(2, $debit_amount);
                $stmt->bindParam(3, $sale_id);
                $stmt->bindParam(4, $description);
                $stmt->bindParam(5, $data['user_id']);
                $stmt->execute();
            }
            
            // Ön ödeme yapıldıysa müşteri cari hesap hareketine ekle
            if($data['paid_amount'] > 0) {
                $query = "INSERT INTO customer_account_movements (customer_id, amount, movement_type, reference_type, reference_id, description, user_id) 
                         VALUES (?, ?, 'credit', 'payment', ?, ?, ?)";
                $stmt = $this->db->prepare($query);
                $credit_amount = $data['paid_amount'];
                $description = "Ödeme: " . $data['invoice_no'];
                $stmt->bindParam(1, $data['customer_id']);
                $stmt->bindParam(2, $credit_amount);
                $stmt->bindParam(3, $sale_id);
                $stmt->bindParam(4, $description);
                $stmt->bindParam(5, $data['user_id']);
                $stmt->execute();
                
                // Müşteri ödemesi ekle
                $query = "INSERT INTO customer_payments (customer_id, sale_id, amount, payment_date, payment_method, notes, user_id) 
                         VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $this->db->prepare($query);
                $payment_date = date('Y-m-d');
                $payment_method = 'cash'; // Varsayılan ödeme yöntemi
                $notes = "Satış sırasında ön ödeme: " . $data['invoice_no'];
                $stmt->bindParam(1, $data['customer_id']);
                $stmt->bindParam(2, $sale_id);
                $stmt->bindParam(3, $credit_amount);
                $stmt->bindParam(4, $payment_date);
                $stmt->bindParam(5, $payment_method);
                $stmt->bindParam(6, $notes);
                $stmt->bindParam(7, $data['user_id']);
                $stmt->execute();
            }
            
            $this->db->commit();
            return $sale_id;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }
    
    public function getSaleDetails($sale_id) {
        try {
            $query = "SELECT sd.*, p.name as product_name, p.code as product_code, p.unit 
                     FROM sale_details sd
                     LEFT JOIN products p ON sd.product_id = p.id
                     WHERE sd.sale_id = ?
                     ORDER BY sd.id ASC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $sale_id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function delete($id) {
        try {
            $this->db->beginTransaction();
            
            // Satış detaylarını al
            $sale_details = $this->getSaleDetails($id);
            
            // Satış bilgilerini al
            $sale = $this->getById($id);
            
            if(!$sale) {
                return false;
            }
            
            // Stok hareketlerini sil
            $query = "DELETE FROM stock_movements WHERE reference_type = 'sale' AND reference_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            
            // Müşteri ödemelerini sil
            $query = "DELETE FROM customer_payments WHERE sale_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            
            // Müşteri cari hesap hareketlerini sil
            $query = "DELETE FROM customer_account_movements WHERE reference_type IN ('sale', 'payment') AND reference_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            
            // Müşteri bakiyesini güncelle
            if($sale['due_amount'] > 0) {
                $query = "UPDATE customers SET balance = balance - ? WHERE id = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(1, $sale['due_amount']);
                $stmt->bindParam(2, $sale['customer_id']);
                $stmt->execute();
            }
            
            // Ürün stoklarını geri yükle
            foreach($sale_details as $item) {
                $query = "UPDATE products SET stock_quantity = stock_quantity + ? WHERE id = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(1, $item['quantity']);
                $stmt->bindParam(2, $item['product_id']);
                $stmt->execute();
            }
            
            // Satış detaylarını sil
            $query = "DELETE FROM sale_details WHERE sale_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            
            // Satış kaydını sil
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
    
    public function getRecentSales($limit = 5) {
        try {
            $query = "SELECT s.*, c.name as customer_name 
                     FROM " . $this->table . " s
                     LEFT JOIN customers c ON s.customer_id = c.id
                     ORDER BY s.id DESC
                     LIMIT ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function getSalesByCustomer($customer_id) {
        try {
            $query = "SELECT s.*, c.name as customer_name 
                     FROM " . $this->table . " s
                     LEFT JOIN customers c ON s.customer_id = c.id
                     WHERE s.customer_id = ?
                     ORDER BY s.id DESC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $customer_id);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function getSalesByDateRange($start_date, $end_date) {
        try {
            $query = "SELECT s.*, c.name as customer_name 
                     FROM " . $this->table . " s
                     LEFT JOIN customers c ON s.customer_id = c.id
                     WHERE s.sale_date BETWEEN ? AND ?
                     ORDER BY s.id DESC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $start_date);
            $stmt->bindParam(2, $end_date);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function getMonthlySales() {
        try {
            $query = "SELECT DATE_FORMAT(sale_date, '%Y-%m') as month, 
                     SUM(net_amount) as total_amount,
                     COUNT(*) as sale_count
                     FROM " . $this->table . "
                     WHERE sale_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                     GROUP BY DATE_FORMAT(sale_date, '%Y-%m')
                     ORDER BY month ASC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function generateInvoiceNo() {
        try {
            $query = "SELECT MAX(CAST(SUBSTRING(invoice_no, 4) AS UNSIGNED)) as max_no FROM " . $this->table . " WHERE invoice_no LIKE 'INV%'";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $next_no = (int)$result['max_no'] + 1;
            return 'INV' . str_pad($next_no, 6, '0', STR_PAD_LEFT);
        } catch (PDOException $e) {
            return 'INV000001'; // Varsayılan ilk numara
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
    
    public function getTotalSalesAmount() {
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