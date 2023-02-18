<?=$this->extend('templates/default');?>
<?=$this->section('content');?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="error-page">
                <h2 class="headline text-warning"> 403</h2>

                <div class="error-content">
                <h3><i class="fas fa-exclamation-triangle text-warning"></i> Oops! You have no access to this page.</h3>

                <p>
                    Click here to <a href="<?= base_url('/'); ?>">return to dashboard</a>.
                </p>
            </div>
            <!-- /.error-content -->
        </div>
    </div>
</div>
<?=$this->endSection();?>
