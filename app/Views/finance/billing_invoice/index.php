<?=$this->extend('templates/default');?>
<?=$this->section('content');?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">                    
            <div class="card">
				<div class="card-header">
                    <div class="mr-2 mb-2">
                        <strong>Filters by Status, Bill Type or Payment Method: </strong>
                    </div>
                    <div class="d-flex flex-md-row flex-column align-items-md-center">
                        <div class="mr-2 flex-fill mb-2 mb-md-0">
                            <select class="custom-select select2" id="filter_billing_status" data-placeholder="Select a billing status" multiple style="width: 100%;">
                                <?php foreach (get_billing_status() as $val => $text): ?>
                                    <option value="<?= $val ?>"><?= $text ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mr-2 flex-fill mb-2 mb-md-0">
                            <select class="custom-select select2" id="filter_bill_type" data-placeholder="Select a bill type" multiple style="width: 100%;">
                                <?php foreach (get_bill_types() as $val => $text): ?>
                                    <option value="<?= $val ?>"><?= $text ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mr-2 flex-fill mb-2 mb-md-0">
                            <select class="custom-select select2" id="filter_payment_method" data-placeholder="Select a payment method" multiple style="width: 100%;">
                                <?php foreach (get_supplier_mop() as $val => $text): ?>
                                    <option value="<?= $val ?>"><?= $text ?></option>
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
                    <table id="billing_invoice_table" class="table table-hover table-striped nowrap">
                        <thead class="nowrap">
                            <tr>
                                <th></th>
                                <th>Actions</th>
                                <th>Status</th>
                                <th>Billing Status</th>
                                <th>Billing ID</th>
                                <th>TaskLead ID</th>
                                <th>Quotation #</th>
                                <th>Client</th>
                                <th>Manager</th>
                                <th>Quotation Type</th>
                                <th>Due Date</th>
                                <th>Bill Type</th>
                                <th>Payment Method</th>
                                <th>Billing Amount</th>
                                <th>Overdue Interest</th>
                                <th>Amount Paid</th>
                                <th>Paid At</th>
                                <th>Attention To</th>
                                <th>With Vat?</th>
                                <th>Vat Amount</th>
                                <th>Created By</th>
                                <th>Created At</th>
                                <th>Approved By</th>
                                <th>Approved At</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->include('finance/billing_invoice/form'); ?>
<?= $this->include('templates/loading'); ?>
<?=$this->endSection();?>
