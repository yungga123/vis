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
            <div class="image" style="margin-top: 7px;">
                <img style="height: 40px; width: 40px; border: 2px solid #adb5bd;"
                src="<?= get_current_user_avatar(); ?>" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info" style="line-height: 1.3rem;">
                <a href="<?= url_to('account.profile') ?>" class="d-block" title="Click here to view profile."><strong><?= esc(session('name')); ?></strong></a>
                <span style="color: #c2c7d0a8;"><?= get_roles(session('access_level')); ?></span>
            </div>
        </div>
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="<?= site_url('dashboard') ?>" class="nav-link <?= (url_is('dashboard') ? "active" : "") ;?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <!-- User sidebar menus -->
                <?= get_sidebar_menus(); ?>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>

        