<?php
// app/controllers/CustomerController.php

class CustomerController extends Controller {
    private $customerModel;
    
    public function __construct() {
        // Oturumu kontrol et
        if(!isset($_SESSION['user_id'])) {
            $this->redirect('/auth/login');
        }
        
        $this->customerModel = $this->model('Customer');
    }
    
    public function index() {
        // Tüm müşterileri getir
        $customers = $this->customerModel->getAll()->fetchAll(PDO::FETCH_ASSOC);
        
        // Müşteri listesini göster
        $this->view('customers/index', ['customers' => $customers]);
    }
    
    public function create() {
        // Yeni müşteri kodu oluştur
        $customer_code = $this->customerModel->generateCustomerCode();
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Form verilerini al
            $data = [
                'code' => trim($_POST['code']),
                'name' => trim($_POST['name']),
                'contact_person' => trim($_POST['contact_person']),
                'phone' => trim($_POST['phone']),
                'email' => trim($_POST['email']),
                'address' => trim($_POST['address']),
                'tax_office' => trim($_POST['tax_office']),
                'tax_number' => trim($_POST['tax_number']),
                'status' => isset($_POST['status']) ? 1 : 0
            ];
            
            // Müşteriyi oluştur
            if($this->customerModel->create($data)) {
                $_SESSION['success'] = "Müşteri başarıyla oluşturuldu.";
                $this->redirect('/customer');
            } else {
                $_SESSION['error'] = "Müşteri oluşturulurken bir hata oluştu.";
            }
        }
        
        // Müşteri oluşturma formunu göster
        $this->view('customers/create', [
            'customer_code' => $customer_code
        ]);
    }
    
    public function edit($id) {
        // Müşteriyi getir
        $customer = $this->customerModel->getById($id);
        
        if(!$customer) {
            $_SESSION['error'] = "Müşteri bulunamadı.";
            $this->redirect('/customer');
        }
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Form verilerini al
            $data = [
                'code' => trim($_POST['code']),
                'name' => trim($_POST['name']),
                'contact_person' => trim($_POST['contact_person']),
                'phone' => trim($_POST['phone']),
                'email' => trim($_POST['email']),
                'address' => trim($_POST['address']),
                'tax_office' => trim($_POST['tax_office']),
                'tax_number' => trim($_POST['tax_number']),
                'status' => isset($_POST['status']) ? 1 : 0
            ];
            
            // Müşteriyi güncelle
            if($this->customerModel->update($id, $data)) {
                $_SESSION['success'] = "Müşteri başarıyla güncellendi.";
                $this->redirect('/customer');
            } else {
                $_SESSION['error'] = "Müşteri güncellenirken bir hata oluştu.";
            }
        }
        
        // Müşteri düzenleme formunu göster
        $this->view('customers/edit', [
            'customer' => $customer
        ]);
    }
    
    // "view" metodunu "viewCustomer" olarak yeniden adlandırıyoruz
    public function viewCustomer($id) {
        // Müşteriyi getir
        $customer = $this->customerModel->getById($id);
        
        if(!$customer) {
            $_SESSION['error'] = "Müşteri bulunamadı.";
            $this->redirect('/customer');
        }
        
        // Müşteri detaylarını göster
        $this->view('customers/view', ['customer' => $customer]);
    }
    
    public function delete($id) {
        // Müşteriyi getir
        $customer = $this->customerModel->getById($id);
        
        if(!$customer) {
            $_SESSION['error'] = "Müşteri bulunamadı.";
            $this->redirect('/customer');
        }
        
        // Müşteriyi sil
        if($this->customerModel->delete($id)) {
            $_SESSION['success'] = "Müşteri başarıyla silindi.";
        } else {
            $_SESSION['error'] = "Müşteri silinemedi. Bu müşteri satış kayıtlarında kullanılıyor olabilir.";
        }
        
        $this->redirect('/customer');
    }
    
    public function search() {
        if(isset($_GET['keyword'])) {
            $keyword = trim($_GET['keyword']);
            
            // Müşterileri ara
            $customers = $this->customerModel->searchCustomers($keyword)->fetchAll(PDO::FETCH_ASSOC);
            
            // Arama sonuçlarını göster
            $this->view('customers/search', [
                'customers' => $customers,
                'keyword' => $keyword
            ]);
        } else {
            $this->redirect('/customer');
        }
    }
}