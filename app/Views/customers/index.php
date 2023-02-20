<?= $this->extend('templates/default'); ?>

<?= $this->section('content'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <input type="hidden" id="edit_url" value="<?= url_to('customers.edit'); ?>" disabled>
            <input type="hidden" id="remove_url" value="<?= url_to('customers.delete'); ?>" disabled>
            <table id="customer_table" class="table table-striped table-hover nowrap" data-url="<?= url_to('customers.list'); ?>">
                <thead class="nowrap">
                    <tr>
                        <th>Actions</th>
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

<?= $this->include('customers/modal_addcustomer') ;?>

<?= $this->include('customers/modal_customerbranches') ;?>

<?= $this->include('customers/modal_addcustomerbranch') ;?>

<?= $this->include('templates/loading'); ?>
<?= $this->endSection(); ?>