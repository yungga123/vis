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
    <?= $this->include('templates/styles') ;?>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Navbar -->
        <?= $this->include('templates/navbar') ;?>
        <!-- /.navbar -->

        <!-- SideBar -->
        <?= $this->include('templates/sidebar') ;?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0"><?= $page_title ?></h1>
                        </div><!-- /.col -->
                        <div class="col-sm-6">
                            <?php if (isset($can_add) && $can_add): ?>
                            <button type="button" class="btn btn-primary float-right"><i class="fas fa-plus-circle" id="btn_add_record"></i> Add New Record</button>
                            <?php endif; ?>
                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.container-fluid -->
            </div>
            <!-- /.content-header -->
            
            <section class="content">
                <?= $this->renderSection('content') ?>
            </section>
        </div>

    <footer class="main-footer">
        <strong>Copyright &copy; 2022 <a href="#">Vinculum Technologies</a>.</strong>
        All rights reserved.
        <div class="float-right d-none d-sm-inline-block">
            <b>Version</b> 3.0
        </div>
    </footer>

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->
        <!-- Control Sidebar -->
        <aside class="control-sidebar control-sidebar-dark">
            <!-- Control sidebar content goes here -->
        </aside>
        <!-- /.control-sidebar -->
    </div>
    <!-- ./wrapper -->

    <?= $this->include('templates/javascripts') ;?>
</body>
</html>