<?php
// app/controllers/SaleController.php

class SaleController extends Controller {
    private $saleModel;
    private $saleDetailModel;
    private $customerModel;
    private $productModel;
    
    public function __construct() {
        // Oturumu kontrol et
        if(!isset($_SESSION['user_id'])) {
            $this->redirect('/auth/login');
        }
        
        $this->saleModel = $this->model('Sale');
        $this->saleDetailModel = $this->model('SaleDetail');
        $this->customerModel = $this->model('Customer');
        $this->productModel = $this->model('Product');
    }
    
    public function index() {
        // Tüm satışları getir
        $sales = $this->saleModel->getAll()->fetchAll(PDO::FETCH_ASSOC);
        
        // Satış listesini göster
        $this->view('sales/index', ['sales' => $sales]);
    }
    
    public function create() {
        // Müşterileri getir
        $customers = $this->customerModel->getAll()->fetchAll(PDO::FETCH_ASSOC);
        
        // Ürünleri getir
        $products = $this->productModel->getAll()->fetchAll(PDO::FETCH_ASSOC);
        
        // Yeni fatura numarası oluştur
        $invoice_no = $this->saleModel->generateInvoiceNo();
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Form verilerini al
            $customer_id = $_POST['customer_id'];
            $sale_date = $_POST['sale_date'];
            $due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : null;
            $note = $_POST['note'];
            
            // Toplam tutarları hesapla
            $total_amount = 0;
            $total_discount = 0;
            $total_tax = 0;
            
            // Ürün detayları
            $items = [];
            
            for($i = 0; $i < count($_POST['product_id']); $i++) {
                $product_id = $_POST['product_id'][$i];
                $quantity = $_POST['quantity'][$i];
                $unit_price = $_POST['unit_price'][$i];
                $discount_rate = $_POST['discount_rate'][$i];
                $tax_rate = $_POST['tax_rate'][$i];
                
                // Sıfır miktarlı ürünleri atla
                if($quantity <= 0) {
                    continue;
                }
                
                // Ürün toplam tutarını hesapla
                $subtotal = $quantity * $unit_price;
                $discount_amount = $subtotal * ($discount_rate / 100);
                $after_discount = $subtotal - $discount_amount;
                $tax_amount = $after_discount * ($tax_rate / 100);
                $item_total = $after_discount + $tax_amount;
                
                // Genel toplamları güncelle
                $total_amount += $subtotal;
                $total_discount += $discount_amount;
                $total_tax += $tax_amount;
                
                // Ürün detaylarını diziye ekle
                $items[] = [
                    'product_id' => $product_id,
                    'quantity' => $quantity,
                    'unit_price' => $unit_price,
                    'discount_rate' => $discount_rate,
                    'tax_rate' => $tax_rate,
                    'total_amount' => $item_total
                ];
            }
            
            // Net tutar
            $net_amount = $total_amount - $total_discount + $total_tax;
            
            // Ödeme tutarı
            $paid_amount = isset($_POST['paid_amount']) ? floatval($_POST['paid_amount']) : 0;
            
            // Kalan tutar
            $due_amount = $net_amount - $paid_amount;
            
            // Ödeme durumu
            if($paid_amount <= 0) {
                $payment_status = 'unpaid';
            } elseif($paid_amount < $net_amount) {
                $payment_status = 'partially_paid';
            } else {
                $payment_status = 'paid';
            }
            
            // Satış verisi
            $sale_data = [
                'invoice_no' => $_POST['invoice_no'],
                'customer_id' => $customer_id,
                'sale_date' => $sale_date,
                'due_date' => $due_date,
                'total_amount' => $total_amount,
                'discount_amount' => $total_discount,
                'tax_amount' => $total_tax,
                'net_amount' => $net_amount,
                'paid_amount' => $paid_amount,
                'due_amount' => $due_amount,
                'payment_status' => $payment_status,
                'note' => $note,
                'user_id' => $_SESSION['user_id'],
                'items' => $items
            ];
            
            // Satışı oluştur
            $sale_id = $this->saleModel->create($sale_data);
            
            if($sale_id) {
                $_SESSION['success'] = "Satış başarıyla oluşturuldu.";
                $this->redirect('/sale/viewSale/' . $sale_id);
            } else {
                $_SESSION['error'] = "Satış oluşturulurken bir hata oluştu.";
            }
        }
        
        // Satış oluşturma formunu göster
        $this->view('sales/create', [
            'customers' => $customers,
            'products' => $products,
            'invoice_no' => $invoice_no,
            'today' => date('Y-m-d')
        ]);
    }
    
    public function viewSale($id) {
        // Satışı getir
        $sale = $this->saleModel->getById($id);
        
        if(!$sale) {
            $_SESSION['error'] = "Satış bulunamadı.";
            $this->redirect('/sale');
        }
        
        // Satış detaylarını getir
        $sale_details = $this->saleDetailModel->getBySaleId($id)->fetchAll(PDO::FETCH_ASSOC);
        
        // Satış detaylarını göster
        $this->view('sales/view', [
            'sale' => $sale,
            'sale_details' => $sale_details
        ]);
    }
    
    public function delete($id) {
        // Satışı getir
        $sale = $this->saleModel->getById($id);
        
        if(!$sale) {
            $_SESSION['error'] = "Satış bulunamadı.";
            $this->redirect('/sale');
        }
        
        // Satışı sil
        if($this->saleModel->delete($id)) {
            $_SESSION['success'] = "Satış başarıyla silindi.";
        } else {
            $_SESSION['error'] = "Satış silinirken bir hata oluştu.";
        }
        
        $this->redirect('/sale');
    }
    
    public function getProductDetails() {
        if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
            $product_id = $_POST['product_id'];
            
            // Ürün detaylarını getir
            $product = $this->productModel->getById($product_id);
            
            if($product) {
                // JSON formatında ürün detaylarını döndür
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'product' => [
                        'id' => $product['id'],
                        'name' => $product['name'],
                        'code' => $product['code'],
                        'stock_quantity' => $product['stock_quantity'],
                        'unit' => $product['unit'],
                        'purchase_price' => $product['purchase_price'],
                        'sale_price' => $product['sale_price']
                    ]
                ]);
                exit;
            } else {
                // Hata mesajı döndür
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Ürün bulunamadı.']);
                exit;
            }
        }
        
        // Geçersiz istek
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Geçersiz istek.']);
        exit;
    }
}