<?php
// app/controllers/ProductController.php

class ProductController extends Controller {
    private $productModel;
    private $categoryModel;
    
    public function __construct() {
        // Oturumu kontrol et
        if(!isset($_SESSION['user_id'])) {
            $this->redirect('/auth/login');
        }
        
        $this->productModel = $this->model('Product');
        $this->categoryModel = $this->model('Category');
    }
    
    public function index() {
        // Tüm ürünleri getir
        $products = $this->productModel->getAll()->fetchAll(PDO::FETCH_ASSOC);
        
        // Ürün listesini göster
        $this->view('products/index', ['products' => $products]);
    }
    
    public function create() {
        // Kategorileri getir
        $categories = $this->categoryModel->getAll()->fetchAll(PDO::FETCH_ASSOC);
        
        // Yeni ürün kodu oluştur
        $product_code = $this->productModel->generateProductCode();
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Form verilerini al
            $data = [
                'code' => trim($_POST['code']),
                'barcode' => trim($_POST['barcode']),
                'name' => trim($_POST['name']),
                'category_id' => $_POST['category_id'],
                'purchase_price' => floatval($_POST['purchase_price']),
                'sale_price' => floatval($_POST['sale_price']),
                'stock_quantity' => intval($_POST['stock_quantity']),
                'min_stock_level' => intval($_POST['min_stock_level']),
                'unit' => trim($_POST['unit']),
                'description' => trim($_POST['description']),
                'image' => '',
                'status' => isset($_POST['status']) ? 1 : 0
            ];
            
            // Resim yükleme işlemi
            if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $upload_dir = 'uploads/products/';
                
                // Dizin yoksa oluştur
                if(!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $temp_name = $_FILES['image']['tmp_name'];
                $file_name = time() . '_' . $_FILES['image']['name'];
                $file_path = $upload_dir . $file_name;
                
                // Resmi yükle
                if(move_uploaded_file($temp_name, $file_path)) {
                    $data['image'] = $file_name;
                }
            }
            
            // Ürünü oluştur
            if($this->productModel->create($data)) {
                $_SESSION['success'] = "Ürün başarıyla oluşturuldu.";
                $this->redirect('/product');
            } else {
                $_SESSION['error'] = "Ürün oluşturulurken bir hata oluştu.";
            }
        }
        
