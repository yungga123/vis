<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?= $page_title ?></h1>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="error-page">
            <h2 class="headline text-success">OK!</h2>

            <div class="error-content">
                <h3><i class="far fa-check-circle text-success"></i> Tasklead Updated.</h3>

                <p>
                    The tasklead has been updated to <?= $status ?>
                    <a href="<?= $href ?>">Click here!</a> to return to table.
                </p>
            </div>
        </div>
        <!-- /.error-page -->

    </section>
    <!-- /.content -->
</div>