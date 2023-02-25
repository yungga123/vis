<?= $this->extend('templates/default'); ?>

<?= $this->section('content'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <a class="btn btn-success" href="<?= url_to('customervt.home') ?>" role="button">Commercial</a>
            <a class="btn btn-success disabled" href="#" role="button">Residential</a>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <input type="hidden" id="edit_url" value="<?= url_to('customersresidential.edit'); ?>" disabled>
            <input type="hidden" id="remove_url" value="<?= url_to('customersresidential.delete'); ?>" disabled>
            <table id="customers_residential_table" class="table table-bordered table-striped nowrap" data-url="<?= url_to('customersresidential.list'); ?>">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Forecast</th>
                        <th>ID</th>
                        <th>Customer Name</th>
                        <th>Contact Person</th>
                        <th>Address</th>
                        <th>Contact Number</th>
                        <th>Email Address</th>
                        <th>Source</th>
                        <th>Notes</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<?= $this->include('customers_residential/modal_addcustomerresidential') ?>
<?= $this->include('templates/loading'); ?>
<?= $this->endSection(); ?>