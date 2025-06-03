<?php
// app/views/products/index.php
require_once 'app/views/layouts/header.php';
?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Ürünler</h1>
        <div>
            <a href="<?php echo BASE_URL; ?>/product/lowStock" class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm me-2">
                <i class="fas fa-exclamation-triangle fa-sm me-1"></i> Kritik Stok
            </a>
            <a href="<?php echo BASE_URL; ?>/product/create" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                <i class="fas fa-plus fa-sm text-white-50 me-1"></i> Yeni Ürün
            </a>
        </div>
    </div>

    <!-- Alerts -->
    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Search Box -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form action="<?php echo BASE_URL; ?>/product/search" method="GET">
                <div class="input-group">
                    <input type="text" class="form-control" name="keyword" placeholder="Ürün adı, kodu veya barkodu ara...">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search fa-sm"></i> Ara
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-12">
            <!-- Products Table -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Tüm Ürünler</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Ürün İşlemleri:</div>
                            <a class="dropdown-item" href="<?php echo BASE_URL; ?>/product/create">Yeni Ürün</a>
                            <a class="dropdown-item" href="<?php echo BASE_URL; ?>/product/lowStock">Kritik Stok</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="<?php echo BASE_URL; ?>/category">Kategorilere Git</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                            <thead class="table-light">
                                <tr>
                                    <th>Kod</th>
                                    <th>Resim</th>
                                    <th>Ürün Adı</th>
                                    <th>Kategori</th>
                                    <th>Stok</th>
                                    <th>Alış Fiyatı</th>
                                    <th>Satış Fiyatı</th>
                                    <th>Durum</th>
                                    <th width="15%">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($data['products']) > 0): ?>
                                    <?php foreach($data['products'] as $product): ?>
                                        <tr>
                                            <td><?php echo $product['code']; ?></td>
                                            <td class="text-center">
                                                <?php if(!empty($product['image']) && file_exists('uploads/products/' . $product['image'])): ?>
                                                    <img src="<?php echo BASE_URL; ?>/uploads/products/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="img-product">
                                                <?php else: ?>
                                                    <img src="<?php echo BASE_URL; ?>/uploads/no-image.png" alt="No Image" class="img-product">
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
                                                <?php if($product['status'] == 1): ?>
                                                    <span class="badge bg-success">Aktif</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Pasif</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?php echo BASE_URL; ?>/product/viewProduct/<?php echo $product['id']; ?>" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Detay">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>/product/updateStock/<?php echo $product['id']; ?>" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Stok Güncelle">
                                                    <i class="fas fa-plus-minus"></i>
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>/product/edit/<?php echo $product['id']; ?>" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Düzenle">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>/product/delete/<?php echo $product['id']; ?>" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Sil" onclick="return confirm('Bu ürünü silmek istediğinize emin misiniz?');">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center">Henüz ürün bulunmamaktadır.</td>
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