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
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3 col-6">
                    <!-- small card -->
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h4>Add Employee</h4>

                            <p>Click proceed</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <a href="<?= site_url('add-employee') ?>" class="small-box-footer">
                            Proceed <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small card -->
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h4>List of Employees</h4>

                            <p>Click proceed</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <a href="<?= site_url('employee-list') ?>" class="small-box-footer">
                            Proceed <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <!-- small card -->
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h4>Enroll an account</h4>

                            <p>Click proceed</p>
                        </div>
                        <div class="icon">
                            <i class="far fa-user-circle"></i>
                        </div>
                        <a href="<?= site_url('add-account') ?>" class="small-box-footer">
                            Proceed <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <!-- small card -->
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h4>List of Accounts</h4>

                            <p>Click proceed</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-table"></i>
                        </div>
                        <a href="<?= site_url('list-account') ;?>" class="small-box-footer">
                            Proceed <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </section>

</div>