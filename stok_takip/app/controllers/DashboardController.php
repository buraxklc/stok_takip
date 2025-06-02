<?php
// app/controllers/DashboardController.php

class DashboardController extends Controller {
    private $productModel;
    private $categoryModel;
    private $customerModel;
    private $supplierModel;
    
    public function __construct() {
        // Oturumu kontrol et
        if(!isset($_SESSION['user_id'])) {
            $this->redirect('/auth/login');
        }
        
        $this->productModel = $this->model('Product');
        $this->categoryModel = $this->model('Category');
        $this->customerModel = $this->model('Customer');
        $this->supplierModel = $this->model('Supplier');
    }
    
    public function index() {
        // Dashboard için gerekli verileri topla
        $totalProducts = $this->productModel->getCount();
        $totalCategories = $this->categoryModel->getCount();
        $totalCustomers = $this->customerModel->getCount();
        $totalSuppliers = $this->supplierModel->getCount();
        $lowStockProducts = $this->productModel->getLowStock()->fetchAll(PDO::FETCH_ASSOC);
        
        // Dashboard sayfasını göster
        $this->view('dashboard/index', [
            'totalProducts' => $totalProducts,
            'totalCategories' => $totalCategories,
            'totalCustomers' => $totalCustomers,
            'totalSuppliers' => $totalSuppliers,
            'lowStockProducts' => $lowStockProducts
        ]);
    }
}