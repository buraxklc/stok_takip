<?php
// app/views/reports/profit.php
require_once 'app/views/layouts/header.php';
?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kâr/Zarar Raporu</h1>
        <div>
            <a href="<?php echo BASE_URL; ?>/report/generatePdf/profit" class="d-none d-sm-inline-block btn btn-sm btn-danger shadow-sm me-2">
                <i class="fas fa-file-pdf fa-sm text-white-50 me-1"></i> PDF İndir
            </a>
            <a href="<?php echo BASE_URL; ?>/report/generateExcel/profit" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
                <i class="fas fa-file-excel fa-sm text-white-50 me-1"></i> Excel İndir
            </a>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tarih Aralığı Seçin</h6>
                </div>
                <div class="card-body">
                    <form action="<?php echo BASE_URL; ?>/report/profit" method="GET" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="date_filter" class="form-label">Tarih Filtresi</label>
                            <select class="form-select" id="date_filter" name="date_filter">
                                <option value="today" <?php echo ($data['date_filter'] == 'today') ? 'selected' : ''; ?>>Bugün</option>
                                <option value="yesterday" <?php echo ($data['date_filter'] == 'yesterday') ? 'selected' : ''; ?>>Dün</option>
                                <option value="this_week" <?php echo ($data['date_filter'] == 'this_week') ? 'selected' : ''; ?>>Bu Hafta</option>
                                <option value="last_week" <?php echo ($data['date_filter'] == 'last_week') ? 'selected' : ''; ?>>Geçen Hafta</option>
                                <option value="this_month" <?php echo ($data['date_filter'] == 'this_month') ? 'selected' : ''; ?>>Bu Ay</option>
                                <option value="last_month" <?php echo ($data['date_filter'] == 'last_month') ? 'selected' : ''; ?>>Geçen Ay</option>
                                <option value="this_year" <?php echo ($data['date_filter'] == 'this_year') ? 'selected' : ''; ?>>Bu Yıl</option>
                                <option value="last_year" <?php echo ($data['date_filter'] == 'last_year') ? 'selected' : ''; ?>>Geçen Yıl</option>
                                <option value="custom" <?php echo ($data['date_filter'] == 'custom') ? 'selected' : ''; ?>>Özel Aralık</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3 <?php echo ($data['date_filter'] != 'custom') ? 'd-none' : ''; ?>" id="custom_date_range">
                            <label for="start_date" class="form-label">Başlangıç Tarihi</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $data['start_date']; ?>">
                        </div>
                        
                        <div class="col-md-3 <?php echo ($data['date_filter'] != 'custom') ? 'd-none' : ''; ?>" id="custom_date_range2">
                            <label for="end_date" class="form-label">Bitiş Tarihi</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $data['end_date']; ?>">
                        </div>
                        
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">Filtrele</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Total Sales Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Toplam Satış</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($data['total_sales'], 2, ',', '.') . ' ₺'; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Cost Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Toplam Maliyet</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($data['total_cost'], 2, ',', '.') . ' ₺'; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Profit Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Toplam Kâr</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($data['total_profit'], 2, ',', '.') . ' ₺'; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profit Percentage Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Kâr Oranı</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($data['total_profit_percentage'], 2, ',', '.') . ' %'; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Profit Table -->
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Kâr/Zarar Detayları (<?php echo date('d.m.Y', strtotime($data['start_date'])); ?> - <?php echo date('d.m.Y', strtotime($data['end_date'])); ?>)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                            <thead class="table-light">
                                <tr>
                                    <th>Fatura No</th>
                                    <th>Tarih</th>
                                    <th>Ürün</th>
                                    <th>Miktar</th>
                                    <th>Maliyet Fiyatı</th>
                                    <th>Satış Fiyatı</th>
                                    <th>Toplam Maliyet</th>
                                    <th>Toplam Satış</th>
                                    <th>Kâr</th>
                                    <th>Kâr Oranı %</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($data['profit_details']) > 0): ?>
                                    <?php foreach($data['profit_details'] as $detail): ?>
                                        <tr>
                                            <td><?php echo $detail['invoice_no']; ?></td>
                                            <td><?php echo date('d.m.Y', strtotime($detail['sale_date'])); ?></td>
                                            <td><?php echo $detail['product_name']; ?></td>
                                            <td><?php echo $detail['quantity']; ?></td>
                                            <td><?php echo number_format($detail['cost_price'], 2, ',', '.') . ' ₺'; ?></td>
                                            <td><?php echo number_format($detail['sale_price'], 2, ',', '.') . ' ₺'; ?></td>
                                            <td><?php echo number_format($detail['total_cost'], 2, ',', '.') . ' ₺'; ?></td>
                                            <td><?php echo number_format($detail['total_revenue'], 2, ',', '.') . ' ₺'; ?></td>
                                            <td class="<?php echo ($detail['profit'] >= 0) ? 'text-success' : 'text-danger'; ?>">
                                                <?php echo number_format($detail['profit'], 2, ',', '.') . ' ₺'; ?>
                                            </td>
                                            <td class="<?php echo ($detail['profit_percentage'] >= 0) ? 'text-success' : 'text-danger'; ?>">
                                                <?php echo number_format($detail['profit_percentage'], 2, ',', '.') . ' %'; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="10" class="text-center">Seçilen tarih aralığında satış kaydı bulunmamaktadır.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="6" class="text-end">Toplam:</th>
                                    <th><?php echo number_format($data['total_cost'], 2, ',', '.') . ' ₺'; ?></th>
                                    <th><?php echo number_format($data['total_sales'], 2, ',', '.') . ' ₺'; ?></th>
                                    <th class="<?php echo ($data['total_profit'] >= 0) ? 'text-success' : 'text-danger'; ?>">
                                        <?php echo number_format($data['total_profit'], 2, ',', '.') . ' ₺'; ?>
                                    </th>
                                    <th class="<?php echo ($data['total_profit_percentage'] >= 0) ? 'text-success' : 'text-danger'; ?>">
                                        <?php echo number_format($data['total_profit_percentage'], 2, ',', '.') . ' %'; ?>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateFilter = document.getElementById('date_filter');
    const customDateRange = document.getElementById('custom_date_range');
    const customDateRange2 = document.getElementById('custom_date_range2');
    
    dateFilter.addEventListener('change', function() {
        if(this.value === 'custom') {
            customDateRange.classList.remove('d-none');
            customDateRange2.classList.remove('d-none');
        } else {
            customDateRange.classList.add('d-none');
            customDateRange2.classList.add('d-none');
        }
    });
});
</script>

<?php require_once 'app/views/layouts/footer.php'; ?>