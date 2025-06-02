<?php
// app/views/customers/search.php
require_once 'app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3>Arama Sonuçları: "<?php echo $data['keyword']; ?>"</h3>
                    <a href="<?php echo BASE_URL; ?>/customer" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Tüm Müşteriler
                    </a>
                </div>
                <div class="card-body">
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