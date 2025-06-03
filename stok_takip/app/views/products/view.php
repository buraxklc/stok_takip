<?php
// app/views/products/view.php
require_once 'app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Ürün Detayları</h3>
                    <div>
                        <a href="<?php echo BASE_URL; ?>/product/updateStock/<?php echo $data['product']['id']; ?>" class="btn btn-success me-2">
                            <i class="fas fa-plus-minus"></i> Stok Güncelle
                        </a>
                        <a href="<?php echo BASE_URL; ?>/product/edit/<?php echo $data['product']['id']; ?>" class="btn btn-warning me-2">
                            <i class="fas fa-edit"></i> Düzenle
                        </a>
                        <a href="<?php echo BASE_URL; ?>/product" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Geri
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if(isset($_SESSION['success'])): ?>
                        <div class="alert alert-success">
                            <?php 
                                echo $_SESSION['success']; 
                                unset($_SESSION['success']);
                            ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?php 
                                echo $_SESSION['error']; 
                                unset($_SESSION['error']);
                            ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-4 text-center mb-4">
                            <?php if(!empty($data['product']['image']) && file_exists('uploads/products/' . $data['product']['image'])): ?>
                                <img src="<?php echo BASE_URL; ?>/uploads/products/<?php echo $data['product']['image']; ?>" alt="<?php echo $data['product']['name']; ?>" class="img-fluid img-thumbnail" style="max-height: 250px;">
                            <?php else: ?>
                                <img src="<?php echo BASE_URL; ?>/uploads/no-image.png" alt="No Image" class="img-fluid img-thumbnail" style="max-height: 250px;">
                            <?php endif; ?>
                        </div>
                        <div class="col-md-8">
                            <h2><?php echo $data['product']['name']; ?></h2>
                            <p class="text-muted"><?php echo $data['product']['code']; ?></p>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p><strong>Kategori:</strong> <?php echo $data['product']['category_name']; ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p>
                                        <strong>Durum:</strong> 
                                        <?php if($data['product']['status'] == 1): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Pasif</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p><strong>Alış Fiyatı:</strong> <?php echo number_format($data['product']['purchase_price'], 2, ',', '.') . ' ₺'; ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Satış Fiyatı:</strong> <?php echo number_format($data['product']['sale_price'], 2, ',', '.') . ' ₺'; ?></p>
                                </div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p>
                                        <strong>Stok Miktarı:</strong> 
                                        <?php if($data['product']['stock_quantity'] <= $data['product']['min_stock_level']): ?>
                                            <span class="badge bg-danger"><?php echo $data['product']['stock_quantity']; ?></span>
                                        <?php else: ?>
                                            <span class="badge bg-success"><?php echo $data['product']['stock_quantity']; ?></span>
                                        <?php endif; ?>
                                        <?php echo $data['product']['unit']; ?>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Kritik Stok Seviyesi:</strong> <?php echo $data['product']['min_stock_level']; ?></p>
                                </div>
                            </div>
                            
                            <?php if(!empty($data['product']['barcode'])): ?>
                                <p><strong>Barkod:</strong> <?php echo $data['product']['barcode']; ?></p>
                            <?php endif; ?>
                            
                            <?php if(!empty($data['product']['description'])): ?>
                                <div class="mt-3">
                                    <h5>Açıklama</h5>
                                    <p><?php echo nl2br($data['product']['description']); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-muted">
                    Son güncelleme: <?php echo date('d.m.Y H:i', strtotime($data['product']['updated_at'])); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'app/views/layouts/footer.php'; ?>