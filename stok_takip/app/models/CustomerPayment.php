<?php
// app/models/CustomerPayment.php

class CustomerPayment extends Model {
    protected $table = 'customer_payments';
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getAll() {
        try {
            $query = "SELECT cp.*, c.name as customer_name, c.code as customer_code, s.invoice_no 
                     FROM " . $this->table . " cp
                     LEFT JOIN customers c ON cp.customer_id = c.id
                     LEFT JOIN sales s ON cp.sale_id = s.id
                     ORDER BY cp.payment_date DESC, cp.id DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function getById($id) {
        try {
            $query = "SELECT cp.*, c.name as customer_name, c.code as customer_code, s.invoice_no 
                     FROM " . $this->table . " cp
                     LEFT JOIN customers c ON cp.customer_id = c.id
                     LEFT JOIN sales s ON cp.sale_id = s.id
                     WHERE cp.id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function getByCustomerId($customer_id) {
        try {
            $query = "SELECT cp.*, c.name as customer_name, c.code as customer_code, s.invoice_no 
                     FROM " . $this->table . " cp
                     LEFT JOIN customers c ON cp.customer_id = c.id
                     LEFT JOIN sales s ON cp.sale_id = s.id
                     WHERE cp.customer_id = ?
                     ORDER BY cp.payment_date DESC, cp.id DESC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $customer_id);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function getBySaleId($sale_id) {
        try {
            $query = "SELECT cp.*, c.name as customer_name, c.code as customer_code, s.invoice_no 
                     FROM " . $this->table . " cp
                     LEFT JOIN customers c ON cp.customer_id = c.id
                     LEFT JOIN sales s ON cp.sale_id = s.id
                     WHERE cp.sale_id = ?
                     ORDER BY cp.payment_date DESC, cp.id DESC";
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
            $this->db->beginTransaction();
            
            // Ödeme kaydı oluştur
            $query = "INSERT INTO " . $this->table . " (customer_id, sale_id, amount, payment_date, payment_method, reference_no, notes, user_id) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            
            $stmt->bindParam(1, $data['customer_id']);
            $stmt->bindParam(2, $data['sale_id']);
            $stmt->bindParam(3, $data['amount']);
            $stmt->bindParam(4, $data['payment_date']);
            $stmt->bindParam(5, $data['payment_method']);
            $stmt->bindParam(6, $data['reference_no']);
            $stmt->bindParam(7, $data['notes']);
            $stmt->bindParam(8, $data['user_id']);
            
            $stmt->execute();
            $payment_id = $this->db->lastInsertId();
            
            // Müşteri cari hesap hareketi ekle
            $query = "INSERT INTO customer_account_movements (customer_id, amount, movement_type, reference_type, reference_id, description, user_id) 
                     VALUES (?, ?, 'credit', 'payment', ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            $description = "Tahsilat: " . ($data['reference_no'] ? $data['reference_no'] : date('d.m.Y', strtotime($data['payment_date'])));
            
            $stmt->bindParam(1, $data['customer_id']);
            $stmt->bindParam(2, $data['amount']);
            $stmt->bindParam(3, $payment_id);
            $stmt->bindParam(4, $description);
            $stmt->bindParam(5, $data['user_id']);
            
            $stmt->execute();
            
            // Müşteri bakiyesini güncelle
            $query = "UPDATE customers SET balance = balance - ? WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $data['amount']);
            $stmt->bindParam(2, $data['customer_id']);
            $stmt->execute();
            
            // Eğer satış kaydı varsa, satış kaydının ödeme durumunu güncelle
            if($data['sale_id']) {
                // Satış kaydını getir
                $query = "SELECT * FROM sales WHERE id = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(1, $data['sale_id']);
                $stmt->execute();
                $sale = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($sale) {
                    // Toplam ödenen tutarı hesapla
                    $query = "SELECT SUM(amount) as total_paid FROM " . $this->table . " WHERE sale_id = ?";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(1, $data['sale_id']);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $total_paid = $result['total_paid'];
                    $due_amount = $sale['net_amount'] - $total_paid;
                    
                    // Ödeme durumu
                    if($due_amount <= 0) {
                        $payment_status = 'paid';
                    } else {
                        $payment_status = 'partially_paid';
                    }
                    
                    // Satış kaydını güncelle
                    $query = "UPDATE sales SET paid_amount = ?, due_amount = ?, payment_status = ? WHERE id = ?";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(1, $total_paid);
                    $stmt->bindParam(2, $due_amount);
                    $stmt->bindParam(3, $payment_status);
                    $stmt->bindParam(4, $data['sale_id']);
                    $stmt->execute();
                }
            }
            
            $this->db->commit();
            return $payment_id;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }
    
    public function delete($id) {
        try {
            $this->db->beginTransaction();
            
            // Ödeme kaydını getir
            $payment = $this->getById($id);
            
            if(!$payment) {
                return false;
            }
            
            // Müşteri cari hesap hareketini sil
            $query = "DELETE FROM customer_account_movements WHERE reference_type = 'payment' AND reference_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            
            // Müşteri bakiyesini güncelle
            $query = "UPDATE customers SET balance = balance + ? WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $payment['amount']);
            $stmt->bindParam(2, $payment['customer_id']);
            $stmt->execute();
            
            // Eğer satış kaydı varsa, satış kaydının ödeme durumunu güncelle
            if($payment['sale_id']) {
                // Satış kaydını getir
                $query = "SELECT * FROM sales WHERE id = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(1, $payment['sale_id']);
                $stmt->execute();
                $sale = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($sale) {
                    // Toplam ödenen tutarı hesapla
                    $query = "SELECT SUM(amount) as total_paid FROM " . $this->table . " WHERE sale_id = ? AND id != ?";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(1, $payment['sale_id']);
                    $stmt->bindParam(2, $id);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $total_paid = $result['total_paid'] ?: 0;
                    $due_amount = $sale['net_amount'] - $total_paid;
                    
                    // Ödeme durumu
                    if($total_paid <= 0) {
                        $payment_status = 'unpaid';
                    } elseif($due_amount <= 0) {
                        $payment_status = 'paid';
                    } else {
                        $payment_status = 'partially_paid';
                    }
                    
                    // Satış kaydını güncelle
                    $query = "UPDATE sales SET paid_amount = ?, due_amount = ?, payment_status = ? WHERE id = ?";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(1, $total_paid);
                    $stmt->bindParam(2, $due_amount);
                    $stmt->bindParam(3, $payment_status);
                    $stmt->bindParam(4, $payment['sale_id']);
                    $stmt->execute();
                }
            }
            
            // Ödeme kaydını sil
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
    
    public function getRecentPayments($limit = 5) {
        try {
            $query = "SELECT cp.*, c.name as customer_name, c.code as customer_code, s.invoice_no 
                     FROM " . $this->table . " cp
                     LEFT JOIN customers c ON cp.customer_id = c.id
                     LEFT JOIN sales s ON cp.sale_id = s.id
                     ORDER BY cp.id DESC
                     LIMIT ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function getPaymentsByDateRange($start_date, $end_date) {
        try {
            $query = "SELECT cp.*, c.name as customer_name, c.code as customer_code, s.invoice_no 
                     FROM " . $this->table . " cp
                     LEFT JOIN customers c ON cp.customer_id = c.id
                     LEFT JOIN sales s ON cp.sale_id = s.id
                     WHERE cp.payment_date BETWEEN ? AND ?
                     ORDER BY cp.payment_date DESC, cp.id DESC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $start_date);
            $stmt->bindParam(2, $end_date);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function getTotalPaymentsAmount() {
        try {
            $query = "SELECT SUM(amount) as total FROM " . $this->table;
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['total'] ? $row['total'] : 0;
        } catch (PDOException $e) {
            return 0;
        }
    }
    
    public function getTable() {
        return $this->table;
    }
    
    public function getDb() {
        return $this->db;
    }
}