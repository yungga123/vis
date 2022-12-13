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
                <a href="#" class="d-block">Hi! <?= $_SESSION['name'] ?></a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->

                <li class="nav-item menu-open">
                    <a href="#" class="nav-link active">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <p>
                        Dashboard
                        <i class="right fas fa-angle-left"></i>
                    </p>
                    </a>
                    <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="<?= site_url('dashboard') ?>" class="nav-link<?php if(url_is('dashboard')){ echo " active";} ?>">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Main Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= site_url("sales-dashboard") ?>" class="nav-link<?php if(url_is('sales-dashboard')){ echo " active";} ?>">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Sales Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= site_url("admin-dashboard") ?>" class="nav-link<?php if(url_is('admin-dashboard')){ echo " active";} ?>">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Admin Dashboard</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= site_url("executive-overview") ?>" class="nav-link<?php if(url_is('executive-overview')){ echo " active";} ?>">
                        <i class="far fa-circle nav-icon"></i>
                        <p>Executive Overview</p>
                        </a>
                    </li>
                    </ul>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>