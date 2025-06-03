<?php
// app/controllers/SupplierController.php

class SupplierController extends Controller {
    private $supplierModel;
    
    public function __construct() {
        // Oturumu kontrol et
        if(!isset($_SESSION['user_id'])) {
            $this->redirect('/auth/login');
        }
        
        $this->supplierModel = $this->model('Supplier');
    }
    
    public function index() {
        // Tüm tedarikçileri getir
        $suppliers = $this->supplierModel->getAll()->fetchAll(PDO::FETCH_ASSOC);
        
        // Tedarikçi listesini göster
        $this->view('suppliers/index', ['suppliers' => $suppliers]);
    }
    
    public function create() {
        // Yeni tedarikçi kodu oluştur
        $supplier_code = $this->supplierModel->generateSupplierCode();
        
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
            
            // Tedarikçiyi oluştur
            if($this->supplierModel->create($data)) {
                $_SESSION['success'] = "Tedarikçi başarıyla oluşturuldu.";
                $this->redirect('/supplier');
            } else {
                $_SESSION['error'] = "Tedarikçi oluşturulurken bir hata oluştu.";
            }
        }
        
        // Tedarikçi oluşturma formunu göster
        $this->view('suppliers/create', [
            'supplier_code' => $supplier_code
        ]);
    }
    
    public function edit($id) {
        // Tedarikçiyi getir
        $supplier = $this->supplierModel->getById($id);
        
        if(!$supplier) {
            $_SESSION['error'] = "Tedarikçi bulunamadı.";
            $this->redirect('/supplier');
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
            
            // Tedarikçiyi güncelle
            if($this->supplierModel->update($id, $data)) {
                $_SESSION['success'] = "Tedarikçi başarıyla güncellendi.";
                $this->redirect('/supplier');
            } else {
                $_SESSION['error'] = "Tedarikçi güncellenirken bir hata oluştu.";
            }
        }
        
        // Tedarikçi düzenleme formunu göster
        $this->view('suppliers/edit', [
            'supplier' => $supplier
        ]);
    }
    
    // "view" metodunu "viewSupplier" olarak yeniden adlandırıyoruz
    public function viewSupplier($id) {
        // Tedarikçiyi getir
        $supplier = $this->supplierModel->getById($id);
        
        if(!$supplier) {
            $_SESSION['error'] = "Tedarikçi bulunamadı.";
            $this->redirect('/supplier');
        }
        
        // Tedarikçi detaylarını göster
        $this->view('suppliers/view', ['supplier' => $supplier]);
    }
    
    public function delete($id) {
        // Tedarikçiyi getir
        $supplier = $this->supplierModel->getById($id);
        
        if(!$supplier) {
            $_SESSION['error'] = "Tedarikçi bulunamadı.";
            $this->redirect('/supplier');
        }
        
        // Tedarikçiyi sil
        if($this->supplierModel->delete($id)) {
            $_SESSION['success'] = "Tedarikçi başarıyla silindi.";
        } else {
            $_SESSION['error'] = "Tedarikçi silinemedi. Bu tedarikçi alım kayıtlarında kullanılıyor olabilir.";
        }
        
        $this->redirect('/supplier');
    }
    
    public function search() {
        if(isset($_GET['keyword'])) {
            $keyword = trim($_GET['keyword']);
            
            // Tedarikçileri ara
            $suppliers = $this->supplierModel->searchSuppliers($keyword)->fetchAll(PDO::FETCH_ASSOC);
            
            // Arama sonuçlarını göster
            $this->view('suppliers/search', [
                'suppliers' => $suppliers,
                'keyword' => $keyword
            ]);
        } else {
            $this->redirect('/supplier');
        }
    }
}