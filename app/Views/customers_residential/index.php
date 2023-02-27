<?= $this->extend('templates/default'); ?>

<?= $this->section('content'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <a class="btn btn-success" href="<?= url_to('customervt.home') ?>" role="button">Commercial</a>
            <a class="btn btn-success disabled" href="#" role="button">Residential</a>
            <div class="float-right">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="filterby">Filter by</label>
                    </div>
                    <select class="custom-select" name="filter" id="filterby">
                        <option value="all" selected>All</option>
                        <option value="YES">Forecast</option>
                        <option value="NO">Official</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <input type="hidden" id="edit_url" value="<?= url_to('customersresidential.edit'); ?>" disabled>
            <input type="hidden" id="remove_url" value="<?= url_to('customersresidential.delete'); ?>" disabled>
            <table id="customers_residential_table" class="table table-hover table-striped nowrap" data-url="<?= url_to('customersresidential.list'); ?>">
                <thead class="nowrap">
                    <tr>
                        <th>Actions</th>
                        <th>Forecast?</th>
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