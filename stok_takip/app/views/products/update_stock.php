<?php
// app/views/products/update_stock.php
require_once 'app/views/layouts/header.php';
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card">
                <div class="card-header">
                    <h3>Stok Güncelleme</h3>
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
                    
                    <div class="mb-4">
                        <h5><?php echo $data['product']['name']; ?></h5>
                        <p>Mevcut Stok: <strong><?php echo $data['product']['stock_quantity'] . ' ' . $data['product']['unit']; ?></strong></p>
                    </div>
                    
                    <form action="<?php echo BASE_URL; ?>/product/updateStock/<?php echo $data['product']['id']; ?>" method="POST">
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Stok Değişimi</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="quantity" name="quantity" step="1" value="0" required>
                                <span class="input-group-text"><?php echo $data['product']['unit']; ?></span>
                            </div>
                            <div class="form-text">Stok eklemek için pozitif değer, azaltmak için negatif değer girin.</div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Güncelle</button>
                            <a href="<?php echo BASE_URL; ?>/product/view/<?php echo $data['product']['id']; ?>" class="btn btn-secondary">İptal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'app/views/layouts/footer.php'; ?>