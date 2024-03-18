<?=$this->extend('templates/default');?>
<?=$this->section('content');?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">                    
            <div class="card">
                <div class="card-header">
                    <div class="mr-2 mb-2">
                        <strong>Filters by Status, Quotation Type, Is Manual Quotation or Work Type: </strong>
                    </div>
                    <div class="d-flex flex-md-row flex-column align-items-md-center">
                        <div class="mr-2 flex-fill mb-2 mb-md-0">
                            <select class="custom-select select2" id="filter_status" data-placeholder="Select a status" multiple style="width: 100%;">
                                <?php foreach (get_jo_status('', true) as $val => $text): ?>
                                    <option value="<?= $val ?>"><?= ucfirst($text) ?></option>
                                <?php endforeach; ?>
                            </select>   
                        </div>
                        <div class="mr-2 flex-fill mb-2 mb-md-0">
                            <select class="custom-select select2" id="filter_qtype" data-placeholder="Select a quotation type" multiple style="width: 100%;">
                                <?php foreach (get_tasklead_type() as $val => $text): ?>
                                    <option value="<?= $val ?>"><?= ucfirst($text) ?></option>
                                <?php endforeach; ?>
                            </select>  
                        </div>
                        <div class="mr-2 flex-fill mb-2 mb-md-0">
                            <select class="custom-select select2" id="filter_is_manual" data-placeholder="Is manual quotation?" style="width: 100%;">
                                <option value="">All</option>
                                <option value="1">Yes</option>
                                <option value="zero">No</option>
                            </select>   
                        </div>
                        <div class="mr-2 flex-fill mb-2 mb-md-0">
                            <select class="custom-select select2" id="filter_work_type" data-placeholder="Select a work type" multiple style="width: 100%;">
                                <?php foreach (get_work_type() as $val => $text): ?>
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
                    <table id="job_order_table" class="table table-hover table-striped nowrap">
                        <thead class="nowrap">
                            <tr>
                                <th></th>
                                <th>Action</th>
                                <th>Status</th>
                                <th>JO #</th>
                                <th>Task Lead #</th>
                                <th>Manual Quotation?</th>
                                <th>Quotation</th>
                                <th>Quotation Type</th>
                                <th>Client</th>
                                <th>Client Branch</th>
                                <th>Manager</th>
                                <th>Work Type</th>
                                <th>Date Requested</th>
                                <th>Date Committed</th>
                                <th>Date Reported</th>
                                <th>Warranty</th>
                                <th>Comments</th>
                                <th>Remarks</th>
                                <th>Requested By</th>
                                <th>Requested At</th>
                                <th>Accepted By</th>
                                <th>Accepted At</th>
                                <th>Filed By</th>
                                <th>Filed At</th>
                                <th>Discarded By</th>
                                <th>Discarded At</th>
                                <th>Reverted By</th>
                                <th>Reverted At</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->include('admin/job_order/forms'); ?>
<?= $this->include('templates/loading'); ?>
<?=$this->endSection();?>
