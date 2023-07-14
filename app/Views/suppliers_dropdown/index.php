<?= $this->extend('templates/default'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <input type="hidden" id="edit_url" value="" readonly>
    <input type="hidden" id="remove_url" value="" readonly>
    <!-- Suppliers List Card -->
    <div class="card">
        <div class="card-body">


            <table id="supplier_dropdowns_table" class="table table-bordered table-striped nowrap" data-url="<?= url_to('suppliers.dropdown.list'); ?>">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Dropdown ID</th>
                        <th>Dropdown</th>
                        <th>Type</th>
                    </tr>
                </thead>
            </table>

        </div>
    </div>
</div>

<?= $this->include('templates/loading'); ?>
<?= $this->endSection(); ?>