<?php
// app/models/SupplierPayment.php

class SupplierPayment extends Model {
    protected $table = 'supplier_payments';
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getAll() {
        try {
            $query = "SELECT sp.*, s.name as supplier_name, s.code as supplier_code, p.invoice_no 
                     FROM " . $this->table . " sp
                     LEFT JOIN suppliers s ON sp.supplier_id = s.id
                     LEFT JOIN purchases p ON sp.purchase_id = p.id
                     ORDER BY sp.payment_date DESC, sp.id DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function getById($id) {
        try {
            $query = "SELECT sp.*, s.name as supplier_name, s.code as supplier_code, p.invoice_no 
                     FROM " . $this->table . " sp
                     LEFT JOIN suppliers s ON sp.supplier_id = s.id
                     LEFT JOIN purchases p ON sp.purchase_id = p.id
                     WHERE sp.id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function getBySupplierId($supplier_id) {
        try {
            $query = "SELECT sp.*, s.name as supplier_name, s.code as supplier_code, p.invoice_no 
                     FROM " . $this->table . " sp
                     LEFT JOIN suppliers s ON sp.supplier_id = s.id
                     LEFT JOIN purchases p ON sp.purchase_id = p.id
                     WHERE sp.supplier_id = ?
                     ORDER BY sp.payment_date DESC, sp.id DESC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $supplier_id);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }
    
         public function getByPurchaseId($purchase_id) {
        try {
            $query = "SELECT sp.*, s.name as supplier_name, s.code as supplier_code, p.invoice_no 
                     FROM " . $this->table . " sp
                     LEFT JOIN suppliers s ON sp.supplier_id = s.id
                     LEFT JOIN purchases p ON sp.purchase_id = p.id
                     WHERE sp.purchase_id = ?
                     ORDER BY sp.payment_date DESC, sp.id DESC";
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
            $this->db->beginTransaction();
            
            // Ödeme kaydı oluştur
            $query = "INSERT INTO " . $this->table . " (supplier_id, purchase_id, amount, payment_date, payment_method, reference_no, notes, user_id) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            
            $stmt->bindParam(1, $data['supplier_id']);
            $stmt->bindParam(2, $data['purchase_id']);
            $stmt->bindParam(3, $data['amount']);
            $stmt->bindParam(4, $data['payment_date']);
            $stmt->bindParam(5, $data['payment_method']);
            $stmt->bindParam(6, $data['reference_no']);
            $stmt->bindParam(7, $data['notes']);
            $stmt->bindParam(8, $data['user_id']);
            
            $stmt->execute();
            $payment_id = $this->db->lastInsertId();
            
            // Tedarikçi cari hesap hareketi ekle
            $query = "INSERT INTO supplier_account_movements (supplier_id, amount, movement_type, reference_type, reference_id, description, user_id) 
                     VALUES (?, ?, 'credit', 'payment', ?, ?, ?)";
            $stmt = $this->db->prepare($query);
            $description = "Ödeme: " . ($data['reference_no'] ? $data['reference_no'] : date('d.m.Y', strtotime($data['payment_date'])));
            
            $stmt->bindParam(1, $data['supplier_id']);
            $stmt->bindParam(2, $data['amount']);
            $stmt->bindParam(3, $payment_id);
            $stmt->bindParam(4, $description);
            $stmt->bindParam(5, $data['user_id']);
            
            $stmt->execute();
            
            // Tedarikçi bakiyesini güncelle
            $query = "UPDATE suppliers SET balance = balance - ? WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $data['amount']);
            $stmt->bindParam(2, $data['supplier_id']);
            $stmt->execute();
            
            // Eğer alım kaydı varsa, alım kaydının ödeme durumunu güncelle
            if($data['purchase_id']) {
                // Alım kaydını getir
                $query = "SELECT * FROM purchases WHERE id = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(1, $data['purchase_id']);
                $stmt->execute();
                $purchase = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($purchase) {
                    // Toplam ödenen tutarı hesapla
                    $query = "SELECT SUM(amount) as total_paid FROM " . $this->table . " WHERE purchase_id = ?";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(1, $data['purchase_id']);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $total_paid = $result['total_paid'];
                    $due_amount = $purchase['net_amount'] - $total_paid;
                    
                    // Ödeme durumu
                    if($due_amount <= 0) {
                        $payment_status = 'paid';
                    } else {
                        $payment_status = 'partially_paid';
                    }
                    
                    // Alım kaydını güncelle
                    $query = "UPDATE purchases SET paid_amount = ?, due_amount = ?, payment_status = ? WHERE id = ?";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(1, $total_paid);
                    $stmt->bindParam(2, $due_amount);
                    $stmt->bindParam(3, $payment_status);
                    $stmt->bindParam(4, $data['purchase_id']);
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
            
            // Tedarikçi cari hesap hareketini sil
            $query = "DELETE FROM supplier_account_movements WHERE reference_type = 'payment' AND reference_id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            
            // Tedarikçi bakiyesini güncelle
            $query = "UPDATE suppliers SET balance = balance + ? WHERE id = ?";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $payment['amount']);
            $stmt->bindParam(2, $payment['supplier_id']);
            $stmt->execute();
            
            // Eğer alım kaydı varsa, alım kaydının ödeme durumunu güncelle
            if($payment['purchase_id']) {
                // Alım kaydını getir
                $query = "SELECT * FROM purchases WHERE id = ?";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(1, $payment['purchase_id']);
                $stmt->execute();
                $purchase = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($purchase) {
                    // Toplam ödenen tutarı hesapla
                    $query = "SELECT SUM(amount) as total_paid FROM " . $this->table . " WHERE purchase_id = ? AND id != ?";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(1, $payment['purchase_id']);
                    $stmt->bindParam(2, $id);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    $total_paid = $result['total_paid'] ?: 0;
                    $due_amount = $purchase['net_amount'] - $total_paid;
                    
                    // Ödeme durumu
                    if($total_paid <= 0) {
                        $payment_status = 'unpaid';
                    } elseif($due_amount <= 0) {
                        $payment_status = 'paid';
                    } else {
                        $payment_status = 'partially_paid';
                    }
                    
                    // Alım kaydını güncelle
                    $query = "UPDATE purchases SET paid_amount = ?, due_amount = ?, payment_status = ? WHERE id = ?";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(1, $total_paid);
                    $stmt->bindParam(2, $due_amount);
                    $stmt->bindParam(3, $payment_status);
                    $stmt->bindParam(4, $payment['purchase_id']);
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
            $query = "SELECT sp.*, s.name as supplier_name, s.code as supplier_code, p.invoice_no 
                     FROM " . $this->table . " sp
                     LEFT JOIN suppliers s ON sp.supplier_id = s.id
                     LEFT JOIN purchases p ON sp.purchase_id = p.id
                     ORDER BY sp.id DESC
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
            $query = "SELECT sp.*, s.name as supplier_name, s.code as supplier_code, p.invoice_no 
                     FROM " . $this->table . " sp
                     LEFT JOIN suppliers s ON sp.supplier_id = s.id
                     LEFT JOIN purchases p ON sp.purchase_id = p.id
                     WHERE sp.payment_date BETWEEN ? AND ?
                     ORDER BY sp.payment_date DESC, sp.id DESC";
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