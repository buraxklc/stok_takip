<?php
// app/views/payments/supplier_search.php
require_once 'app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Arama Sonuçları: "<?php echo $data['keyword']; ?>"</h3>
                    <a href="<?php echo BASE_URL; ?>/payment/supplier" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Tüm Ödemeler
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Ödeme No</th>
                                    <th>Tedarikçi</th>
                                    <th>Tarih</th>
                                    <th>Tutar</th>
                                    <th>Ödeme Yöntemi</th>
                                    <th>Referans No</th>
                                    <th>Fatura No</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($data['payments']) > 0): ?>
                                    <?php foreach($data['payments'] as $payment): ?>
                                        <tr>
                                            <td><?php echo $payment['id']; ?></td>
                                            <td><?php echo $payment['supplier_name']; ?></td>
                                            <td><?php echo date('d.m.Y', strtotime($payment['payment_date'])); ?></td>
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
                                            <td><?php echo $payment['invoice_no'] ? $payment['invoice_no'] : '-'; ?></td>
                                            <td>
                                                <a href="<?php echo BASE_URL; ?>/payment/viewSupplierPayment/<?php echo $payment['id']; ?>" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>/payment/deleteSupplierPayment/<?php echo $payment['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bu ödemeyi silmek istediğinize emin misiniz? Bu işlem geri alınamaz.');">
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