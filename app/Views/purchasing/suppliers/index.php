<?= $this->extend('templates/default'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">
    <input type="hidden" id="edit_url" value="<?= url_to('suppliers.edit') ?>" readonly>
    <input type="hidden" id="remove_url" value="<?= url_to('suppliers.delete') ?>" readonly>
    <input type="hidden" id="edit_url_brand" value="<?= url_to('suppliers.brand.edit') ?>" readonly>
    <input type="hidden" id="remove_url_brand" value="<?= url_to('suppliers.brand.delete') ?>" readonly>
    <!-- Suppliers List Card -->
    <div class="card">
        <div class="card-body">
            <table id="supplier_table" class="table table-hover table-striped nowrap" data-url="<?= url_to('suppliers.list'); ?>">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Supplier ID</th>
                        <th>Supplier Name</th>
                        <th>Supplier Type</th>
                        <th>Contact Person</th>
                        <th>Contact Number</th>
                        <th>Viber</th>
                        <th>Payment Terms</th>
                        <th>Mode of Payment</th>
                        <th>Product</th>
                        <th>Email Address</th>
                        <th>Bank Name</th>
                        <th>Bank Number</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<?= $this->include('purchasing/suppliers/modal') ?>
<?= $this->include('purchasing/supplier_brands/list') ?>
<?= $this->include('purchasing/supplier_brands/modal') ?>
<?= $this->include('templates/loading'); ?>
<?= $this->endSection(); ?>