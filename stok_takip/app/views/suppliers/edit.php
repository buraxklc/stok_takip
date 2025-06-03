<?php
// app/views/suppliers/edit.php
require_once 'app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3>Tedarikçi Düzenle</h3>
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
                    
                    <form action="<?php echo BASE_URL; ?>/supplier/edit/<?php echo $data['supplier']['id']; ?>" method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="code" class="form-label">Tedarikçi Kodu</label>
                                <input type="text" class="form-control" id="code" name="code" value="<?php echo $data['supplier']['code']; ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Tedarikçi Adı</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo $data['supplier']['name']; ?>" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="contact_person" class="form-label">İletişim Kişisi</label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person" value="<?php echo $data['supplier']['contact_person']; ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Telefon</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="<?php echo $data['supplier']['phone']; ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">E-posta</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $data['supplier']['email']; ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="address" class="form-label">Adres</label>
                            <textarea class="form-control" id="address" name="address" rows="3"><?php echo $data['supplier']['address']; ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tax_office" class="form-label">Vergi Dairesi</label>
                                <input type="text" class="form-control" id="tax_office" name="tax_office" value="<?php echo $data['supplier']['tax_office']; ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="tax_number" class="form-label">Vergi Numarası</label>
                                <input type="text" class="form-control" id="tax_number" name="tax_number" value="<?php echo $data['supplier']['tax_number']; ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="status" name="status" <?php echo ($data['supplier']['status'] == 1) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="status">Aktif</label>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Güncelle</button>
                            <a href="<?php echo BASE_URL; ?>/supplier" class="btn btn-secondary">İptal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'app/views/layouts/footer.php'; ?>