<?php
// app/models/Report.php

class Report extends Model {
    protected $db;
    
    public function __construct() {
        parent::__construct();
    }
    
    // Satış raporları için sorgu
    public function getSalesReport($start_date, $end_date) {
        try {
            $query = "SELECT s.*, c.name as customer_name, c.code as customer_code, u.name as user_name
                     FROM sales s
                     LEFT JOIN customers c ON s.customer_id = c.id
                     LEFT JOIN users u ON s.user_id = u.id
                     WHERE s.sale_date BETWEEN ? AND ?
                     ORDER BY s.sale_date DESC, s.id DESC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $start_date);
            $stmt->bindParam(2, $end_date);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Alım raporları için sorgu
    public function getPurchasesReport($start_date, $end_date) {
        try {
            $query = "SELECT p.*, s.name as supplier_name, s.code as supplier_code, u.name as user_name
                     FROM purchases p
                     LEFT JOIN suppliers s ON p.supplier_id = s.id
                     LEFT JOIN users u ON p.user_id = u.id
                     WHERE p.purchase_date BETWEEN ? AND ?
                     ORDER BY p.purchase_date DESC, p.id DESC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $start_date);
            $stmt->bindParam(2, $end_date);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Kâr/zarar raporu için sorgu
    public function getProfitReport($start_date, $end_date) {
        try {
            $query = "SELECT s.id, s.invoice_no, s.sale_date, s.customer_id, c.name as customer_name,
                     sd.product_id, p.name as product_name, p.code as product_code, 
                     sd.quantity, sd.unit_price, sd.discount_rate, sd.tax_rate, sd.total_amount,
                     p.purchase_price as cost_price
                     FROM sales s
                     LEFT JOIN customers c ON s.customer_id = c.id
                     LEFT JOIN sale_details sd ON s.id = sd.sale_id
                     LEFT JOIN products p ON sd.product_id = p.id
                     WHERE s.sale_date BETWEEN ? AND ?
                     ORDER BY s.sale_date DESC, s.id DESC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $start_date);
            $stmt->bindParam(2, $end_date);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Stok raporu için sorgu
    public function getStockReport() {
        try {
            $query = "SELECT p.*, c.name as category_name,
                     (p.stock_quantity * p.purchase_price) as stock_value
                     FROM products p
                     LEFT JOIN categories c ON p.category_id = c.id
                     ORDER BY p.name ASC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Ödeme raporu için sorgu - Müşteri ödemeleri
    public function getCustomerPaymentsReport($start_date, $end_date) {
        try {
            $query = "SELECT cp.*, c.name as customer_name, c.code as customer_code, s.invoice_no,
                     u.name as user_name
                     FROM customer_payments cp
                     LEFT JOIN customers c ON cp.customer_id = c.id
                     LEFT JOIN sales s ON cp.sale_id = s.id
                     LEFT JOIN users u ON cp.user_id = u.id
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
    
    // Ödeme raporu için sorgu - Tedarikçi ödemeleri
    public function getSupplierPaymentsReport($start_date, $end_date) {
        try {
            $query = "SELECT sp.*, s.name as supplier_name, s.code as supplier_code, p.invoice_no,
                     u.name as user_name
                     FROM supplier_payments sp
                     LEFT JOIN suppliers s ON sp.supplier_id = s.id
                     LEFT JOIN purchases p ON sp.purchase_id = p.id
                     LEFT JOIN users u ON sp.user_id = u.id
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
    
    // Müşteri raporu için sorgu
    public function getCustomersReport() {
        try {
            $query = "SELECT c.*, 
                     (SELECT COUNT(*) FROM sales WHERE customer_id = c.id) as total_sales,
                     (SELECT SUM(net_amount) FROM sales WHERE customer_id = c.id) as total_amount,
                     (SELECT SUM(paid_amount) FROM sales WHERE customer_id = c.id) as total_paid,
                     (SELECT SUM(due_amount) FROM sales WHERE customer_id = c.id) as total_due
                     FROM customers c
                     ORDER BY c.name ASC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Tedarikçi raporu için sorgu
    public function getSuppliersReport() {
        try {
            $query = "SELECT s.*, 
                     (SELECT COUNT(*) FROM purchases WHERE supplier_id = s.id) as total_purchases,
                     (SELECT SUM(net_amount) FROM purchases WHERE supplier_id = s.id) as total_amount,
                     (SELECT SUM(paid_amount) FROM purchases WHERE supplier_id = s.id) as total_paid,
                     (SELECT SUM(due_amount) FROM purchases WHERE supplier_id = s.id) as total_due
                     FROM suppliers s
                     ORDER BY s.name ASC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Aylık satış raporu için sorgu
    public function getMonthlySalesReport($year) {
        try {
            $query = "SELECT MONTH(sale_date) as month, 
                     COUNT(*) as total_sales,
                     SUM(total_amount) as total_amount,
                     SUM(discount_amount) as total_discount,
                     SUM(tax_amount) as total_tax,
                     SUM(net_amount) as total_net_amount,
                     SUM(paid_amount) as total_paid,
                     SUM(due_amount) as total_due
                     FROM sales
                     WHERE YEAR(sale_date) = ?
                     GROUP BY MONTH(sale_date)
                     ORDER BY MONTH(sale_date) ASC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $year);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Aylık alım raporu için sorgu
    public function getMonthlyPurchasesReport($year) {
        try {
            $query = "SELECT MONTH(purchase_date) as month, 
                     COUNT(*) as total_purchases,
                     SUM(total_amount) as total_amount,
                     SUM(discount_amount) as total_discount,
                     SUM(tax_amount) as total_tax,
                     SUM(net_amount) as total_net_amount,
                     SUM(paid_amount) as total_paid,
                     SUM(due_amount) as total_due
                     FROM purchases
                     WHERE YEAR(purchase_date) = ?
                     GROUP BY MONTH(purchase_date)
                     ORDER BY MONTH(purchase_date) ASC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(1, $year);
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            return false;
        }
    }
}