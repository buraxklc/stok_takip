<?php
// app/controllers/ReportController.php

class ReportController extends Controller {
    private $productModel;
    private $categoryModel;
    private $customerModel;
    private $supplierModel;
    private $saleModel;
    private $saleDetailModel;
    private $purchaseModel;
    private $purchaseDetailModel;
    private $customerPaymentModel;
    private $supplierPaymentModel;
    
    public function __construct() {
        // Oturumu kontrol et
        if(!isset($_SESSION['user_id'])) {
            $this->redirect('/auth/login');
        }
        
        $this->productModel = $this->model('Product');
        $this->categoryModel = $this->model('Category');
        $this->customerModel = $this->model('Customer');
        $this->supplierModel = $this->model('Supplier');
        $this->saleModel = $this->model('Sale');
        $this->saleDetailModel = $this->model('SaleDetail');
        $this->purchaseModel = $this->model('Purchase');
        $this->purchaseDetailModel = $this->model('PurchaseDetail');
        $this->customerPaymentModel = $this->model('CustomerPayment');
        $this->supplierPaymentModel = $this->model('SupplierPayment');
    }
    
    public function index() {
        // Ana rapor sayfasına yönlendir
        $this->view('reports/index');
    }
    
    public function stock() {
        // Stok durumu raporu
        $products = $this->productModel->getAll()->fetchAll(PDO::FETCH_ASSOC);
        $categories = $this->categoryModel->getAll()->fetchAll(PDO::FETCH_ASSOC);
        
        $total_stock_value = 0;
        $low_stock_count = 0;
        
        // Toplam stok değerini ve kritik stok sayısını hesapla
        foreach($products as $product) {
            $total_stock_value += $product['stock_quantity'] * $product['purchase_price'];
            
            if($product['stock_quantity'] <= $product['min_stock_level']) {
                $low_stock_count++;
            }
        }
        
        $this->view('reports/stock', [
            'products' => $products,
            'categories' => $categories,
            'total_stock_value' => $total_stock_value,
            'low_stock_count' => $low_stock_count
        ]);
    }
    
    public function sales() {
        // Varsayılan olarak bugünün tarihini al
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d');
        $date_filter = 'today';
        
        if(isset($_GET['date_filter'])) {
            $date_filter = $_GET['date_filter'];
            
            switch($date_filter) {
                case 'today':
                    $start_date = date('Y-m-d');
                    $end_date = date('Y-m-d');
                    break;
                case 'yesterday':
                    $start_date = date('Y-m-d', strtotime('-1 day'));
                    $end_date = date('Y-m-d', strtotime('-1 day'));
                    break;
                case 'this_week':
                    $start_date = date('Y-m-d', strtotime('monday this week'));
                    $end_date = date('Y-m-d', strtotime('sunday this week'));
                    break;
                case 'last_week':
                    $start_date = date('Y-m-d', strtotime('monday last week'));
                    $end_date = date('Y-m-d', strtotime('sunday last week'));
                    break;
                case 'this_month':
                    $start_date = date('Y-m-01');
                    $end_date = date('Y-m-t');
                    break;
                case 'last_month':
                    $start_date = date('Y-m-01', strtotime('-1 month'));
                    $end_date = date('Y-m-t', strtotime('-1 month'));
                    break;
                case 'this_year':
                    $start_date = date('Y-01-01');
                    $end_date = date('Y-12-31');
                    break;
                case 'last_year':
                    $start_date = date('Y-01-01', strtotime('-1 year'));
                    $end_date = date('Y-12-31', strtotime('-1 year'));
                    break;
                case 'custom':
                    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d');
                    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
                    break;
                default:
                    $start_date = date('Y-m-d');
                    $end_date = date('Y-m-d');
                    break;
            }
        }
        
        // Belirtilen tarih aralığındaki satışları getir
        $sales = $this->saleModel->getSalesByDateRange($start_date, $end_date)->fetchAll(PDO::FETCH_ASSOC);
        
        // Satış istatistiklerini hesapla
        $total_sales = count($sales);
        $total_amount = 0;
        $total_discount = 0;
        $total_tax = 0;
        $total_net_amount = 0;
        $total_paid = 0;
        $total_due = 0;
        
        foreach($sales as $sale) {
            $total_amount += $sale['total_amount'];
            $total_discount += $sale['discount_amount'];
            $total_tax += $sale['tax_amount'];
            $total_net_amount += $sale['net_amount'];
            $total_paid += $sale['paid_amount'];
            $total_due += $sale['due_amount'];
        }
        
        // En çok satılan ürünleri getir (tarih filtresi uygulama)
        $top_selling_products = $this->saleDetailModel->getMostSoldProducts(10)->fetchAll(PDO::FETCH_ASSOC);
        
        $this->view('reports/sales', [
            'sales' => $sales,
            'date_filter' => $date_filter,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'total_sales' => $total_sales,
            'total_amount' => $total_amount,
            'total_discount' => $total_discount,
            'total_tax' => $total_tax,
            'total_net_amount' => $total_net_amount,
            'total_paid' => $total_paid,
            'total_due' => $total_due,
            'top_selling_products' => $top_selling_products
        ]);
    }
    
