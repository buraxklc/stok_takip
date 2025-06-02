<?php
// Kök dizindeki index.php - bütün istekleri buraya yönlendiriyoruz

// Oturum başlat
session_start();

// Gerekli dosyaları dahil et
require_once 'app/config/config.php';
require_once 'app/core/App.php';
require_once 'app/core/Controller.php';
require_once 'app/core/Model.php';

// Uygulama başlat
$app = new App();