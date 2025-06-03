<?php
// app/views/reports/stock.php
require_once 'app/views/layouts/header.php';
?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Stok Raporu</h1>
        <div>
            <a href="<?php echo BASE_URL; ?>/report/generatePdf/stock" class="d-none d-sm-inline-block btn btn-sm btn-danger shadow-sm mr-2">
                <i class="fas fa-file-pdf fa-sm text-white-50 me-1"></i> PDF İndir
            </a>
            <a href="<?php echo BASE_URL; ?>/report/generateExcel/stock" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
                <i class="fas fa-file-excel fa-sm text-white-50 me-1"></i> Excel İndir
            </a>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Total Stock Value Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Toplam Stok Değeri</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($data['total_stock_value'], 2, ',', '.') . ' ₺'; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Products Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Toplam Ürün Sayısı</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count($data['products']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Categories Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Toplam Kategori Sayısı</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count($data['categories']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-folder fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Low Stock Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Kritik Stok Sayısı</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $data['low_stock_count']; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Stock Table -->
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Ürün Stok Listesi</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                            <thead class="table-light">
                                <tr>
                                    <th>Kod</th>
                                    <th>Ürün Adı</th>
                                    <th>Kategori</th>
                                    <th>Stok Miktarı</th>
                                    <th>Kritik Seviye</th>
                                    <th>Birim</th>
                                    <th>Alış Fiyatı</th>
                                    <th>Satış Fiyatı</th>
                                    <th>Stok Değeri</th>
                                    <th>Durum</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($data['products']) > 0): ?>
                                    <?php foreach($data['products'] as $product): ?>
                                        <tr class="<?php echo ($product['stock_quantity'] <= $product['min_stock_level']) ? 'table-danger' : ''; ?>">
                                            <td><?php echo $product['code']; ?></td>
                                            <td>
                                                <?php if(!empty($product['image']) && file_exists('uploads/products/' . $product['image'])): ?>
                                                    <img src="<?php echo BASE_URL; ?>/uploads/products/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" class="img-product me-2">
                                                <?php endif; ?>
                                                <?php echo $product['name']; ?>
                                            </td>
                                            <td><?php echo $product['category_name']; ?></td>
                                            <td><?php echo $product['stock_quantity']; ?></td>
                                            <td><?php echo $product['min_stock_level']; ?></td>
                                            <td><?php echo $product['unit']; ?></td>
                                            <td><?php echo number_format($product['purchase_price'], 2, ',', '.') . ' ₺'; ?></td>
                                            <td><?php echo number_format($product['sale_price'], 2, ',', '.') . ' ₺'; ?></td>
                                            <td><?php echo number_format($product['stock_quantity'] * $product['purchase_price'], 2, ',', '.') . ' ₺'; ?></td>
                                            <td>
                                                <?php if($product['status'] == 1): ?>
                                                    <span class="badge bg-success">Aktif</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Pasif</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="10" class="text-center">Henüz ürün bulunmamaktadır.</td>
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