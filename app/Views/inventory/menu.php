<?= $this->extend('templates/default'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="row">

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h4>Add Item</h4>
                    <p>Register new items.</p>
                </div>
                <div class="icon">
                    <i class="fas fa-plus"></i>
                </div>
                <a href="<?= site_url('inventory/add-item') ;?>" class="small-box-footer">
                    Proceed <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

    </div>
</div>
<?= $this->endSection(); ?>