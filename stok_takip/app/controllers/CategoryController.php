<?php
// app/controllers/CategoryController.php

class CategoryController extends Controller {
    private $categoryModel;
    
    public function __construct() {
        // Oturumu kontrol et
        if(!isset($_SESSION['user_id'])) {
            $this->redirect('/auth/login');
        }
        
        $this->categoryModel = $this->model('Category');
    }
    
    public function index() {
        // Tüm kategorileri getir
        $categories = $this->categoryModel->getAllWithParentName()->fetchAll(PDO::FETCH_ASSOC);
        
        // Kategori listesini göster
        $this->view('categories/index', ['categories' => $categories]);
    }
    
    public function create() {
        // Ana kategorileri getir (parent_id = NULL olanlar)
        $mainCategories = $this->categoryModel->getMainCategories()->fetchAll(PDO::FETCH_ASSOC);
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Form verilerini al
            $data = [
                'name' => trim($_POST['name']),
                'parent_id' => isset($_POST['parent_id']) ? trim($_POST['parent_id']) : null,
                'description' => trim($_POST['description']),
                'status' => isset($_POST['status']) ? 1 : 0
            ];
            
            // Kategoriyi oluştur
            if($this->categoryModel->create($data)) {
                $_SESSION['success'] = "Kategori başarıyla oluşturuldu.";
                $this->redirect('/category');
            } else {
                $_SESSION['error'] = "Kategori oluşturulurken bir hata oluştu.";
            }
        }
        
        // Kategori oluşturma formunu göster
        $this->view('categories/create', ['mainCategories' => $mainCategories]);
    }
    
    public function edit($id) {
        // Kategoriyi getir
        $category = $this->categoryModel->getById($id);
        
        if(!$category) {
            $_SESSION['error'] = "Kategori bulunamadı.";
            $this->redirect('/category');
        }
        
        // Ana kategorileri getir (parent_id = NULL olanlar)
        $mainCategories = $this->categoryModel->getMainCategories()->fetchAll(PDO::FETCH_ASSOC);
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Form verilerini al
            $data = [
                'name' => trim($_POST['name']),
                'parent_id' => isset($_POST['parent_id']) ? trim($_POST['parent_id']) : null,
                'description' => trim($_POST['description']),
                'status' => isset($_POST['status']) ? 1 : 0
            ];
            
            // Kendisini üst kategori olarak seçmesini engelle
            if($data['parent_id'] == $id) {
                $_SESSION['error'] = "Kategori kendisini üst kategori olarak seçemez.";
                $this->view('categories/edit', [
                    'category' => $category,
                    'mainCategories' => $mainCategories
                ]);
                return;
            }
            
            // Kategoriyi güncelle
            if($this->categoryModel->update($id, $data)) {
                $_SESSION['success'] = "Kategori başarıyla güncellendi.";
                $this->redirect('/category');
            } else {
                $_SESSION['error'] = "Kategori güncellenirken bir hata oluştu.";
            }
        }
        
        // Kategori düzenleme formunu göster
        $this->view('categories/edit', [
            'category' => $category,
            'mainCategories' => $mainCategories
        ]);
    }
    
    public function delete($id) {
        // Kategoriyi sil
        if($this->categoryModel->delete($id)) {
            $_SESSION['success'] = "Kategori başarıyla silindi.";
        } else {
            $_SESSION['error'] = "Kategori silinemedi. Bu kategori ürünlerde kullanılıyor olabilir veya alt kategorileri var.";
        }
        
        $this->redirect('/category');
    }
}