<?php
// app/views/reports/payments.php
require_once 'app/views/layouts/header.php';
?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Ödeme Raporu</h1>
        <div>
            <a href="<?php echo BASE_URL; ?>/report/generatePdf/payments" class="d-none d-sm-inline-block btn btn-sm btn-danger shadow-sm me-2">
                <i class="fas fa-file-pdf fa-sm text-white-50 me-1"></i> PDF İndir
            </a>
            <a href="<?php echo BASE_URL; ?>/report/generateExcel/payments" class="d-none d-sm-inline-block btn btn-sm btn-success shadow-sm">
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
                    <form action="<?php echo BASE_URL; ?>/report/payments" method="GET" class="row g-3 align-items-end">
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
        <!-- Total Customer Payments Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Toplam Tahsilat</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($data['total_customer_payments'], 2, ',', '.') . ' ₺'; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-hand-holding-usd fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Supplier Payments Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Toplam Tedarikçi Ödemesi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($data['total_supplier_payments'], 2, ',', '.') . ' ₺'; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Cash Balance Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Kasa Dengesi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo number_format($data['total_customer_payments'] - $data['total_supplier_payments'], 2, ',', '.') . ' ₺'; ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-balance-scale fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Transactions Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Toplam İşlem Sayısı</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo count($data['customer_payments']) + count($data['supplier_payments']); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Methods Chart -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ödeme Yöntemleri - Tahsilatlar</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Ödeme Yöntemi</th>
                                    <th>Tutar</th>
                                    <th>Yüzde</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    foreach($data['payment_methods'] as $method => $amounts): 
                                    $method_name = '';
                                    switch($method) {
                                        case 'cash':
                                            $method_name = 'Nakit';
                                            break;
                                        case 'bank_transfer':
                                            $method_name = 'Banka Havalesi';
                                            break;
                                        case 'credit_card':
                                            $method_name = 'Kredi Kartı';
                                            break;
                                        case 'check':
                                            $method_name = 'Çek';
                                            break;
                                        case 'other':
                                            $method_name = 'Diğer';
                                            break;
                                    }
                                    $percentage = ($data['total_customer_payments'] > 0) ? ($amounts['customer'] / $data['total_customer_payments']) * 100 : 0;
                                ?>
                                    <tr>
                                        <td><?php echo $method_name; ?></td>
                                        <td><?php echo number_format($amounts['customer'], 2, ',', '.') . ' ₺'; ?></td>
                                        <td><?php echo number_format($percentage, 2, ',', '.') . ' %'; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ödeme Yöntemleri - Ödemeler</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Ödeme Yöntemi</th>
                                    <th>Tutar</th>
                                    <th>Yüzde</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    foreach($data['payment_methods'] as $method => $amounts): 
                                    $method_name = '';
                                    switch($method) {
                                        case 'cash':
                                            $method_name = 'Nakit';
                                            break;
                                        case 'bank_transfer':
                                            $method_name = 'Banka Havalesi';
                                            break;
                                        case 'credit_card':
                                            $method_name = 'Kredi Kartı';
                                            break;
                                        case 'check':
                                            $method_name = 'Çek';
                                            break;
                                        case 'other':
                                            $method_name = 'Diğer';
                                            break;
                                    }
                                    $percentage = ($data['total_supplier_payments'] > 0) ? ($amounts['supplier'] / $data['total_supplier_payments']) * 100 : 0;
                                ?>
                                    <tr>
                                        <td><?php echo $method_name; ?></td>
                                        <td><?php echo number_format($amounts['supplier'], 2, ',', '.') . ' ₺'; ?></td>
                                        <td><?php echo number_format($percentage, 2, ',', '.') . ' %'; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Payments Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Tahsilatlar (<?php echo date('d.m.Y', strtotime($data['start_date'])); ?> - <?php echo date('d.m.Y', strtotime($data['end_date'])); ?>)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="customerPaymentsTable" width="100%" cellspacing="0">
                            <thead class="table-light">
                                <tr>
                                    <th>Ödeme No</th>
                                    <th>Tarih</th>
                                    <th>Müşteri</th>
                                    <th>Fatura No</th>
                                    <th>Tutar</th>
                                    <th>Ödeme Yöntemi</th>
                                    <th>Referans No</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($data['customer_payments']) > 0): ?>
                                    <?php foreach($data['customer_payments'] as $payment): ?>
                                        <tr>
                                            <td><?php echo $payment['id']; ?></td>
                                            <td><?php echo date('d.m.Y', strtotime($payment['payment_date'])); ?></td>
                                            <td><?php echo $payment['customer_name']; ?></td>
                                            <td><?php echo $payment['invoice_no'] ? $payment['invoice_no'] : '-'; ?></td>
                                            <td><?php echo number_format($payment['amount'], 2, ',', '.') . ' ₺'; ?></td>
                                            <td>
                                                <?php
                                                    switch($payment['payment_method']) {
                                                        case 'cash':
                                                            echo 'Nakit';
                                                            break;
                                                        case 'bank_transfer':
                                                            echo 'Banka Havalesi';
                                                            break;
                                                        case 'credit_card':
                                                            echo 'Kredi Kartı';
                                                            break;
                                                        case 'check':
                                                            echo 'Çek';
                                                            break;
                                                        case 'other':
                                                            echo 'Diğer';
                                                            break;
                                                        default:
                                                            echo $payment['payment_method'];
                                                    }
                                                ?>
                                            </td>
                                            <td><?php echo $payment['reference_no'] ? $payment['reference_no'] : '-'; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Seçilen tarih aralığında tahsilat kaydı bulunmamaktadır.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Supplier Payments Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Tedarikçi Ödemeleri (<?php echo date('d.m.Y', strtotime($data['start_date'])); ?> - <?php echo date('d.m.Y', strtotime($data['end_date'])); ?>)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="supplierPaymentsTable" width="100%" cellspacing="0">
                            <thead class="table-light">
                                <tr>
                                    <th>Ödeme No</th>
                                    <th>Tarih</th>
                                    <th>Tedarikçi</th>
                                    <th>Fatura No</th>
                                    <th>Tutar</th>
                                    <th>Ödeme Yöntemi</th>
                                    <th>Referans No</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($data['supplier_payments']) > 0): ?>
                                    <?php foreach($data['supplier_payments'] as $payment): ?>
                                        <tr>
                                            <td><?php echo $payment['id']; ?></td>
                                            <td><?php echo date('d.m.Y', strtotime($payment['payment_date'])); ?></td>
                                            <td><?php echo $payment['supplier_name']; ?></td>
                                            <td><?php echo $payment['invoice_no'] ? $payment['invoice_no'] : '-'; ?></td>
                                            <td><?php echo number_format($payment['amount'], 2, ',', '.') . ' ₺'; ?></td>
                                            <td>
                                                <?php
                                                    switch($payment['payment_method']) {
                                                        case 'cash':
                                                            echo 'Nakit';
                                                            break;
                                                        case 'bank_transfer':
                                                            echo 'Banka Havalesi';
                                                            break;
                                                        case 'credit_card':
                                                            echo 'Kredi Kartı';
                                                            break;
                                                        case 'check':
                                                            echo 'Çek';
                                                            break;
                                                        case 'other':
                                                            echo 'Diğer';
                                                            break;
                                                        default:
                                                            echo $payment['payment_method'];
                                                    }
                                                ?>
                                            </td>
                                            <td><?php echo $payment['reference_no'] ? $payment['reference_no'] : '-'; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center">Seçilen tarih aralığında tedarikçi ödemesi bulunmamaktadır.</td>
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