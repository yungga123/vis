<?= $this->extend('templates/default'); ?>

<?= $this->section('content'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <input type="hidden" id="edit_url" value="<?= url_to('customervt.edit'); ?>" disabled>
            <input type="hidden" id="remove_url" value="<?= url_to('customervt.delete'); ?>" disabled>
            <table id="customervt_table" class="table table-bordered table-striped nowrap" data-url="<?= url_to('customervt.list'); ?>">
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

<?= $this->include('customers_vt/modal_customerbranches') ;?>
<?= $this->include('customers_vt/modal_addcustomervt') ;?>
<?= $this->include('templates/loading'); ?>
<?= $this->endSection(); ?>