    public function purchases() {
        // Varsayılan olarak bugünün tarihini al
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d');
        $date_filter = 'today';
        
        if(isset($_GET['date_filter'])) {
            $date_filter = $_GET['date_filter'];
            
            switch($date_filter) {
                case 'today':
                    $start_date = date('Y-m-d');
                    $end_date = date('Y-m-d');
                    break;
                case 'yesterday':
                    $start_date = date('Y-m-d', strtotime('-1 day'));
                    $end_date = date('Y-m-d', strtotime('-1 day'));
                    break;
                case 'this_week':
                    $start_date = date('Y-m-d', strtotime('monday this week'));
                    $end_date = date('Y-m-d', strtotime('sunday this week'));
                    break;
                case 'last_week':
                    $start_date = date('Y-m-d', strtotime('monday last week'));
                    $end_date = date('Y-m-d', strtotime('sunday last week'));
                    break;
                case 'this_month':
                    $start_date = date('Y-m-01');
                    $end_date = date('Y-m-t');
                    break;
                case 'last_month':
                    $start_date = date('Y-m-01', strtotime('-1 month'));
                    $end_date = date('Y-m-t', strtotime('-1 month'));
                    break;
                case 'this_year':
                    $start_date = date('Y-01-01');
                    $end_date = date('Y-12-31');
                    break;
                case 'last_year':
                    $start_date = date('Y-01-01', strtotime('-1 year'));
                    $end_date = date('Y-12-31', strtotime('-1 year'));
                    break;
                case 'custom':
                    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d');
                    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
                    break;
                default:
                    $start_date = date('Y-m-d');
                    $end_date = date('Y-m-d');
                    break;
            }
        }
        
        // Belirtilen tarih aralığındaki alımları getir
        $purchases = $this->purchaseModel->getPurchasesByDateRange($start_date, $end_date)->fetchAll(PDO::FETCH_ASSOC);
        
        // Alım istatistiklerini hesapla
        $total_purchases = count($purchases);
        $total_amount = 0;
        $total_discount = 0;
        $total_tax = 0;
        $total_net_amount = 0;
        $total_paid = 0;
        $total_due = 0;
        
        foreach($purchases as $purchase) {
            $total_amount += $purchase['total_amount'];
            $total_discount += $purchase['discount_amount'];
            $total_tax += $purchase['tax_amount'];
            $total_net_amount += $purchase['net_amount'];
            $total_paid += $purchase['paid_amount'];
            $total_due += $purchase['due_amount'];
        }
        
        // En çok alınan ürünleri getir
        $top_purchased_products = $this->purchaseDetailModel->getMostPurchasedProducts(10)->fetchAll(PDO::FETCH_ASSOC);
        
        $this->view('reports/purchases', [
            'purchases' => $purchases,
            'date_filter' => $date_filter,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'total_purchases' => $total_purchases,
            'total_amount' => $total_amount,
            'total_discount' => $total_discount,
            'total_tax' => $total_tax,
            'total_net_amount' => $total_net_amount,
            'total_paid' => $total_paid,
            'total_due' => $total_due,
            'top_purchased_products' => $top_purchased_products
        ]);
    }
    
