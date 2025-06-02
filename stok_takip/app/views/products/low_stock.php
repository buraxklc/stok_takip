<?php
// app/views/products/low_stock.php
require_once 'app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Kritik Stok Seviyesindeki Ürünler</h3>
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
                                    <th>Kritik Seviye</th>
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
                                                <span class="badge bg-danger"><?php echo $product['stock_quantity']; ?></span>
                                                <?php echo $product['unit']; ?>
                                            </td>
                                            <td><?php echo $product['min_stock_level'] . ' ' . $product['unit']; ?></td>
                                            <td>
                                                <a href="<?php echo BASE_URL; ?>/product/updateStock/<?php echo $product['id']; ?>" class="btn btn-sm btn-success">
                                                    <i class="fas fa-plus-minus"></i> Stok Ekle
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>/product/viewProduct/<?php echo $product['id']; ?>" class="btn btn-sm btn-info">
    <i class="fas fa-eye"></i> Detay
</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Kritik stok seviyesinde ürün bulunmamaktadır.</td>
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