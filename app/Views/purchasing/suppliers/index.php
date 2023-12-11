<?= $this->extend('templates/default'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <label>Filters by [Supplier Type (ST), Payment Terms (PT), or Mode of Payment (MOP)]:</label>
                    <div class="input-group" style="flex-wrap: nowrap; width: 100%;">
                        <!-- Supplier Type -->
                        <div class="input-group-prepend">
                            <span class="input-group-text">ST</span>
                        </div>
                        <select class="custom-select select2" id="filter_supplier_type" data-placeholder="Select a supplier type" multiple>
                            <?php foreach (get_supplier_type() as $val => $text): ?>
                                <option value="<?= $val ?>"><?= ucfirst($text) ?></option>
                            <?php endforeach; ?>
                        </select>                        
                        <!-- Payment Terms -->
                        <div class="input-group-prepend ml-1">
                            <span class="input-group-text">PT</span>
                        </div>
                        <select class="custom-select select2" id="filter_payment_terms" data-placeholder="Select a payment term" multiple>
                            <?php foreach (get_payment_terms('', true) as $val => $text): ?>
                                <option value="<?= $val ?>"><?= ucfirst($text) ?></option>
                            <?php endforeach; ?>
                        </select>             
                        <!-- Mode of Payment -->
                        <div class="input-group-prepend ml-1">
                            <span class="input-group-text">MOP</span>
                        </div>
                        <select class="custom-select select2" id="filter_payment_mode" data-placeholder="Select an MOP" multiple>
                            <?php foreach (get_supplier_mop() as $val => $text): ?>
                                <option value="<?= $val ?>"><?= ucfirst($text) ?></option>
                            <?php endforeach; ?>
                        </select> 
                        <!-- Action Buttons -->
                        <div class="input-group-append">
                            <button class="btn btn-outline-primary px-3" onclick="filterData()" type="button" title="Search filter">Filter</button>
                            <button class="btn btn-outline-secondary px-3 rounded-right" onclick="filterData(true)" type="button" title="Reset filter">Reset</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table id="supplier_table" class="table table-hover table-striped nowrap">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Actions</th>
                                <th>Supplier ID</th>
                                <th>Supplier Name</th>
                                <th>Supplier Type</th>
                                <th>Address</th>
                                <th>Contact Person</th>
                                <th>Contact Number</th>
                                <th>Viber</th>
                                <th>Email Address</th>
                                <th>Payment Terms</th>
                                <th>Mode of Payment</th>
                                <th>Product</th>
                                <th>Bank Name</th>
                                <th>Bank Account Name</th>
                                <th>Bank Number</th>
                                <th>Remarks</th>
                                <th>Created By</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->include('purchasing/suppliers/modal') ?>
<?= $this->include('purchasing/supplier_brands/list') ?>
<?= $this->include('purchasing/supplier_brands/modal') ?>
<?= $this->include('templates/loading'); ?>
<?= $this->endSection(); ?>