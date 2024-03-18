<?=$this->extend('templates/default');?>
<?=$this->section('content');?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="mr-2 mb-2">
                        <strong>Filters by Status, With Vat? or Purchate Date (Start/End): </strong>
                    </div>
                    <div class="d-flex flex-md-row flex-column align-items-md-center">
                        <div class="mr-2 flex-fill mb-2 mb-md-0">
                            <select class="custom-select select2" id="filter_status" data-placeholder="Select a status" multiple style="width: 100%;">
                                <?php foreach (get_prf_status('', true) as $val => $text): ?>
                                    <option value="<?= $val ?>"><?= ucwords(str_replace('_', ' ', $text)) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mr-2 flex-fill mb-2 mb-md-0">
                            <select class="custom-select select2" id="filter_with_vat" style="width: 100%;" data-placeholder="With Vat?">
                                <option value=""></option>
                                <option value="1">Yes</option>
                                <option value="zero">No</option>
                            </select>
                        </div>
                        <div class="mr-2 flex-fill mb-2 mb-md-0">
                            <input type="date" class="form-control" name="filter_start_date" id="filter_start_date" placeholder="Start Date">
                        </div>
                        <div class="mr-2 flex-fill mb-2 mb-md-0">
                            <input type="date" class="form-control" name="filter_end_date" id="filter_end_date" placeholder="End Date">
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
                    <table id="order_form_table" class="table table-hover table-striped nowrap" width="100%">
                        <thead class="nowrap">
                            <tr> 
                                <th></th>
                                <th>Actions</th>
                                <th>Items</th>
                                <th>Status</th>
                                <th>Order Form #</th>
                                <th>Client</th>
                                <th>Client Branch</th>
                                <th>Purchased At</th>
                                <th>Total Amount</th>
                                <th>Total Discount</th>
                                <th>With Vat?</th>
                                <th>Vat Amount</th>
                                <th>Grand Total (w/ Vat)</th>
                                <th>Remarks</th>
                                <th>Created By</th>
                                <th>Created At</th>
                                <th>Item Out By</th>
                                <th>Item Out At</th>
                                <th>Received By</th>
                                <th>Received At</th>
                                <th>Filed By</th>
                                <th>Filed At</th>
                                <th>Rejected By</th>
                                <th>Rejected At</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->include('inventory/order_form/form'); ?>
<?= $this->include('inventory/order_form/items'); ?>
<?= $this->include('templates/loading'); ?>
<?=$this->endSection();?>