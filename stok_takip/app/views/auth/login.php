<?php
// app/views/auth/login.php
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap - Stok Takip Sistemi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-form {
            width: 100%;
            max-width: 400px;
            padding: 15px;
            margin: auto;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .login-form h1 {
            text-align: center;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="login-form">
        <h1>Stok Takip Sistemi</h1>
        
        <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php 
                echo $_SESSION['error']; 
                unset($_SESSION['error']);
            ?>
        </div>
        <?php endif; ?>
        
        <form action="<?php echo BASE_URL; ?>/auth/login" method="POST">
            <div class="form-group mb-3">
                <label for="username">Kullanıcı Adı</label>
                <input type="text" name="username" id="username" class="form-control" value="<?php echo isset($data['username']) ? $data['username'] : ''; ?>" required>
            </div>
            
            <div class="form-group mb-3">
                <label for="password">Şifre</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            
            <div class="form-group mb-3">
                <button type="submit" class="btn btn-primary btn-block w-100">Giriş Yap</button>
            </div>
        </form>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>