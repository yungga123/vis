<?= $this->extend('templates/default'); ?>

<?= $this->section('content'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <a class="btn btn-success disabled" href="#" role="button">Commercial</a>
            <a class="btn btn-success" href="<?= url_to('customersresidential.home') ?>" role="button">Residential</a>
            <div class="float-right">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="filterby">Filter by</label>
                    </div>
                    <select class="custom-select" name="filter" id="filterby">
                        <option value="all" selected>All</option>
                        <option value="YES">New Clients</option>
                        <option value="NO">Official</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <input type="hidden" id="edit_url" value="<?= url_to('customervt.edit'); ?>" disabled>
            <input type="hidden" id="remove_url" value="<?= url_to('customervt.delete'); ?>" disabled>
            <input type="hidden" id="forecast_url" value="<?= url_to('customervt.listget'); ?>" disabled>
            <table id="customervt_table" class="table table-hover table-striped nowrap" data-url="<?= url_to('customervt.list'); ?>">
                <thead class="nowrap">
                    <tr>
                        <th>Actions</th>
                        <th>New Client?</th>
                        <th>Client ID</th>
                        <th>Client Name</th>
                        <th>Contact Person</th>
                        <th>Address</th>
                        <th>Contact Number</th>
                        <th>Email Address</th>
                        <th>Source</th>
                        <th>Notes</th>
                        <th>Referred By</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<?= $this->include('customers_vt/modal_customerbranches') ;?>
<?= $this->include('customers_vt/modal_addcustomervt') ;?>
<?= $this->include('customers_branch/modal_addcustomervtbranch') ;?>
<?= $this->include('templates/loading'); ?>
<?= $this->endSection(); ?>