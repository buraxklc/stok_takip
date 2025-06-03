<?php
// app/views/sales/index.php
require_once 'app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Satışlar</h3>
                    <a href="<?php echo BASE_URL; ?>/sale/create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Yeni Satış
                    </a>
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
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Fatura No</th>
                                    <th>Müşteri</th>
                                    <th>Tarih</th>
                                    <th>Toplam Tutar</th>
                                    <th>Ödenen Tutar</th>
                                    <th>Kalan Tutar</th>
                                    <th>Durum</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($data['sales']) > 0): ?>
                                    <?php foreach($data['sales'] as $sale): ?>
                                        <tr>
                                            <td><?php echo $sale['invoice_no']; ?></td>
                                            <td><?php echo $sale['customer_name']; ?></td>
                                            <td><?php echo date('d.m.Y', strtotime($sale['sale_date'])); ?></td>
                                            <td><?php echo number_format($sale['net_amount'], 2, ',', '.') . ' ₺'; ?></td>
                                             <td><?php echo number_format($sale['paid_amount'], 2, ',', '.') . ' ₺'; ?></td>
                                            <td><?php echo number_format($sale['due_amount'], 2, ',', '.') . ' ₺'; ?></td>
                                            <td>
                                                <?php if($sale['payment_status'] == 'paid'): ?>
                                                    <span class="badge bg-success">Ödendi</span>
                                                <?php elseif($sale['payment_status'] == 'partially_paid'): ?>
                                                    <span class="badge bg-warning">Kısmi Ödeme</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Ödenmedi</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?php echo BASE_URL; ?>/sale/viewSale/<?php echo $sale['id']; ?>" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>/sale/delete/<?php echo $sale['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bu satışı silmek istediğinize emin misiniz? Bu işlem geri alınamaz.');">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">Henüz satış kaydı bulunmamaktadır.</td>
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