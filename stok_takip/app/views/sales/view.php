<?php
// app/views/sales/view.php
require_once 'app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Satış Detayları</h3>
                    <div>
                        <a href="<?php echo BASE_URL; ?>/sale" class="btn btn-secondary">
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
                            <p><strong>Fatura No:</strong> <?php echo $data['sale']['invoice_no']; ?></p>
                            <p><strong>Tarih:</strong> <?php echo date('d.m.Y', strtotime($data['sale']['sale_date'])); ?></p>
                            <?php if($data['sale']['due_date']): ?>
                                <p><strong>Vade Tarihi:</strong> <?php echo date('d.m.Y', strtotime($data['sale']['due_date'])); ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <h5>Müşteri Bilgileri</h5>
                            <p><strong>Müşteri:</strong> <?php echo $data['sale']['customer_name']; ?></p>
                            <p><strong>Müşteri Kodu:</strong> <?php echo $data['sale']['customer_code']; ?></p>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h5>Satış Detayları</h5>
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
                                <?php $i = 1; foreach($data['sale_details'] as $item): ?>
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
                                    <td colspan="2"><?php echo number_format($data['sale']['total_amount'], 2, ',', '.') . ' ₺'; ?></td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end"><strong>İndirim:</strong></td>
                                    <td colspan="2"><?php echo number_format($data['sale']['discount_amount'], 2, ',', '.') . ' ₺'; ?></td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end"><strong>KDV:</strong></td>
                                    <td colspan="2"><?php echo number_format($data['sale']['tax_amount'], 2, ',', '.') . ' ₺'; ?></td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end"><strong>Genel Toplam:</strong></td>
                                    <td colspan="2"><strong><?php echo number_format($data['sale']['net_amount'], 2, ',', '.') . ' ₺'; ?></strong></td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end"><strong>Ödenen Tutar:</strong></td>
                                    <td colspan="2"><?php echo number_format($data['sale']['paid_amount'], 2, ',', '.') . ' ₺'; ?></td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end"><strong>Kalan Tutar:</strong></td>
                                    <td colspan="2">
                                        <strong>
                                            <?php echo number_format($data['sale']['due_amount'], 2, ',', '.') . ' ₺'; ?>
                                            <?php if($data['sale']['payment_status'] == 'paid'): ?>
                                                <span class="badge bg-success">Ödendi</span>
                                            <?php elseif($data['sale']['payment_status'] == 'partially_paid'): ?>
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
                    
                    <?php if(!empty($data['sale']['note'])): ?>
                        <div class="mt-3">
                            <h5>Not</h5>
                            <p><?php echo nl2br($data['sale']['note']); ?></p>
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