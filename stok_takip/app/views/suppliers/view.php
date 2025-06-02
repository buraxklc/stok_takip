<?php
// app/views/suppliers/view.php
require_once 'app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Tedarikçi Detayları</h3>
                    <div>
                        <a href="<?php echo BASE_URL; ?>/supplier/edit/<?php echo $data['supplier']['id']; ?>" class="btn btn-warning me-2">
                            <i class="fas fa-edit"></i> Düzenle
                        </a>
                        <a href="<?php echo BASE_URL; ?>/supplier" class="btn btn-secondary">
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
                            <h2><?php echo $data['supplier']['name']; ?></h2>
                            <p class="text-muted"><?php echo $data['supplier']['code']; ?></p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h5>Tedarikçi Bakiyesi:</h5>
                            <?php if($data['supplier']['balance'] > 0): ?>
                                <h3 class="text-danger"><?php echo number_format($data['supplier']['balance'], 2, ',', '.') . ' ₺'; ?></h3>
                                <p class="text-muted">Tedarikçiye borçlusunuz</p>
                            <?php elseif($data['supplier']['balance'] < 0): ?>
                                <h3 class="text-success"><?php echo number_format(abs($data['supplier']['balance']), 2, ',', '.') . ' ₺'; ?></h3>
                                <p class="text-muted">Tedarikçiden alacaklısınız</p>
                            <?php else: ?>
                                <h3><?php echo number_format($data['supplier']['balance'], 2, ',', '.') . ' ₺'; ?></h3>
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
                                    <p><strong>İletişim Kişisi:</strong> <?php echo $data['supplier']['contact_person'] ? $data['supplier']['contact_person'] : '-'; ?></p>
                                    <p><strong>Telefon:</strong> <?php echo $data['supplier']['phone'] ? $data['supplier']['phone'] : '-'; ?></p>
                                    <p><strong>E-posta:</strong> <?php echo $data['supplier']['email'] ? $data['supplier']['email'] : '-'; ?></p>
                                    <p><strong>Adres:</strong> <?php echo $data['supplier']['address'] ? nl2br($data['supplier']['address']) : '-'; ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title">Vergi Bilgileri</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Vergi Dairesi:</strong> <?php echo $data['supplier']['tax_office'] ? $data['supplier']['tax_office'] : '-'; ?></p>
                                    <p><strong>Vergi Numarası:</strong> <?php echo $data['supplier']['tax_number'] ? $data['supplier']['tax_number'] : '-'; ?></p>
                                </div>
                            </div>
                            
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Diğer Bilgiler</h5>
                                </div>
                                <div class="card-body">
                                    <p>
                                        <strong>Durum:</strong> 
                                        <?php if($data['supplier']['status'] == 1): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Pasif</span>
                                        <?php endif; ?>
                                    </p>
                                    <p><strong>Oluşturulma Tarihi:</strong> <?php echo date('d.m.Y H:i', strtotime($data['supplier']['created_at'])); ?></p>
                                    <p><strong>Son Güncelleme:</strong> <?php echo date('d.m.Y H:i', strtotime($data['supplier']['updated_at'])); ?></p>
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