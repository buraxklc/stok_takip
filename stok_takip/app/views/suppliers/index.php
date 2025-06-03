<?php
// app/views/suppliers/index.php
require_once 'app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Tedarikçiler</h3>
                    <a href="<?php echo BASE_URL; ?>/supplier/create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Yeni Tedarikçi
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
                    
                    <form action="<?php echo BASE_URL; ?>/supplier/search" method="GET" class="mb-4">
                        <div class="input-group">
                            <input type="text" class="form-control" name="keyword" placeholder="Tedarikçi adı, kodu, telefonu veya email'i ara...">
                            <button class="btn btn-outline-secondary" type="submit">Ara</button>
                        </div>
                    </form>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Kod</th>
                                    <th>Tedarikçi Adı</th>
                                    <th>İletişim Kişisi</th>
                                    <th>Telefon</th>
                                    <th>E-posta</th>
                                    <th>Bakiye</th>
                                    <th>Durum</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($data['suppliers']) > 0): ?>
                                    <?php foreach($data['suppliers'] as $supplier): ?>
                                        <tr>
                                            <td><?php echo $supplier['code']; ?></td>
                                            <td><?php echo $supplier['name']; ?></td>
                                            <td><?php echo $supplier['contact_person'] ? $supplier['contact_person'] : '-'; ?></td>
                                            <td><?php echo $supplier['phone'] ? $supplier['phone'] : '-'; ?></td>
                                            <td><?php echo $supplier['email'] ? $supplier['email'] : '-'; ?></td>
                                            <td>
                                                <?php if($supplier['balance'] > 0): ?>
                                                    <span class="text-danger"><?php echo number_format($supplier['balance'], 2, ',', '.') . ' ₺'; ?></span>
                                                <?php elseif($supplier['balance'] < 0): ?>
                                                    <span class="text-success"><?php echo number_format(abs($supplier['balance']), 2, ',', '.') . ' ₺'; ?></span>
                                                <?php else: ?>
                                                    <span><?php echo number_format($supplier['balance'], 2, ',', '.') . ' ₺'; ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($supplier['status'] == 1): ?>
                                                    <span class="badge bg-success">Aktif</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Pasif</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?php echo BASE_URL; ?>/supplier/viewSupplier/<?php echo $supplier['id']; ?>" class="btn btn-sm btn-info">
    <i class="fas fa-eye"></i>
</a>
                                                <a href="<?php echo BASE_URL; ?>/supplier/edit/<?php echo $supplier['id']; ?>" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>/supplier/delete/<?php echo $supplier['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bu tedarikçiyi silmek istediğinize emin misiniz?');">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">Henüz tedarikçi bulunmamaktadır.</td>
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