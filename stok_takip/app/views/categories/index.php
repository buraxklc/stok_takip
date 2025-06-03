<?php
// app/views/categories/index.php
require_once 'app/views/layouts/header.php';
?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Kategoriler</h1>
        <a href="<?php echo BASE_URL; ?>/category/create" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-plus fa-sm text-white-50 me-1"></i> Yeni Kategori
        </a>
    </div>

    <!-- Alerts -->
    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Content Row -->
    <div class="row">
        <div class="col-12">
            <!-- Categories Table -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Tüm Kategoriler</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Kategori İşlemleri:</div>
                            <a class="dropdown-item" href="<?php echo BASE_URL; ?>/category/create">Yeni Kategori</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="<?php echo BASE_URL; ?>/product">Ürünlere Git</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th>Kategori Adı</th>
                                    <th>Üst Kategori</th>
                                    <th>Açıklama</th>
                                    <th width="10%">Durum</th>
                                    <th width="15%">İşlemler</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(count($data['categories']) > 0): ?>
                                    <?php foreach($data['categories'] as $index => $category): ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo $category['name']; ?></td>
                                            <td>
                                                <?php echo $category['parent_name'] ? $category['parent_name'] : '<span class="text-muted">Ana Kategori</span>'; ?>
                                            </td>
                                            <td><?php echo $category['description'] ? $category['description'] : '<span class="text-muted">-</span>'; ?></td>
                                            <td>
                                                <?php if($category['status'] == 1): ?>
                                                    <span class="badge bg-success">Aktif</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Pasif</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?php echo BASE_URL; ?>/product/byCategory/<?php echo $category['id']; ?>" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Ürünleri Göster">
                                                    <i class="fas fa-list"></i>
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>/category/edit/<?php echo $category['id']; ?>" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Düzenle">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>/category/delete/<?php echo $category['id']; ?>" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Sil" onclick="return confirm('Bu kategoriyi silmek istediğinize emin misiniz?');">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Henüz kategori bulunmamaktadır.</td>
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