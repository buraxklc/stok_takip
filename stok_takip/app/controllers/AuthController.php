<?php
// app/controllers/AuthController.php

class AuthController extends Controller {
    private $userModel;
    
    public function __construct() {
        $this->userModel = $this->model('User');
    }
    
    public function login() {
        // Eğer kullanıcı zaten giriş yapmışsa ana sayfaya yönlendir
        if(isset($_SESSION['user_id'])) {
            $this->redirect('/');
        }
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Form verilerini al
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);
            
            // Kullanıcıyı veritabanında ara
            $user = $this->userModel->findByUsername($username);
            
            // Kullanıcı bulunduysa ve şifre doğruysa
            if($user && password_verify($password, $user['password'])) {
                // Oturum başlat
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                
                // Ana sayfaya yönlendir
                $this->redirect('/');
            } else {
                // Hata mesajı göster
                $_SESSION['error'] = "Kullanıcı adı veya şifre hatalı!";
                $this->view('auth/login', ['username' => $username]);
            }
            
            return;
        }
        
        $this->view('auth/login');
    }
    
    public function logout() {
        // Oturumu sonlandır
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_role']);
        session_destroy();
        
        // Login sayfasına yönlendir
        $this->redirect('/auth/login');
    }
    
    // Basit bir kayıt sayfası
    public function register() {
        // Eğer kullanıcı zaten giriş yapmışsa ana sayfaya yönlendir
        if(isset($_SESSION['user_id'])) {
            $this->redirect('/');
        }
        
        if($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Form verilerini al
            $username = trim($_POST['username']);
            $password = trim($_POST['password']);
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            
            // Şifre hashle
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Kullanıcı verileri
            $userData = [
                'username' => $username,
                'password' => $hashedPassword,
                'name' => $name,
                'email' => $email,
                'role' => 'admin'
            ];
            
            // Kullanıcıyı oluştur
            if($this->userModel->create($userData)) {
                $_SESSION['success'] = "Kullanıcı başarıyla oluşturuldu!";
                $this->redirect('/auth/login');
            } else {
                $_SESSION['error'] = "Kullanıcı oluşturulurken bir hata oluştu!";
                $this->view('auth/register');
            }
            
            return;
        }
        
        // Register formu göster
        $this->view('auth/register');
    }
}