    public function profit() {
        // Varsayılan olarak bugünün tarihini al
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d');
        $date_filter = 'today';
        
        if(isset($_GET['date_filter'])) {
            $date_filter = $_GET['date_filter'];
            
            switch($date_filter) {
                case 'today':
                    $start_date = date('Y-m-d');
                    $end_date = date('Y-m-d');
                    break;
                case 'yesterday':
                    $start_date = date('Y-m-d', strtotime('-1 day'));
                    $end_date = date('Y-m-d', strtotime('-1 day'));
                    break;
                case 'this_week':
                    $start_date = date('Y-m-d', strtotime('monday this week'));
                    $end_date = date('Y-m-d', strtotime('sunday this week'));
                    break;
                case 'last_week':
                    $start_date = date('Y-m-d', strtotime('monday last week'));
                    $end_date = date('Y-m-d', strtotime('sunday last week'));
                    break;
                case 'this_month':
                    $start_date = date('Y-m-01');
                    $end_date = date('Y-m-t');
                    break;
                case 'last_month':
                    $start_date = date('Y-m-01', strtotime('-1 month'));
                    $end_date = date('Y-m-t', strtotime('-1 month'));
                    break;
                case 'this_year':
                    $start_date = date('Y-01-01');
                    $end_date = date('Y-12-31');
                    break;
                case 'last_year':
                    $start_date = date('Y-01-01', strtotime('-1 year'));
                    $end_date = date('Y-12-31', strtotime('-1 year'));
                    break;
                case 'custom':
                    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d');
                    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
                    break;
                default:
                    $start_date = date('Y-m-d');
                    $end_date = date('Y-m-d');
                    break;
            }
        }
        
        // Belirtilen tarih aralığındaki satışları getir
        $sales = $this->saleModel->getSalesByDateRange($start_date, $end_date)->fetchAll(PDO::FETCH_ASSOC);
        
        // Satış detaylarını getir
        $sale_details = [];
        foreach($sales as $sale) {
            $details = $this->saleDetailModel->getBySaleId($sale['id'])->fetchAll(PDO::FETCH_ASSOC);
            $sale_details[$sale['id']] = $details;
        }
        
        // Kâr/zarar hesaplaması için değişkenler
        $total_sales = 0;
        $total_cost = 0;
        $total_profit = 0;
        $total_profit_percentage = 0;
        
        // Satış ve kâr detaylarını hesapla
        $profit_details = [];
        
        foreach($sales as $sale) {
            $sale_id = $sale['id'];
            $total_sales += $sale['net_amount'];
            
            if(isset($sale_details[$sale_id])) {
                $sale_cost = 0;
                
                foreach($sale_details[$sale_id] as $detail) {
                    // Ürün bilgilerini getir
                    $product = $this->productModel->getById($detail['product_id']);
                    
                    if($product) {
                        $item_cost = $detail['quantity'] * $product['purchase_price'];
                        $item_revenue = $detail['total_amount'];
                        $item_profit = $item_revenue - $item_cost;
                        $item_profit_percentage = ($item_profit / $item_cost) * 100;
                        
                        $sale_cost += $item_cost;
                        
                        $profit_details[] = [
                            'sale_id' => $sale_id,
                            'invoice_no' => $sale['invoice_no'],
                            'sale_date' => $sale['sale_date'],
                            'product_id' => $detail['product_id'],
                            'product_name' => $detail['product_name'],
                            'quantity' => $detail['quantity'],
                            'cost_price' => $product['purchase_price'],
                            'sale_price' => $detail['unit_price'],
                            'total_cost' => $item_cost,
                            'total_revenue' => $item_revenue,
                            'profit' => $item_profit,
                            'profit_percentage' => $item_profit_percentage
                        ];
                    }
                }
                
                $total_cost += $sale_cost;
            }
        }
        
        $total_profit = $total_sales - $total_cost;
        
        if($total_cost > 0) {
            $total_profit_percentage = ($total_profit / $total_cost) * 100;
        }
        
        $this->view('reports/profit', [
            'date_filter' => $date_filter,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'profit_details' => $profit_details,
            'total_sales' => $total_sales,
            'total_cost' => $total_cost,
            'total_profit' => $total_profit,
            'total_profit_percentage' => $total_profit_percentage
        ]);
    }
    
