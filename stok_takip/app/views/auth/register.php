<?php
// app/views/auth/register.php
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol - Stok Takip Sistemi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .register-form {
            width: 100%;
            max-width: 500px;
            padding: 15px;
            margin: auto;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .register-form h1 {
            text-align: center;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="register-form">
        <h1>Stok Takip Sistemi - Kayıt Ol</h1>
        
        <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php 
                echo $_SESSION['error']; 
                unset($_SESSION['error']);
            ?>
        </div>
        <?php endif; ?>
        
        <form action="<?php echo BASE_URL; ?>/auth/register" method="POST">
            <div class="form-group mb-3">
                <label for="username">Kullanıcı Adı</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>
            
            <div class="form-group mb-3">
                <label for="password">Şifre</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            
            <div class="form-group mb-3">
                <label for="name">Ad Soyad</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>
            
            <div class="form-group mb-3">
                <label for="email">E-posta</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            
            <div class="form-group mb-3">
                <button type="submit" class="btn btn-primary btn-block w-100">Kayıt Ol</button>
            </div>
            
            <div class="text-center">
                <a href="<?php echo BASE_URL; ?>/auth/login">Giriş Yap</a>
            </div>
        </form>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>