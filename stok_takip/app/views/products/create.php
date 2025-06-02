<?php
// app/views/products/create.php
require_once 'app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3>Yeni Ürün Ekle</h3>
                </div>
                <div class="card-body">
                    <?php if(isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?php 
                                echo $_SESSION['error']; 
                                unset($_SESSION['error']);
                            ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?php echo BASE_URL; ?>/product/create" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label">Ürün Kodu</label>
                                <input type="text" class="form-control" id="code" name="code" value="<?php echo $data['product_code']; ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="barcode" class="form-label">Barkod</label>
                                <input type="text" class="form-control" id="barcode" name="barcode">
                                <div class="form-text">Opsiyonel, varsa barkod numarası girin.</div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Ürün Adı</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Kategori</label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">Kategori Seçin</option>
                                <?php foreach($data['categories'] as $category): ?>
                                    <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="purchase_price" class="form-label">Alış Fiyatı</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="purchase_price" name="purchase_price" step="0.01" min="0" required>
                                    <span class="input-group-text">₺</span>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="sale_price" class="form-label">Satış Fiyatı</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="sale_price" name="sale_price" step="0.01" min="0" required>
                                    <span class="input-group-text">₺</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="stock_quantity" class="form-label">Stok Miktarı</label>
                                <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" min="0" required>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="min_stock_level" class="form-label">Kritik Stok Seviyesi</label>
                                <input type="number" class="form-control" id="min_stock_level" name="min_stock_level" min="0" required>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="unit" class="form-label">Birim</label>
                                <select class="form-select" id="unit" name="unit" required>
                                    <option value="adet">Adet</option>
                                    <option value="kg">Kilogram (kg)</option>
                                    <option value="lt">Litre (lt)</option>
                                    <option value="mt">Metre (mt)</option>
                                    <option value="paket">Paket</option>
                                    <option value="kutu">Kutu</option>
                                </select>
                                </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Açıklama</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="image" class="form-label">Ürün Resmi</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <div class="form-text">Maksimum dosya boyutu: 2MB. İzin verilen formatlar: JPG, JPEG, PNG.</div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="status" name="status" checked>
                            <label class="form-check-label" for="status">Aktif</label>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Kaydet</button>
                            <a href="<?php echo BASE_URL; ?>/product" class="btn btn-secondary">İptal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'app/views/layouts/footer.php'; ?>