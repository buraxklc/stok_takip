<?php
// app/controllers/PaymentController.php

class PaymentController extends Controller {
    private $customerPaymentModel;
    private $supplierPaymentModel;
    private $customerModel;
    private $supplierModel;
    private $saleModel;
    private $purchaseModel;
    
    public function __construct() {
        // Oturumu kontrol et
        if(!isset($_SESSION['user_id'])) {
            $this->redirect('/auth/login');
        }
        
        $this->customerPaymentModel = $this->model('CustomerPayment');
        $this->supplierPaymentModel = $this->model('SupplierPayment');
        $this->customerModel = $this->model('Customer');
        $this->supplierModel = $this->model('Supplier');
        $this->saleModel = $this->model('Sale');
        $this->purchaseModel = $this->model('Purchase');
    }
    
    public function index() {
        // Varsayılan olarak müşteri ödemeleri (tahsilatlar) sayfasına yönlendir
        $this->redirect('/payment/customer');
    }
    
    public function customer() {
        // Tüm müşteri ödemelerini getir
        $payments = $this->customerPaymentModel->getAll()->fetchAll(PDO::FETCH_ASSOC);
        
        // Ödeme listesini göster
        $this->view('payments/customer_index', ['payments' => $payments]);
    }
    
    public function createCustomerPayment() {
        // Müşterileri getir
        $customers = $this->customerModel->getAll()->fetchAll(PDO::FETCH_ASSOC);
        
        // Satışları getir
        $sales = $this->saleModel->getAll()->fetchAll(PDO::FETCH_ASSOC);
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Form verilerini al
            $customer_id = $_POST['customer_id'];
            $sale_id = !empty($_POST['sale_id']) ? $_POST['sale_id'] : null;
            $amount = floatval($_POST['amount']);
            $payment_date = $_POST['payment_date'];
            $payment_method = $_POST['payment_method'];
            $reference_no = $_POST['reference_no'];
            $notes = $_POST['notes'];
            
            // Ödeme verisi
            $payment_data = [
                'customer_id' => $customer_id,
                'sale_id' => $sale_id,
                'amount' => $amount,
                'payment_date' => $payment_date,
                'payment_method' => $payment_method,
                'reference_no' => $reference_no,
                'notes' => $notes,
                'user_id' => $_SESSION['user_id']
            ];
            
            // Ödemeyi oluştur
            if($this->customerPaymentModel->create($payment_data)) {
                $_SESSION['success'] = "Ödeme başarıyla kaydedildi.";
                $this->redirect('/payment/customer');
            } else {
                $_SESSION['error'] = "Ödeme kaydedilirken bir hata oluştu.";
            }
        }
        
