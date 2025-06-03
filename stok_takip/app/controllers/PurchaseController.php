<?php
// app/controllers/PurchaseController.php

class PurchaseController extends Controller {
    private $purchaseModel;
    private $purchaseDetailModel;
    private $supplierModel;
    private $productModel;
    
    public function __construct() {
        // Oturumu kontrol et
        if(!isset($_SESSION['user_id'])) {
            $this->redirect('/auth/login');
        }
        
        $this->purchaseModel = $this->model('Purchase');
        $this->purchaseDetailModel = $this->model('PurchaseDetail');
        $this->supplierModel = $this->model('Supplier');
        $this->productModel = $this->model('Product');
    }
    
    public function index() {
        // Tüm alımları getir
        $purchases = $this->purchaseModel->getAll()->fetchAll(PDO::FETCH_ASSOC);
        
        // Alım listesini göster
        $this->view('purchases/index', ['purchases' => $purchases]);
    }
    
    public function create() {
        // Tedarikçileri getir
        $suppliers = $this->supplierModel->getAll()->fetchAll(PDO::FETCH_ASSOC);
        
        // Ürünleri getir
        $products = $this->productModel->getAll()->fetchAll(PDO::FETCH_ASSOC);
        
        // Yeni fatura numarası oluştur
        $invoice_no = $this->purchaseModel->generateInvoiceNo();
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Form verilerini al
            $supplier_id = $_POST['supplier_id'];
            $purchase_date = $_POST['purchase_date'];
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
            
            // Alım verisi
            $purchase_data = [
                'invoice_no' => $_POST['invoice_no'],
                'supplier_id' => $supplier_id,
                'purchase_date' => $purchase_date,
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
            
            // Alımı oluştur
            $purchase_id = $this->purchaseModel->create($purchase_data);
            
            if($purchase_id) {
                $_SESSION['success'] = "Alım başarıyla oluşturuldu.";
                $this->redirect('/purchase/viewPurchase/' . $purchase_id);
            } else {
                $_SESSION['error'] = "Alım oluşturulurken bir hata oluştu.";
            }
        }
        
        // Alım oluşturma formunu göster
        $this->view('purchases/create', [
            'suppliers' => $suppliers,
            'products' => $products,
            'invoice_no' => $invoice_no,
            'today' => date('Y-m-d')
        ]);
    }
    
    public function viewPurchase($id) {
        // Alımı getir
        $purchase = $this->purchaseModel->getById($id);
        
        if(!$purchase) {
            $_SESSION['error'] = "Alım bulunamadı.";
            $this->redirect('/purchase');
        }
        
        // Alım detaylarını getir
        $purchase_details = $this->purchaseDetailModel->getByPurchaseId($id)->fetchAll(PDO::FETCH_ASSOC);
        
        // Alım detaylarını göster
        $this->view('purchases/view', [
            'purchase' => $purchase,
            'purchase_details' => $purchase_details
        ]);
    }
    
    public function delete($id) {
        // Alımı getir
        $purchase = $this->purchaseModel->getById($id);
        
        if(!$purchase) {
            $_SESSION['error'] = "Alım bulunamadı.";
            $this->redirect('/purchase');
        }
        
        // Alımı sil
        if($this->purchaseModel->delete($id)) {
            $_SESSION['success'] = "Alım başarıyla silindi.";
        } else {
            $_SESSION['error'] = "Alım silinirken bir hata oluştu.";
        }
        
        $this->redirect('/purchase');
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