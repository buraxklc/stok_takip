<?php
// app/views/payments/customer_view.php
require_once 'app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Tahsilat Detayları</h3>
                    <div>
                        <a href="<?php echo BASE_URL; ?>/payment/customer" class="btn btn-secondary">
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
                            <h5>Tahsilat Bilgileri</h5>
                            <p><strong>Tahsilat No:</strong> <?php echo $data['payment']['id']; ?></p>
                            <p><strong>Tarih:</strong> <?php echo date('d.m.Y', strtotime($data['payment']['payment_date'])); ?></p>
                            <p><strong>Tutar:</strong> <?php echo number_format($data['payment']['amount'], 2, ',', '.') . ' ₺'; ?></p>
                            <p>
                                <strong>Ödeme Yöntemi:</strong>
                                <?php
                                    switch($data['payment']['payment_method']) {
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
                                            echo $data['payment']['payment_method'];
                                    }
                                ?>
                            </p>
                            <?php if($data['payment']['reference_no']): ?>
                                <p><strong>Referans No:</strong> <?php echo $data['payment']['reference_no']; ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <h5>Müşteri Bilgileri</h5>
                            <p><strong>Müşteri:</strong> <?php echo $data['payment']['customer_name']; ?></p>
                            <p><strong>Müşteri Kodu:</strong> <?php echo $data['payment']['customer_code']; ?></p>
                            <?php if($data['payment']['invoice_no']): ?>
                                <p><strong>Fatura No:</strong> <?php echo $data['payment']['invoice_no']; ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if($data['payment']['notes']): ?>
                        <div class="mt-4">
                            <h5>Notlar</h5>
                            <p><?php echo nl2br($data['payment']['notes']); ?></p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer text-muted">
                    <small>Oluşturulma Tarihi: <?php echo date('d.m.Y H:i', strtotime($data['payment']['created_at'])); ?></small>
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