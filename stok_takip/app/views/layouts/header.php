<?php
// app/views/layouts/header.php
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stok Takip Sistemi</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #858796;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --light-color: #f8f9fc;
            --dark-color: #5a5c69;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fc;
            padding-top: 56px;
        }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 56px;
            left: 0;
            width: 250px;
            height: calc(100vh - 56px);
            background-color: #4e73df;
            background-image: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
            transition: all 0.3s;
            z-index: 1000;
            overflow-y: auto;
        }
        
        .sidebar-collapsed {
            width: 0;
            overflow: hidden;
        }
        
        .sidebar .nav-item {
            position: relative;
            margin-bottom: 3px;
        }
        
        .sidebar .nav-item .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 1rem;
            display: flex;
            align-items: center;
        }
        
        .sidebar .nav-item .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .sidebar .nav-item .nav-link.active {
            color: #fff;
            font-weight: 600;
            background-color: rgba(255, 255, 255, 0.15);
        }
        
        .sidebar .nav-item .nav-link i {
            margin-right: 0.5rem;
            width: 1.25rem;
            text-align: center;
        }
        
        .sidebar .nav-item .collapse,
        .sidebar .nav-item .collapsing {
            margin: 0 1rem;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 0.35rem;
        }
        
        .sidebar .nav-item .collapse .nav-link,
        .sidebar .nav-item .collapsing .nav-link {
            padding: 0.75rem 1rem;
            margin: 0;
            color: rgba(255, 255, 255, 0.8);
        }
        
        .sidebar .nav-item .collapse .nav-link:hover,
        .sidebar .nav-item .collapsing .nav-link:hover {
            color: #fff;
        }
        
        .sidebar-divider {
            border-top: 1px solid rgba(255, 255, 255, 0.15);
            margin: 1rem 0;
        }
        
        .sidebar-heading {
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05rem;
            padding: 0.5rem 1rem;
        }
        
        /* Main Content */
        .content {
            margin-left: 250px;
            padding: 1.5rem;
            transition: all 0.3s;
        }
        
        .content-fluid {
            margin-left: 0;
        }
        
        /* Cards */
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
        }
        
        /* Dashboard stats */
        .stat-card {
            border-left: 4px solid;
            border-radius: 0.5rem;
        }
        
        .stat-card-primary {
            border-left-color: var(--primary-color);
        }
        
        .stat-card-success {
            border-left-color: var(--success-color);
        }
        
        .stat-card-warning {
            border-left-color: var(--warning-color);
        }
        
        .stat-card-danger {
            border-left-color: var(--danger-color);
        }
        
        .stat-card .stat-icon {
            font-size: 2rem;
            opacity: 0.3;
        }
        
        /* Tables */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .table th {
            background-color: #f8f9fc;
            font-weight: 600;
        }
        
        /* Forms */
        .form-control:focus, .form-select:focus {
            border-color: #bac8f3;
            box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #2e59d9;
            border-color: #2653d4;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                top: 0;
                overflow: hidden;
                max-height: 0;
                transition: max-height 0.3s ease-out;
            }
            
            .sidebar.show {
                max-height: 1000px;
                transition: max-height 0.3s ease-in;
            }
            
            .content {
                margin-left: 0;
                padding: 1rem;
            }
            
            body {
                padding-top: 56px;
            }
            
            .sidebar-toggle {
                display: block !important;
            }
        }
        
        /* Image styles */
        .img-product {
            object-fit: cover;
            width: 50px;
            height: 50px;
        }
        
        .img-product-detail {
            max-height: 300px;
            object-fit: contain;
        }
        
        /* Utilities */
        .text-xs {
            font-size: 0.7rem;
        }
        
        .text-primary {
            color: var(--primary-color) !important;
        }
        
        .text-success {
            color: var(--success-color) !important;
        }
        
        .text-warning {
            color: var(--warning-color) !important;
        }
        
        .text-danger {
            color: var(--danger-color) !important;
        }
        
        .bg-gradient-primary {
            background-color: #4e73df;
            background-image: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
        }
        
        /* Animations */
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        /* Dropdown menu style */
        .topbar .dropdown-menu {
            animation: growIn 0.2s ease;
        }
        
        @keyframes growIn {
            0% {
                transform: scale(0.9);
                opacity: 0;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-gradient-primary fixed-top shadow">
        <div class="container-fluid">
            <button class="navbar-toggler border-0 sidebar-toggle me-2" type="button" id="sidebarToggle">
                <span class="navbar-toggler-icon"></span>
            </button>
            <a class="navbar-brand fw-bold" href="<?php echo BASE_URL; ?>">
                <i class="fas fa-boxes me-2"></i>Stok Takip
            </a>
            
            <div class="d-flex ms-auto">
                <div class="navbar-nav">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="d-none d-lg-inline text-white me-2"><?php echo $_SESSION['user_name']; ?></span>
                            <i class="fas fa-user-circle fa-fw text-white"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userDropdown">
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/user/profile"><i class="fas fa-user fa-sm fa-fw me-2 text-secondary"></i>Profil</a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/user/settings"><i class="fas fa-cogs fa-sm fa-fw me-2 text-secondary"></i>Ayarlar</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/auth/logout"><i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-secondary"></i>Çıkış Yap</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Sidebar -->
    <div class="sidebar shadow" id="sidebar">
        <div class="py-2"></div>
        
        <!-- Nav Items -->
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo ($_SERVER['REQUEST_URI'] == BASE_URL . '/' || $_SERVER['REQUEST_URI'] == BASE_URL . '/dashboard') ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <div class="sidebar-divider"></div>
            <div class="sidebar-heading">Envanter</div>
            
            <li class="nav-item">
                <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], BASE_URL . '/category') === 0) ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/category">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Kategoriler</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], BASE_URL . '/product') === 0) ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/product">
                    <i class="fas fa-fw fa-box"></i>
                    <span>Ürünler</span>
                </a>
            </li>
            
            <div class="sidebar-divider"></div>
            <div class="sidebar-heading">İşlemler</div>
            
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseCustomers" aria-expanded="false" aria-controls="collapseCustomers">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Müşteriler</span>
                </a>
                <div id="collapseCustomers" class="collapse" data-bs-parent="#sidebar">
                    <div class="collapse-inner">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/customer">Tüm Müşteriler</a>
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/customer/create">Yeni Müşteri</a>
                    </div>
                </div>
            </li>
            
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseSuppliers" aria-expanded="false" aria-controls="collapseSuppliers">
                    <i class="fas fa-fw fa-truck"></i>
                    <span>Tedarikçiler</span>
                </a>
                <div id="collapseSuppliers" class="collapse" data-bs-parent="#sidebar">
                    <div class="collapse-inner">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/supplier">Tüm Tedarikçiler</a>
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/supplier/create">Yeni Tedarikçi</a>
                    </div>
                </div>
            </li>
            
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseSales" aria-expanded="false" aria-controls="collapseSales">
                    <i class="fas fa-fw fa-shopping-cart"></i>
                    <span>Satışlar</span>
                </a>
                <div id="collapseSales" class="collapse" data-bs-parent="#sidebar">
                    <div class="collapse-inner">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/sale">Tüm Satışlar</a>
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/sale/create">Yeni Satış</a>
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/payment/customer">Tahsilatlar</a>
                    </div>
                </div>
            </li>
            
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapsePurchases" aria-expanded="false" aria-controls="collapsePurchases">
                    <i class="fas fa-fw fa-shopping-basket"></i>
                    <span>Alımlar</span>
                </a>
                <div id="collapsePurchases" class="collapse" data-bs-parent="#sidebar">
                    <div class="collapse-inner">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/purchase">Tüm Alımlar</a>
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/purchase/create">Yeni Alım</a>
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/payment/supplier">Ödemeler</a>
                    </div>
                </div>
            </li>
            
            <div class="sidebar-divider"></div>
            <div class="sidebar-heading">Raporlar</div>
            
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseReports" aria-expanded="false" aria-controls="collapseReports">
                    <i class="fas fa-fw fa-chart-bar"></i>
                    <span>Raporlar</span>
                </a>
                <div id="collapseReports" class="collapse" data-bs-parent="#sidebar">
                    <div class="collapse-inner">
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/report/stock">Stok Raporu</a>
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/report/sales">Satış Raporu</a>
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/report/purchases">Alım Raporu</a>
                        <a class="nav-link" href="<?php echo BASE_URL; ?>/report/profit">Kâr/Zarar Raporu</a>
                    </div>
                </div>
            </li>
            
            <?php if(isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'): ?>
                <div class="sidebar-divider"></div>
                <div class="sidebar-heading">Yönetim</div>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], BASE_URL . '/user') === 0) ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/user">
                        <i class="fas fa-fw fa-user-cog"></i>
                        <span>Kullanıcılar</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], BASE_URL . '/setting') === 0) ? 'active' : ''; ?>" href="<?php echo BASE_URL; ?>/setting">
                        <i class="fas fa-fw fa-cogs"></i>
                        <span>Ayarlar</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="content" id="content">