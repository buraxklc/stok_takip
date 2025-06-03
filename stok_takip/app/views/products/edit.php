<?php
// app/views/products/edit.php
require_once 'app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3>Ürün Düzenle</h3>
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
                    
                    <form action="<?php echo BASE_URL; ?>/product/edit/<?php echo $data['product']['id']; ?>" method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label">Ürün Kodu</label>
                                <input type="text" class="form-control" id="code" name="code" value="<?php echo $data['product']['code']; ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="barcode" class="form-label">Barkod</label>
                                <input type="text" class="form-control" id="barcode" name="barcode" value="<?php echo $data['product']['barcode']; ?>">
                                <div class="form-text">Opsiyonel, varsa barkod numarası girin.</div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Ürün Adı</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo $data['product']['name']; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Kategori</label>
                            <select class="form-select" id="category_id" name="category_id" required>
                                <option value="">Kategori Seçin</option>
                                <?php foreach($data['categories'] as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" <?php echo ($category['id'] == $data['product']['category_id']) ? 'selected' : ''; ?>>
                                        <?php echo $category['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="purchase_price" class="form-label">Alış Fiyatı</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="purchase_price" name="purchase_price" step="0.01" min="0" value="<?php echo $data['product']['purchase_price']; ?>" required>
                                    <span class="input-group-text">₺</span>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="sale_price" class="form-label">Satış Fiyatı</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="sale_price" name="sale_price" step="0.01" min="0" value="<?php echo $data['product']['sale_price']; ?>" required>
                                    <span class="input-group-text">₺</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="stock_quantity" class="form-label">Stok Miktarı</label>
                                <input type="number" class="form-control" id="stock_quantity" name="stock_quantity" min="0" value="<?php echo $data['product']['stock_quantity']; ?>" required>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="min_stock_level" class="form-label">Kritik Stok Seviyesi</label>
                                <input type="number" class="form-control" id="min_stock_level" name="min_stock_level" min="0" value="<?php echo $data['product']['min_stock_level']; ?>" required>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="unit" class="form-label">Birim</label>
                                <select class="form-select" id="unit" name="unit" required>
                                    <option value="adet" <?php echo ($data['product']['unit'] == 'adet') ? 'selected' : ''; ?>>Adet</option>
                                    <option value="kg" <?php echo ($data['product']['unit'] == 'kg') ? 'selected' : ''; ?>>Kilogram (kg)</option>
                                    <option value="lt" <?php echo ($data['product']['unit'] == 'lt') ? 'selected' : ''; ?>>Litre (lt)</option>
                                    <option value="mt" <?php echo ($data['product']['unit'] == 'mt') ? 'selected' : ''; ?>>Metre (mt)</option>
                                    <option value="paket" <?php echo ($data['product']['unit'] == 'paket') ? 'selected' : ''; ?>>Paket</option>
                                    <option value="kutu" <?php echo ($data['product']['unit'] == 'kutu') ? 'selected' : ''; ?>>Kutu</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Açıklama</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?php echo $data['product']['description']; ?></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="image" class="form-label">Ürün Resmi</label>
                            <?php if(!empty($data['product']['image']) && file_exists('uploads/products/' . $data['product']['image'])): ?>
                                <div class="mb-2">
                                    <img src="<?php echo BASE_URL; ?>/uploads/products/<?php echo $data['product']['image']; ?>" alt="<?php echo $data['product']['name']; ?>" class="img-thumbnail" width="150">
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <div class="form-text">Yeni resim yüklerseniz mevcut resim değiştirilecektir. Maksimum dosya boyutu: 2MB. İzin verilen formatlar: JPG, JPEG, PNG.</div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="status" name="status" <?php echo ($data['product']['status'] == 1) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="status">Aktif</label>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Güncelle</button>
                            <a href="<?php echo BASE_URL; ?>/product" class="btn btn-secondary">İptal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'app/views/layouts/footer.php'; ?>