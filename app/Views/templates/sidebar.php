<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
        <span class="brand-text font-weight-light p-4">Vinculum Technologies</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="info">
                <a href="<?= url_to('account.profile') ?>" class="d-block" title="Click here to view profile.">Hello! <strong><?= esc(session('name')); ?></strong></a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                <li class="nav-header">DASHBOARD</li>
                <li class="nav-item">
                    <a href="<?= site_url('dashboard') ?>" class="nav-link <?= (url_is('dashboard') ? "active" : "") ;?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Main Dashboard
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= site_url("sales-dashboard") ?>" class="nav-link <?= (url_is('sales-dashboard') ? "active" : "") ;?>">
                        <i class="nav-icon far fa-credit-card"></i>
                        <p>
                            Sales Dashboard
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= site_url("admin-dashboard") ?>" class="nav-link <?= (url_is('admin-dashboard') ? "active" : "") ;?>">
                        <i class="nav-icon fas fa-building"></i>
                        <p>
                            Admin Dashboard
                        </p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= site_url("executive-overview") ?>" class="nav-link <?= (url_is('executive-overview') ? "active" : "") ;?>">
                        <i class="nav-icon fas fa-user-tie"></i>
                        <p>
                            Executive Overview
                        </p>
                    </a>
                </li>

                <li class="nav-header">INVENTORY</li>

                <li class="nav-item">
                    <a href="<?= site_url("inventory/menu") ?>" class="nav-link <?= (url_is('inventory/menu') ? "active" : "") ;?>">
                        <i class="nav-icon fas fa-warehouse"></i>
                        <p>
                            Inventory
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>