    public function payments() {
        // Varsayılan olarak bugünün tarihini al
        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d');
        $date_filter = 'today';
        
        if(isset($_GET['date_filter'])) {
            $date_filter = $_GET['date_filter'];
            
            switch($date_filter) {
                case 'today':
                    $start_date = date('Y-m-d');
                    $end_date = date('Y-m-d');
                    break;
                case 'yesterday':
                    $start_date = date('Y-m-d', strtotime('-1 day'));
                    $end_date = date('Y-m-d', strtotime('-1 day'));
                    break;
                case 'this_week':
                    $start_date = date('Y-m-d', strtotime('monday this week'));
                    $end_date = date('Y-m-d', strtotime('sunday this week'));
                    break;
                case 'last_week':
                    $start_date = date('Y-m-d', strtotime('monday last week'));
                    $end_date = date('Y-m-d', strtotime('sunday last week'));
                    break;
                case 'this_month':
                    $start_date = date('Y-m-01');
                    $end_date = date('Y-m-t');
                    break;
                case 'last_month':
                    $start_date = date('Y-m-01', strtotime('-1 month'));
                    $end_date = date('Y-m-t', strtotime('-1 month'));
                    break;
                case 'this_year':
                    $start_date = date('Y-01-01');
                    $end_date = date('Y-12-31');
                    break;
                case 'last_year':
                    $start_date = date('Y-01-01', strtotime('-1 year'));
                    $end_date = date('Y-12-31', strtotime('-1 year'));
                    break;
                case 'custom':
                    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d');
                    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
                    break;
                default:
                    $start_date = date('Y-m-d');
                    $end_date = date('Y-m-d');
                    break;
            }
        }
        
        // Belirtilen tarih aralığındaki ödemeleri getir
        $customer_payments = $this->customerPaymentModel->getPaymentsByDateRange($start_date, $end_date)->fetchAll(PDO::FETCH_ASSOC);
        $supplier_payments = $this->supplierPaymentModel->getPaymentsByDateRange($start_date, $end_date)->fetchAll(PDO::FETCH_ASSOC);
        
        // Ödeme istatistiklerini hesapla
        $total_customer_payments = 0;
        $total_supplier_payments = 0;
        
        foreach($customer_payments as $payment) {
            $total_customer_payments += $payment['amount'];
        }
        
        foreach($supplier_payments as $payment) {
            $total_supplier_payments += $payment['amount'];
        }
        
        $payment_methods = [
            'cash' => ['customer' => 0, 'supplier' => 0],
            'bank_transfer' => ['customer' => 0, 'supplier' => 0],
            'credit_card' => ['customer' => 0, 'supplier' => 0],
            'check' => ['customer' => 0, 'supplier' => 0],
            'other' => ['customer' => 0, 'supplier' => 0]
        ];
        
        foreach($customer_payments as $payment) {
            if(isset($payment_methods[$payment['payment_method']])) {
                $payment_methods[$payment['payment_method']]['customer'] += $payment['amount'];
            } else {
                $payment_methods['other']['customer'] += $payment['amount'];
            }
        }
        
        foreach($supplier_payments as $payment) {
            if(isset($payment_methods[$payment['payment_method']])) {
                $payment_methods[$payment['payment_method']]['supplier'] += $payment['amount'];
            } else {
                $payment_methods['other']['supplier'] += $payment['amount'];
            }
        }
        
        $this->view('reports/payments', [
            'date_filter' => $date_filter,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'customer_payments' => $customer_payments,
            'supplier_payments' => $supplier_payments,
            'total_customer_payments' => $total_customer_payments,
            'total_supplier_payments' => $total_supplier_payments,
            'payment_methods' => $payment_methods
        ]);
    }
    
    public function customers() {
        // Tüm müşterileri getir
        $customers = $this->customerModel->getAll()->fetchAll(PDO::FETCH_ASSOC);
        
        // Müşteri istatistiklerini hesapla
        $total_customers = count($customers);
        $total_balance = 0;
        $active_customers = 0;
        
        foreach($customers as $customer) {
            $total_balance += $customer['balance'];
            
            if($customer['status'] == 1) {
                $active_customers++;
            }
        }
        
        // Müşterileri borç durumuna göre sırala
        usort($customers, function($a, $b) {
            return $b['balance'] - $a['balance'];
        });
        
        $this->view('reports/customers', [
            'customers' => $customers,
            'total_customers' => $total_customers,
            'total_balance' => $total_balance,
            'active_customers' => $active_customers
        ]);
    }
    
    public function suppliers() {
        // Tüm tedarikçileri getir
        $suppliers = $this->supplierModel->getAll()->fetchAll(PDO::FETCH_ASSOC);
        
        // Tedarikçi istatistiklerini hesapla
        $total_suppliers = count($suppliers);
        $total_balance = 0;
        $active_suppliers = 0;
        
        foreach($suppliers as $supplier) {
            $total_balance += $supplier['balance'];
            
            if($supplier['status'] == 1) {
                $active_suppliers++;
            }
        }
        
        // Tedarikçileri borç durumuna göre sırala
        usort($suppliers, function($a, $b) {
            return $b['balance'] - $a['balance'];
        });
        
        $this->view('reports/suppliers', [
            'suppliers' => $suppliers,
            'total_suppliers' => $total_suppliers,
            'total_balance' => $total_balance,
            'active_suppliers' => $active_suppliers
        ]);
    }
    
    public function generatePdf($report_type = null) {
        // PDF oluşturma işlemleri
        // Bu kısım için bir PDF kütüphanesi (örn. TCPDF, FPDF, Dompdf) kullanılabilir
        // Bu örnek içinde boş bir yöntem olarak bırakılmıştır
    }
    
    public function generateExcel($report_type = null) {
        // Excel oluşturma işlemleri
        // Bu kısım için bir Excel kütüphanesi (örn. PHPExcel, PhpSpreadsheet) kullanılabilir
        // Bu örnek içinde boş bir yöntem olarak bırakılmıştır
    }
}