        // Ödeme oluşturma formunu göster
        $this->view('payments/customer_create', [
            'customers' => $customers,
            'sales' => $sales,
            'today' => date('Y-m-d')
        ]);
    }
    
    public function viewCustomerPayment($id) {
        // Ödemeyi getir
        $payment = $this->customerPaymentModel->getById($id);
        
        if(!$payment) {
            $_SESSION['error'] = "Ödeme bulunamadı.";
            $this->redirect('/payment/customer');
        }
        
        // Ödeme detaylarını göster
        $this->view('payments/customer_view', ['payment' => $payment]);
    }
    
    public function deleteCustomerPayment($id) {
        // Ödemeyi getir
        $payment = $this->customerPaymentModel->getById($id);
        
        if(!$payment) {
            $_SESSION['error'] = "Ödeme bulunamadı.";
            $this->redirect('/payment/customer');
        }
        
        // Ödemeyi sil
        if($this->customerPaymentModel->delete($id)) {
            $_SESSION['success'] = "Ödeme başarıyla silindi.";
        } else {
            $_SESSION['error'] = "Ödeme silinirken bir hata oluştu.";
        }
        
        $this->redirect('/payment/customer');
    }

    public function searchCustomerPayments() {
        if(isset($_GET['keyword'])) {
            $keyword = trim($_GET['keyword']);
            
            // Müşteri ödemelerini ara
            $query = "SELECT cp.*, c.name as customer_name, c.code as customer_code, s.invoice_no 
                    FROM " . $this->customerPaymentModel->getTable() . " cp
                    LEFT JOIN customers c ON cp.customer_id = c.id
                    LEFT JOIN sales s ON cp.sale_id = s.id
                    WHERE c.name LIKE ? OR c.code LIKE ? OR s.invoice_no LIKE ? OR cp.reference_no LIKE ?
                    ORDER BY cp.payment_date DESC, cp.id DESC";
            $stmt = $this->customerPaymentModel->getDb()->prepare($query);
            $keyword = "%{$keyword}%";
            $stmt->bindParam(1, $keyword);
            $stmt->bindParam(2, $keyword);
            $stmt->bindParam(3, $keyword);
            $stmt->bindParam(4, $keyword);
            $stmt->execute();
            $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Arama sonuçlarını göster
            $this->view('payments/customer_search', [
                'payments' => $payments,
                'keyword' => $_GET['keyword']
            ]);
        } else {
            $this->redirect('/payment/customer');
        }
    }
    
    public function getCustomerDueAmount() {
        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['customer_id'])) {
            $customer_id = $_POST['customer_id'];
            
            // Müşteriyi getir
            $customer = $this->customerModel->getById($customer_id);
            
            if($customer) {
                // JSON formatında müşteri bakiyesini döndür
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'due_amount' => floatval($customer['balance'])
                ]);
                exit;
            } else {
                // Hata mesajı döndür
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Müşteri bulunamadı.']);
                exit;
            }
        }
        
        // Geçersiz istek
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Geçersiz istek.']);
        exit;
    }
    
    public function getSaleDetails() {
        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['sale_id'])) {
            $sale_id = $_POST['sale_id'];
            
            // Satışı getir
            $sale = $this->saleModel->getById($sale_id);
            
            if($sale) {
                // JSON formatında satış detaylarını döndür
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'sale' => [
                        'id' => $sale['id'],
                        'invoice_no' => $sale['invoice_no'],
                        'customer_id' => $sale['customer_id'],
                        'customer_name' => $sale['customer_name'],
                        'net_amount' => floatval($sale['net_amount']),
                        'paid_amount' => floatval($sale['paid_amount']),
                        'due_amount' => floatval($sale['due_amount'])
                    ]
                ]);
                exit;
            } else {
                // Hata mesajı döndür
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Satış bulunamadı.']);
                exit;
            }
        }
        
        // Geçersiz istek
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Geçersiz istek.']);
        exit;
    }
    
    // Tedarikçi Ödemeleri
    public function supplier() {
        // Tüm tedarikçi ödemelerini getir
        $payments = $this->supplierPaymentModel->getAll()->fetchAll(PDO::FETCH_ASSOC);
        
        // Ödeme listesini göster
        $this->view('payments/supplier_index', ['payments' => $payments]);
    }
    
    public function createSupplierPayment() {
        // Tedarikçileri getir
        $suppliers = $this->supplierModel->getAll()->fetchAll(PDO::FETCH_ASSOC);
        
        // Alımları getir
        $purchases = $this->purchaseModel->getAll()->fetchAll(PDO::FETCH_ASSOC);
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Form verilerini al
            $supplier_id = $_POST['supplier_id'];
            $purchase_id = !empty($_POST['purchase_id']) ? $_POST['purchase_id'] : null;
            $amount = floatval($_POST['amount']);
            $payment_date = $_POST['payment_date'];
            $payment_method = $_POST['payment_method'];
            $reference_no = $_POST['reference_no'];
            $notes = $_POST['notes'];
            
            // Ödeme verisi
            $payment_data = [
                'supplier_id' => $supplier_id,
                'purchase_id' => $purchase_id,
                'amount' => $amount,
                'payment_date' => $payment_date,
                'payment_method' => $payment_method,
                'reference_no' => $reference_no,
                'notes' => $notes,
                'user_id' => $_SESSION['user_id']
            ];
            
            // Ödemeyi oluştur
            if($this->supplierPaymentModel->create($payment_data)) {
                $_SESSION['success'] = "Ödeme başarıyla kaydedildi.";
                $this->redirect('/payment/supplier');
            } else {
                $_SESSION['error'] = "Ödeme kaydedilirken bir hata oluştu.";
            }
        }
        
        // Ödeme oluşturma formunu göster
        $this->view('payments/supplier_create', [
            'suppliers' => $suppliers,
            'purchases' => $purchases,
            'today' => date('Y-m-d')
        ]);
    }
    
    public function viewSupplierPayment($id) {
        // Ödemeyi getir
        $payment = $this->supplierPaymentModel->getById($id);
        
        if(!$payment) {
            $_SESSION['error'] = "Ödeme bulunamadı.";
            $this->redirect('/payment/supplier');
        }
        
        // Ödeme detaylarını göster
        $this->view('payments/supplier_view', ['payment' => $payment]);
    }
    
    public function deleteSupplierPayment($id) {
        // Ödemeyi getir
        $payment = $this->supplierPaymentModel->getById($id);
        
        if(!$payment) {
            $_SESSION['error'] = "Ödeme bulunamadı.";
            $this->redirect('/payment/supplier');
        }
        
        // Ödemeyi sil
        if($this->supplierPaymentModel->delete($id)) {
            $_SESSION['success'] = "Ödeme başarıyla silindi.";
        } else {
            $_SESSION['error'] = "Ödeme silinirken bir hata oluştu.";
        }
        
        $this->redirect('/payment/supplier');
    }
    
    public function searchSupplierPayments() {
        if(isset($_GET['keyword'])) {
            $keyword = trim($_GET['keyword']);
            
            // Tedarikçi ödemelerini ara
            $query = "SELECT sp.*, s.name as supplier_name, s.code as supplier_code, p.invoice_no 
                     FROM " . $this->supplierPaymentModel->getTable() . " sp
                     LEFT JOIN suppliers s ON sp.supplier_id = s.id
                     LEFT JOIN purchases p ON sp.purchase_id = p.id
                     WHERE s.name LIKE ? OR s.code LIKE ? OR p.invoice_no LIKE ? OR sp.reference_no LIKE ?
                     ORDER BY sp.payment_date DESC, sp.id DESC";
            $stmt = $this->supplierPaymentModel->getDb()->prepare($query);
            $keyword = "%{$keyword}%";
            $stmt->bindParam(1, $keyword);
            $stmt->bindParam(2, $keyword);
            $stmt->bindParam(3, $keyword);
            $stmt->bindParam(4, $keyword);
            $stmt->execute();
            $payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Arama sonuçlarını göster
            $this->view('payments/supplier_search', [
                'payments' => $payments,
                'keyword' => $_GET['keyword']
            ]);
        } else {
            $this->redirect('/payment/supplier');
        }
    }
    
    public function getSupplierDueAmount() {
        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['supplier_id'])) {
            $supplier_id = $_POST['supplier_id'];
            
            // Tedarikçiyi getir
            $supplier = $this->supplierModel->getById($supplier_id);
            
            if($supplier) {
                // JSON formatında tedarikçi bakiyesini döndür
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'due_amount' => floatval($supplier['balance'])
                ]);
                exit;
            } else {
                // Hata mesajı döndür
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Tedarikçi bulunamadı.']);
                exit;
            }
        }
        
        // Geçersiz istek
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Geçersiz istek.']);
        exit;
    }
    
    public function getPurchaseDetails() {
        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['purchase_id'])) {
            $purchase_id = $_POST['purchase_id'];
            
            // Alımı getir
            $purchase = $this->purchaseModel->getById($purchase_id);
            
            if($purchase) {
                // JSON formatında alım detaylarını döndür
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'purchase' => [
                        'id' => $purchase['id'],
                        'invoice_no' => $purchase['invoice_no'],
                        'supplier_id' => $purchase['supplier_id'],
                        'supplier_name' => $purchase['supplier_name'],
                        'net_amount' => floatval($purchase['net_amount']),
                        'paid_amount' => floatval($purchase['paid_amount']),
                        'due_amount' => floatval($purchase['due_amount'])
                    ]
                ]);
                exit;
            } else {
                // Hata mesajı döndür
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Alım bulunamadı.']);
                exit;
            }
        }
        
        // Geçersiz istek
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Geçersiz istek.']);
        exit;
    }
}