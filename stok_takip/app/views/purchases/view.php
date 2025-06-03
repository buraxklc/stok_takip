<?php
// app/views/purchases/view.php
require_once 'app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Alım Detayları</h3>
                    <div>
                        <a href="<?php echo BASE_URL; ?>/purchase" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Geri
                        </a>
                        <button class="btn btn-primary" onclick="window.print()">
                            <i class="fas fa-print"></i> Yazdır
                        </button>
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
                        <div class="col-md-6">
                            <h5>Fatura Bilgileri</h5>
                            <p><strong>Fatura No:</strong> <?php echo $data['purchase']['invoice_no']; ?></p>
                            <p><strong>Tarih:</strong> <?php echo date('d.m.Y', strtotime($data['purchase']['purchase_date'])); ?></p>
                            <?php if($data['purchase']['due_date']): ?>
                                <p><strong>Vade Tarihi:</strong> <?php echo date('d.m.Y', strtotime($data['purchase']['due_date'])); ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <h5>Tedarikçi Bilgileri</h5>
                            <p><strong>Tedarikçi:</strong> <?php echo $data['purchase']['supplier_name']; ?></p>
                            <p><strong>Tedarikçi Kodu:</strong> <?php echo $data['purchase']['supplier_code']; ?></p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h5>Alım Detayları</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Ürün</th>
                                    <th>Miktar</th>
                                    <th>Birim Fiyat</th>
                                    <th>İndirim %</th>
                                    <th>KDV %</th>
                                    <th>Toplam</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; foreach($data['purchase_details'] as $item): ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td><?php echo $item['product_name'] . ' (' . $item['product_code'] . ')'; ?></td>
                                        <td><?php echo $item['quantity'] . ' ' . $item['unit']; ?></td>
                                        <td><?php echo number_format($item['unit_price'], 2, ',', '.') . ' ₺'; ?></td>
                                        <td><?php echo $item['discount_rate'] . '%'; ?></td>
                                        <td><?php echo $item['tax_rate'] . '%'; ?></td>
                                        <td><?php echo number_format($item['total_amount'], 2, ',', '.') . ' ₺'; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5" class="text-end"><strong>Ara Toplam:</strong></td>
                                    <td colspan="2"><?php echo number_format($data['purchase']['total_amount'], 2, ',', '.') . ' ₺'; ?></td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end"><strong>İndirim:</strong></td>
                                    <td colspan="2"><?php echo number_format($data['purchase']['discount_amount'], 2, ',', '.') . ' ₺'; ?></td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end"><strong>KDV:</strong></td>
                                    <td colspan="2"><?php echo number_format($data['purchase']['tax_amount'], 2, ',', '.') . ' ₺'; ?></td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end"><strong>Genel Toplam:</strong></td>
                                    <td colspan="2"><strong><?php echo number_format($data['purchase']['net_amount'], 2, ',', '.') . ' ₺'; ?></strong></td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end"><strong>Ödenen Tutar:</strong></td>
                                    <td colspan="2"><?php echo number_format($data['purchase']['paid_amount'], 2, ',', '.') . ' ₺'; ?></td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end"><strong>Kalan Tutar:</strong></td>
                                    <td colspan="2">
                                        <strong>
                                            <?php echo number_format($data['purchase']['due_amount'], 2, ',', '.') . ' ₺'; ?>
                                            <?php if($data['purchase']['payment_status'] == 'paid'): ?>
                                                <span class="badge bg-success">Ödendi</span>
                                            <?php elseif($data['purchase']['payment_status'] == 'partially_paid'): ?>
                                                <span class="badge bg-warning">Kısmi Ödeme</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Ödenmedi</span>
                                            <?php endif; ?>
                                        </strong>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <?php if(!empty($data['purchase']['note'])): ?>
                        <div class="mt-3">
                            <h5>Not</h5>
                            <p><?php echo nl2br($data['purchase']['note']); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .navbar, .footer, .btn, .alert {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .card-header {
        background-color: white !important;
        color: black !important;
        border-bottom: 1px solid #ddd !important;
    }
    
    body {
        padding: 0 !important;
        margin: 0 !important;
    }
    
    .container {
        width: 100% !important;
        max-width: none !important;
        padding: 0 !important;
        margin: 0 !important;
    }
}
</style>

<?php require_once 'app/views/layouts/footer.php'; ?>