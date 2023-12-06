<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?></title>

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('assets') ?>/plugins/fontawesome-free/css/all.min.css">

    <?php if (isset($toastr) && $toastr): ?>
    <!-- Toastr -->
    <link rel="stylesheet" href="<?=base_url('assets')?>/plugins/toastr/toastr.min.css">
    <?php endif;?>

    <?php if (isset($sweetalert2) && $sweetalert2): ?>
    <!-- Sweetalert2 -->
    <link rel="stylesheet" href="<?=base_url('assets')?>/plugins/sweetalert2/sweetalert2.min.css">
    <?php endif;?>

    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url('assets') ?>/dist/css/adminlte.min.css">
</head>

<body class="hold-transition login-page">
    <div class="login-box">
        <!-- /.login-logo -->
        <div class="card card-outline card-primary">
            <div class="card-header text-center">
                <a href="#" class="h1"><b>Vinculum </b>Technologies</a>
            </div>
            <div class="card-body">
                <p class="login-box-msg">Sign in to start your session</p>
                <form id="form_login" action="<?= url_to('login.authenticate'); ?>" method="POST" autocomplete="off">
                    <?= csrf_field(); ?>
                    <div class="input-group mb-3">
                        <input name="username" type="text" class="form-control" placeholder="Username">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-user"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input name="password" type="password" class="form-control" placeholder="Password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-8">
                        </div>
                        <!-- /.col -->
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block btn-login">Sign In</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.login-box -->

    <?= $this->include('templates/loading'); ?>

    <!-- jQuery -->
    <script src="<?= base_url('assets') ?>/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="<?= base_url('assets') ?>/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>

    <?php if (isset($toastr) && $toastr): ?>
    <!-- Toastr -->
    <script src="<?=base_url('assets')?>/plugins/toastr/toastr.min.js"></script>
    <?php endif;?>

    <?php if (isset($sweetalert2) && $sweetalert2): ?>
    <!-- Sweetalert2 -->
    <script src="<?=base_url('assets')?>/plugins/sweetalert2/sweetalert2.min.js"></script>
    <?php endif;?>

    <!-- AdminLTE App -->
    <script src="<?= base_url('assets') ?>/dist/js/adminlte.min.js"></script>
    <!-- General custom js -->
    <script src="<?=base_url('assets')?>/custom/js/initialize.js"></script>
    <script src="<?= base_url('assets') ?>/custom/js/functions.js"></script>
    <!-- Login js -->
    <script src="<?= base_url('assets') ?>/custom/js/login.js"></script>
</body>

</html>