<?= $this->extend('templates/default'); ?>

<?= $this->section('content'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <input type="hidden" id="edit_url" value="<?= url_to('customers.edit'); ?>" disabled>
            <input type="hidden" id="remove_url" value="<?= url_to('customers.delete'); ?>" disabled>
            <table id="customer_table" class="table table-bordered table-striped nowrap" data-url="<?= url_to('customers.list'); ?>">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Branches</th>
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
                <tfoot>
                    <tr>
                        <th>Action</th>
                        <th>Branches</th>
                        <th>ID</th>
                        <th>Customer Name</th>
                        <th>Contact Person</th>
                        <th>Address</th>
                        <th>Contact Number</th>
                        <th>Email Address</th>
                        <th>Source</th>
                        <th>Notes</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?= $this->include('customers/modal_addcustomer') ;?>

<?= $this->include('customers/modal_customerbranches') ;?>

<?= $this->include('customers/modal_addcustomerbranch') ;?>

<?= $this->include('templates/loading'); ?>
<?= $this->endSection(); ?>