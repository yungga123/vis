<?= $this->extend('templates/default'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="mr-2 mb-2">
                        <strong>Filters by Supplier Type, Payment Terms, or Mode of Payment: </strong>
                    </div>
                    <div class="d-flex flex-md-row flex-column align-items-md-center">
                        <div class="mr-2 flex-fill mb-2 mb-md-0">
                            <select class="custom-select select2" id="filter_supplier_type" data-placeholder="Select a supplier type" multiple style="width: 100%;">
                                <?php foreach (get_supplier_type() as $val => $text): ?>
                                    <option value="<?= $val ?>"><?= ucfirst($text) ?></option>
                                <?php endforeach; ?>
                            </select>        
                        </div>
                        <div class="mr-2 flex-fill mb-2 mb-md-0">
                            <select class="custom-select select2" id="filter_payment_terms" data-placeholder="Select a payment term" multiple style="width: 100%;">
                                <?php foreach (get_payment_terms('', true) as $val => $text): ?>
                                    <option value="<?= $val ?>"><?= ucfirst($text) ?></option>
                                <?php endforeach; ?>
                            </select>           
                        </div>
                        <div class="mr-2 flex-fill mb-2 mb-md-0">
                            <select class="custom-select select2" id="filter_payment_mode" data-placeholder="Select an MOP" multiple style="width: 100%;">
                                <?php foreach (get_supplier_mop() as $val => $text): ?>
                                    <option value="<?= $val ?>"><?= ucfirst($text) ?></option>
                                <?php endforeach; ?>
                            </select> 
                        </div>
                        <div class="align-items-center justify-content-center d-flex">
                            <button class="btn btn-outline-primary mr-1" title="Filter" onclick="filterData()">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="btn btn-outline-secondary" title="Reset" onclick="filterData(true)">
                                <i class="fas fa-ban"></i>
                            </button>
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