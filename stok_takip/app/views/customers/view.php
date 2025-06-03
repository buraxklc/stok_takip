<?php
// app/views/customers/view.php
require_once 'app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Müşteri Detayları</h3>
                    <div>
                        <a href="<?php echo BASE_URL; ?>/customer/edit/<?php echo $data['customer']['id']; ?>" class="btn btn-warning me-2">
                            <i class="fas fa-edit"></i> Düzenle
                        </a>
                        <a href="<?php echo BASE_URL; ?>/customer" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Geri
                        </a>
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
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h2><?php echo $data['customer']['name']; ?></h2>
                            <p class="text-muted"><?php echo $data['customer']['code']; ?></p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h5>Müşteri Bakiyesi:</h5>
                            <?php if($data['customer']['balance'] > 0): ?>
                                <h3 class="text-danger"><?php echo number_format($data['customer']['balance'], 2, ',', '.') . ' ₺'; ?></h3>
                                <p class="text-muted">Müşteriden alacaklısınız</p>
                            <?php elseif($data['customer']['balance'] < 0): ?>
                                <h3 class="text-success"><?php echo number_format(abs($data['customer']['balance']), 2, ',', '.') . ' ₺'; ?></h3>
                                <p class="text-muted">Müşteriye borçlusunuz</p>
                            <?php else: ?>
                                <h3><?php echo number_format($data['customer']['balance'], 2, ',', '.') . ' ₺'; ?></h3>
                                <p class="text-muted">Bakiye sıfır</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title">İletişim Bilgileri</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>İletişim Kişisi:</strong> <?php echo $data['customer']['contact_person'] ? $data['customer']['contact_person'] : '-'; ?></p>
                                    <p><strong>Telefon:</strong> <?php echo $data['customer']['phone'] ? $data['customer']['phone'] : '-'; ?></p>
                                    <p><strong>E-posta:</strong> <?php echo $data['customer']['email'] ? $data['customer']['email'] : '-'; ?></p>
                                    <p><strong>Adres:</strong> <?php echo $data['customer']['address'] ? nl2br($data['customer']['address']) : '-'; ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title">Vergi Bilgileri</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Vergi Dairesi:</strong> <?php echo $data['customer']['tax_office'] ? $data['customer']['tax_office'] : '-'; ?></p>
                                    <p><strong>Vergi Numarası:</strong> <?php echo $data['customer']['tax_number'] ? $data['customer']['tax_number'] : '-'; ?></p>
                                </div>
                            </div>
                            
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Diğer Bilgiler</h5>
                                </div>
                                <div class="card-body">
                                    <p>
                                        <strong>Durum:</strong> 
                                        <?php if($data['customer']['status'] == 1): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Pasif</span>
                                        <?php endif; ?>
                                    </p>
                                    <p><strong>Oluşturulma Tarihi:</strong> <?php echo date('d.m.Y H:i', strtotime($data['customer']['created_at'])); ?></p>
                                    <p><strong>Son Güncelleme:</strong> <?php echo date('d.m.Y H:i', strtotime($data['customer']['updated_at'])); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Son İşlemler</h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-center text-muted">Henüz işlem kaydı bulunmamaktadır.</p>
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