        // Ürün oluşturma formunu göster
        $this->view('products/create', [
            'categories' => $categories,
            'product_code' => $product_code
        ]);
    }
    
    public function edit($id) {
        // Ürünü getir
        $product = $this->productModel->getById($id);
        
        if(!$product) {
            $_SESSION['error'] = "Ürün bulunamadı.";
            $this->redirect('/product');
        }
        
        // Kategorileri getir
        $categories = $this->categoryModel->getAll()->fetchAll(PDO::FETCH_ASSOC);
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Form verilerini al
            $data = [
                'code' => trim($_POST['code']),
                'barcode' => trim($_POST['barcode']),
                'name' => trim($_POST['name']),
                'category_id' => $_POST['category_id'],
                'purchase_price' => floatval($_POST['purchase_price']),
                'sale_price' => floatval($_POST['sale_price']),
                'stock_quantity' => intval($_POST['stock_quantity']),
                'min_stock_level' => intval($_POST['min_stock_level']),
                'unit' => trim($_POST['unit']),
                'description' => trim($_POST['description']),
                'image' => $product['image'], // Mevcut resim
                'status' => isset($_POST['status']) ? 1 : 0
            ];
            
            // Resim yükleme işlemi
            if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $upload_dir = 'uploads/products/';
                
                // Dizin yoksa oluştur
                if(!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $temp_name = $_FILES['image']['tmp_name'];
                $file_name = time() . '_' . $_FILES['image']['name'];
                $file_path = $upload_dir . $file_name;
                
                // Resmi yükle
                if(move_uploaded_file($temp_name, $file_path)) {
                    // Eski resmi sil
                    if(!empty($product['image']) && file_exists($upload_dir . $product['image'])) {
                        unlink($upload_dir . $product['image']);
                    }
                    
                    $data['image'] = $file_name;
                }
            }
            
            // Ürünü güncelle
            if($this->productModel->update($id, $data)) {
                $_SESSION['success'] = "Ürün başarıyla güncellendi.";
                $this->redirect('/product');
            } else {
                $_SESSION['error'] = "Ürün güncellenirken bir hata oluştu.";
            }
        }
        
        // Ürün düzenleme formunu göster
        $this->view('products/edit', [
            'product' => $product,
            'categories' => $categories
        ]);
    }
    
    // viewProduct olarak değiştirildi (view => viewProduct)
    public function viewProduct($id) {
        // Ürünü getir
        $product = $this->productModel->getById($id);
        
        if(!$product) {
            $_SESSION['error'] = "Ürün bulunamadı.";
            $this->redirect('/product');
        }
        
        // Ürün detaylarını göster
        $this->view('products/view', ['product' => $product]);
    }
    
    public function delete($id) {
        // Ürünü getir
        $product = $this->productModel->getById($id);
        
        if(!$product) {
            $_SESSION['error'] = "Ürün bulunamadı.";
            $this->redirect('/product');
        }
        
        // Ürünü sil
        if($this->productModel->delete($id)) {
            // Ürün resmini sil
            if(!empty($product['image'])) {
                $file_path = 'uploads/products/' . $product['image'];
                if(file_exists($file_path)) {
                    unlink($file_path);
                }
            }
            
            $_SESSION['success'] = "Ürün başarıyla silindi.";
        } else {
            $_SESSION['error'] = "Ürün silinemedi. Bu ürün satış veya alım kayıtlarında kullanılıyor olabilir.";
        }
        
        $this->redirect('/product');
    }
    
    public function search() {
        if(isset($_GET['keyword'])) {
            $keyword = trim($_GET['keyword']);
            
            // Ürünleri ara
            $products = $this->productModel->searchProducts($keyword)->fetchAll(PDO::FETCH_ASSOC);
            
            // Arama sonuçlarını göster
            $this->view('products/search', [
                'products' => $products,
                'keyword' => $keyword
            ]);
        } else {
            $this->redirect('/product');
        }
    }
    
    public function byCategory($category_id) {
        // Kategoriyi getir
        $category = $this->categoryModel->getById($category_id);
        
        if(!$category) {
            $_SESSION['error'] = "Kategori bulunamadı.";
            $this->redirect('/product');
        }
        
        // Kategoriye ait ürünleri getir
        $products = $this->productModel->getByCategory($category_id)->fetchAll(PDO::FETCH_ASSOC);
        
        // Ürün listesini göster
        $this->view('products/by_category', [
            'products' => $products,
            'category' => $category
        ]);
    }
    
    public function lowStock() {
        // Kritik stok seviyesindeki ürünleri getir
        $products = $this->productModel->getLowStock()->fetchAll(PDO::FETCH_ASSOC);
        
        // Ürün listesini göster
        $this->view('products/low_stock', ['products' => $products]);
    }
    
    public function updateStock($id) {
        // Ürünü getir
        $product = $this->productModel->getById($id);
        
        if(!$product) {
            $_SESSION['error'] = "Ürün bulunamadı.";
            $this->redirect('/product');
        }
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            $quantity = intval($_POST['quantity']);
            
            // Stok güncelle
            if($this->productModel->updateStock($id, $quantity)) {
                $_SESSION['success'] = "Stok başarıyla güncellendi.";
                $this->redirect('/product/viewProduct/' . $id);
            } else {
                $_SESSION['error'] = "Stok güncellenirken bir hata oluştu.";
            }
        }
        
        // Stok güncelleme formunu göster
        $this->view('products/update_stock', ['product' => $product]);
    }
}