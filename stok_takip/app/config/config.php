<?php
// app/config/config.php

// Uygulama sabitleri
define('APP_NAME', 'Stok Takip Sistemi');
define('APP_VERSION', '1.0.0');

// URL ayarları - projenin kök dizinini belirt
define('BASE_URL', 'http://localhost/stok_takip');

// Veritabanı ayarları
define('DB_HOST', 'localhost');
define('DB_NAME', 'stok_takip');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Zaman dilimi ayarları
date_default_timezone_set('Europe/Istanbul');

// Oturum süresi (saniye)
define('SESSION_LIFETIME', 3600); // 1 saat

// Hata raporlama ayarları
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);