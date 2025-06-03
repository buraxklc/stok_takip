<?php
// app/views/reports/index.php
require_once 'app/views/layouts/header.php';
?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Raporlar</h1>
    </div>

    <!-- Content Row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Rapor Türleri</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Stok Raporu</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Stok Durumu</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent border-0 py-2">
                                    <a href="<?php echo BASE_URL; ?>/report/stock" class="text-primary text-decoration-none small">
                                        Görüntüle <i class="fas fa-chevron-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Satış Raporu</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Satış İstatistikleri</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent border-0 py-2">
                                    <a href="<?php echo BASE_URL; ?>/report/sales" class="text-success text-decoration-none small">
                                        Görüntüle <i class="fas fa-chevron-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                                Alım Raporu</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Alım İstatistikleri</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-shopping-basket fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent border-0 py-2">
                                    <a href="<?php echo BASE_URL; ?>/report/purchases" class="text-info text-decoration-none small">
                                        Görüntüle <i class="fas fa-chevron-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="card border-left-warning shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                                Kâr/Zarar Raporu</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Finansal Durum</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent border-0 py-2">
                                    <a href="<?php echo BASE_URL; ?>/report/profit" class="text-warning text-decoration-none small">
                                        Görüntüle <i class="fas fa-chevron-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                                Ödeme Raporu</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Tahsilat ve Ödemeler</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent border-0 py-2">
                                    <a href="<?php echo BASE_URL; ?>/report/payments" class="text-danger text-decoration-none small">
                                        Görüntüle <i class="fas fa-chevron-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="card border-left-secondary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                                Müşteri Raporu</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Müşteri İstatistikleri</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-users fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent border-0 py-2">
                                    <a href="<?php echo BASE_URL; ?>/report/customers" class="text-secondary text-decoration-none small">
                                        Görüntüle <i class="fas fa-chevron-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="card border-left-dark shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                                Tedarikçi Raporu</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">Tedarikçi İstatistikleri</div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-truck fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent border-0 py-2">
                                    <a href="<?php echo BASE_URL; ?>/report/suppliers" class="text-dark text-decoration-none small">
                                        Görüntüle <i class="fas fa-chevron-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'app/views/layouts/footer.php'; ?>