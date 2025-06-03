<?php
// app/views/dashboard/index.php
require_once 'app/views/layouts/header.php';
?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <a href="<?php echo BASE_URL; ?>/report/generate" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-download fa-sm text-white-50 me-1"></i> Rapor Oluştur
        </a>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Ürün Sayısı Kartı -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card stat-card-primary h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Toplam Ürün</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $data['totalProducts']; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300 stat-icon"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 py-2">
                    <a href="<?php echo BASE_URL; ?>/product" class="text-primary text-decoration-none small">
                        Detaylar <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Kategori Sayısı Kartı -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card stat-card-success h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Toplam Kategori</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $data['totalCategories']; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-folder fa-2x text-gray-300 stat-icon"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 py-2">
                    <a href="<?php echo BASE_URL; ?>/category" class="text-success text-decoration-none small">
                        Detaylar <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Müşteri Sayısı Kartı -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card stat-card-warning h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Toplam Müşteri</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">0</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300 stat-icon"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 py-2">
                    <a href="<?php echo BASE_URL; ?>/customer" class="text-warning text-decoration-none small">
                        Detaylar <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Kritik Stok Kartı -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card stat-card-danger h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Kritik Stok</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count($data['lowStockProducts']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300 stat-icon"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 py-2">
                    <a href="<?php echo BASE_URL; ?>/product/lowStock" class="text-danger text-decoration-none small">
                        Detaylar <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Kritik Stok Tablosu -->
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Kritik Stok Seviyesindeki Ürünler</h6>
                   <a href="<?php echo BASE_URL; ?>/product/lowStock" class="btn btn-sm btn-primary">
                        Tümünü Gör
                    </a>
                </div>
                <div class="card-body">
                    <?php if(count($data['lowStockProducts']) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" width="100%" cellspacing="0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kod</th>
                                        <th>Ürün Adı</th>
                                        <th>Kategori</th>
                                        <th>Stok</th>
                                        <th>Kritik Seviye</th>
                                        <th>İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($data['lowStockProducts'] as $product): ?>
                                        <tr>
                                            <td><?php echo $product['code']; ?></td>
                                            <td>
                                                <?php if(!empty($product['image']) && file_exists('uploads/products/' . $product['image'])): ?>
                                                    <img src="<?php echo BASE_URL; ?>/uploads/products/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="img-product me-2">
                                                <?php endif; ?>
                                                <?php echo $product['name']; ?>
                                            </td>
                                            <td><?php echo $product['category_name']; ?></td>
                                            <td>
                                                <span class="badge bg-danger"><?php echo $product['stock_quantity']; ?></span>
                                                <?php echo $product['unit']; ?>
                                            </td>
                                            <td><?php echo $product['min_stock_level'] . ' ' . $product['unit']; ?></td>
                                            <td>
                                                <a href="<?php echo BASE_URL; ?>/product/updateStock/<?php echo $product['id']; ?>" class="btn btn-sm btn-success">
                                                    <i class="fas fa-plus-circle"></i> Stok Ekle
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>/product/viewProduct/<?php echo $product['id']; ?>" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> Detay
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i> Kritik stok seviyesinde ürün bulunmamaktadır.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Hızlı İşlemler -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Hızlı İşlemler</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <a href="<?php echo BASE_URL; ?>/product/create" class="btn btn-primary btn-block">
                                <i class="fas fa-plus-circle me-1"></i> Yeni Ürün Ekle
                            </a>
                        </div>
                        <div class="col-md-6 mb-2">
                            <a href="<?php echo BASE_URL; ?>/category/create" class="btn btn-success btn-block">
                                <i class="fas fa-plus-circle me-1"></i> Yeni Kategori
                            </a>
                        </div>
                        <div class="col-md-6 mb-2">
                            <a href="<?php echo BASE_URL; ?>/sale/create" class="btn btn-info btn-block">
                                <i class="fas fa-shopping-cart me-1"></i> Yeni Satış
                            </a>
                        </div>
                        <div class="col-md-6 mb-2">
                            <a href="<?php echo BASE_URL; ?>/purchase/create" class="btn btn-warning btn-block text-dark">
                                <i class="fas fa-shopping-basket me-1"></i> Yeni Alım
                            </a>
                        </div>
                        <div class="col-md-6 mb-2">
                            <a href="<?php echo BASE_URL; ?>/customer/create" class="btn btn-secondary btn-block">
                                <i class="fas fa-user-plus me-1"></i> Yeni Müşteri
                            </a>
                        </div>
                        <div class="col-md-6 mb-2">
                            <a href="<?php echo BASE_URL; ?>/supplier/create" class="btn btn-dark btn-block">
                                <i class="fas fa-truck me-1"></i> Yeni Tedarikçi
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Sistem Bilgileri</h6>
                </div>
                <div class="card-body">
                    <p><strong>Versiyon:</strong> 1.0.0</p>
                    <p><strong>Son Güncelleme:</strong> <?php echo date('d.m.Y'); ?></p>
                    <p><strong>Lisans:</strong> Ticari Lisans</p>
                    <p><strong>PHP Versiyonu:</strong> <?php echo phpversion(); ?></p>
                    <p><strong>Veritabanı:</strong> MySQL</p>
                </div>
            </div>
        </div>

        <!-- Yaklaşan Ödemeler ve Tahsilatlar (Demo) -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Yaklaşan Ödemeler</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Yaklaşan ödeme bulunmamaktadır.
                    </div>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Yaklaşan Tahsilatlar</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i> Yaklaşan tahsilat bulunmamaktadır.
                    </div>
                </div>
            </div>
            
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Yararlı Bağlantılar</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <a href="<?php echo BASE_URL; ?>/report/stock" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-file-alt me-1"></i> Stok Raporu
                            </a>
                        </div>
                        <div class="col-md-6 mb-2">
                            <a href="<?php echo BASE_URL; ?>/report/sales" class="btn btn-outline-success btn-block">
                                <i class="fas fa-chart-line me-1"></i> Satış Raporu
                            </a>
                        </div>
                        <div class="col-md-6 mb-2">
                            <a href="<?php echo BASE_URL; ?>/report/profit" class="btn btn-outline-info btn-block">
                                <i class="fas fa-chart-pie me-1"></i> Kâr/Zarar Raporu
                            </a>
                        </div>
                        <div class="col-md-6 mb-2">
                            <a href="<?php echo BASE_URL; ?>/setting" class="btn btn-outline-dark btn-block">
                                <i class="fas fa-cogs me-1"></i> Ayarlar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'app/views/layouts/footer.php'; ?>