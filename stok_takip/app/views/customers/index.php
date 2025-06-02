<?php
// app/views/customers/index.php
require_once 'app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Müşteriler</h3>
                    <a href="<?php echo BASE_URL; ?>/customer/create" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Yeni Müşteri
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
                    
                    <form action="<?php echo BASE_URL; ?>/customer/search" method="GET" class="mb-4">
                        <div class="input-group">
                            <input type="text" class="form-control" name="keyword" placeholder="Müşteri adı, kodu, telefonu veya email'i ara...">
                            <button class="btn btn-outline-secondary" type="submit">Ara</button>
                        </div>
                    </form>
                    
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Kod</th>
                                    <th>Müşteri Adı</th>
                                    <th>İletişim Kişisi</th>
                                    <th>Telefon</th>
                                    <th>E-posta</th>
                                    <th>Bakiye</th>
                                    <th>Durum</th>
                                    <th>İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($data['customers']) > 0): ?>
                                    <?php foreach($data['customers'] as $customer): ?>
                                        <tr>
                                            <td><?php echo $customer['code']; ?></td>
                                            <td><?php echo $customer['name']; ?></td>
                                            <td><?php echo $customer['contact_person'] ? $customer['contact_person'] : '-'; ?></td>
                                            <td><?php echo $customer['phone'] ? $customer['phone'] : '-'; ?></td>
                                            <td><?php echo $customer['email'] ? $customer['email'] : '-'; ?></td>
                                            <td>
                                                <?php if($customer['balance'] > 0): ?>
                                                    <span class="text-danger"><?php echo number_format($customer['balance'], 2, ',', '.') . ' ₺'; ?></span>
                                                <?php elseif($customer['balance'] < 0): ?>
                                                    <span class="text-success"><?php echo number_format(abs($customer['balance']), 2, ',', '.') . ' ₺'; ?></span>
                                                <?php else: ?>
                                                    <span><?php echo number_format($customer['balance'], 2, ',', '.') . ' ₺'; ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if($customer['status'] == 1): ?>
                                                    <span class="badge bg-success">Aktif</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Pasif</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?php echo BASE_URL; ?>/customer/viewCustomer/<?php echo $customer['id']; ?>" class="btn btn-sm btn-info">
    <i class="fas fa-eye"></i>
</a>
                                                <a href="<?php echo BASE_URL; ?>/customer/edit/<?php echo $customer['id']; ?>" class="btn btn-sm btn-warning">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>/customer/delete/<?php echo $customer['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bu müşteriyi silmek istediğinize emin misiniz?');">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center">Henüz müşteri bulunmamaktadır.</td>
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