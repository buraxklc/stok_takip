<?php
// app/views/layouts/sidebar.php
?>
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/dashboard" class="brand-link">
        <img src="/public/images/logo.png" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">Stok Takip</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="/dashboard" class="nav-link <?php echo ($_SERVER['REQUEST_URI'] == '/dashboard' || $_SERVER['REQUEST_URI'] == '/') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="/product" class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/product') === 0) ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-box"></i>
                        <p>Ürünler</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="/category" class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/category') === 0) ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-tags"></i>
                        <p>Kategoriler</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="/customer" class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/customer') === 0) ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Müşteriler</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="/supplier" class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/supplier') === 0) ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-truck"></i>
                        <p>Tedarikçiler</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>
                            Satışlar
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/sale" class="nav-link <?php echo ($_SERVER['REQUEST_URI'] == '/sale') ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Tüm Satışlar</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/sale/create" class="nav-link <?php echo ($_SERVER['REQUEST_URI'] == '/sale/create') ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Yeni Satış</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/customer-payment" class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/customer-payment') === 0) ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Tahsilatlar</p>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-cart-plus"></i>
                        <p>
                            Alımlar
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/purchase" class="nav-link <?php echo ($_SERVER['REQUEST_URI'] == '/purchase') ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Tüm Alımlar</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/purchase/create" class="nav-link <?php echo ($_SERVER['REQUEST_URI'] == '/purchase/create') ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Yeni Alım</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/supplier-payment" class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/supplier-payment') === 0) ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Ödemeler</p>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>
                            Raporlar
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/report/stock" class="nav-link <?php echo ($_SERVER['REQUEST_URI'] == '/report/stock') ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Stok Raporu</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/report/sales" class="nav-link <?php echo ($_SERVER['REQUEST_URI'] == '/report/sales') ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Satış Raporu</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/report/purchases" class="nav-link <?php echo ($_SERVER['REQUEST_URI'] == '/report/purchases') ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Alım Raporu</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/report/profit" class="nav-link <?php echo ($_SERVER['REQUEST_URI'] == '/report/profit') ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Kâr/Zarar Raporu</p>
                                
</a>
                        </li>
                        <li class="nav-item">
                            <a href="/report/customer" class="nav-link <?php echo ($_SERVER['REQUEST_URI'] == '/report/customer') ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Müşteri Raporu</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/report/supplier" class="nav-link <?php echo ($_SERVER['REQUEST_URI'] == '/report/supplier') ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Tedarikçi Raporu</p>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <?php if($_SESSION['user_role'] == 'admin'): ?>
                <li class="nav-item">
                    <a href="/user" class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/user') === 0) ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-user-cog"></i>
                        <p>Kullanıcılar</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="/setting" class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/setting') === 0) ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>Ayarlar</p>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>