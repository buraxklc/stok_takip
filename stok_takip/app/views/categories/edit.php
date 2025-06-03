<?php
// app/views/categories/edit.php
require_once 'app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3>Kategori Düzenle</h3>
                </div>
                <div class="card-body">
                    <?php if(isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?php 
                                echo $_SESSION['error']; 
                                unset($_SESSION['error']);
                            ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?php echo BASE_URL; ?>/category/edit/<?php echo $data['category']['id']; ?>" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Kategori Adı</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo $data['category']['name']; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="parent_id" class="form-label">Üst Kategori</label>
                            <select class="form-select" id="parent_id" name="parent_id">
                                <option value="">Ana Kategori</option>
                                <?php foreach($data['mainCategories'] as $mainCategory): ?>
                                    <?php if($mainCategory['id'] != $data['category']['id']): // Kendisini üst kategori olarak seçmeyi engelle ?>
                                        <option value="<?php echo $mainCategory['id']; ?>" <?php echo ($mainCategory['id'] == $data['category']['parent_id']) ? 'selected' : ''; ?>>
                                            <?php echo $mainCategory['name']; ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Eğer alt kategori oluşturmak istiyorsanız, üst kategori seçin.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Açıklama</label>
                            <textarea class="form-control" id="description" name="description" rows="3"><?php echo $data['category']['description']; ?></textarea>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="status" name="status" <?php echo ($data['category']['status'] == 1) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="status">Aktif</label>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Güncelle</button>
                            <a href="<?php echo BASE_URL; ?>/category" class="btn btn-secondary">İptal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'app/views/layouts/footer.php'; ?>