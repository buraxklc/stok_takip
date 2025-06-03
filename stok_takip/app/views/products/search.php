<?php
// app/views/products/search.php
require_once 'app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Arama Sonuçları: "<?php echo $data['keyword']; ?>"</h3>
                    <a href="<?php echo BASE_URL; ?>/product" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Tüm Ürünler
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Kod</th>
                                    <th>Resim</th>
                                    <th>Ürün Adı</th>
                                    <th>Kategori</th>
                                    <th>Stok</th>
                                    <th>Alış Fiyatı</th>
                                    <th>Satış Fiyatı</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($data['products']) > 0): ?>
                                    <?php foreach($data['products'] as $product): ?>
                                        <tr>
                                            <td><?php echo $product['code']; ?></td>
                                            <td>
                                                <?php if(!empty($product['image']) && file_exists('uploads/products/' . $product['image'])): ?>
                                                    <img src="<?php echo BASE_URL; ?>/uploads/products/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" width="50" height="50" class="img-thumbnail">
                                                <?php else: ?>
                                                    <img src="<?php echo BASE_URL; ?>/uploads/no-image.png" alt="No Image" width="50" height="50" class="img-thumbnail">
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo $product['name']; ?></td>
                                            <td><?php echo $product['category_name']; ?></td>
                                            <td>
                                                <?php if($product['stock_quantity'] <= $product['min_stock_level']): ?>
                                                    <span class="badge bg-danger"><?php echo $product['stock_quantity']; ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-success"><?php echo $product['stock_quantity']; ?></span>
                                                <?php endif; ?>
                                                <?php echo $product['unit']; ?>
                                            </td>
                                            <td><?php echo number_format($product['purchase_price'], 2, ',', '.') . ' ₺'; ?></td>
                                            <td><?php echo number_format($product['sale_price'], 2, ',', '.') . ' ₺'; ?></td>
                                            <td>
                                                <a href="<?php echo BASE_URL; ?>/product/viewProduct/<?php echo $product['id']; ?>" class="btn btn-sm btn-info">
    <i class="fas fa-eye"></i>
</a>
                                                <a href="<?php echo BASE_URL; ?>/product/edit/<?php echo $product['id']; ?>" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>/product/delete/<?php echo $product['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bu ürünü silmek istediğinize emin misiniz?');">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">Arama sonucu bulunamadı.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'app/views/layouts/footer.php'; ?>