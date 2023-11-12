<?=$this->extend('templates/default');?>
<?=$this->section('content');?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">                    
            <div class="card">
                <div class="card-header">
                    <label>Filters by [Status (S), Quotation Type (QT), Is Manual Quotation (IMQ) or Work Type (WT)]:</label>
                    <div class="input-group" style="flex-wrap: nowrap; width: 100%;">
                        <!-- Status -->
                        <div class="input-group-prepend">
                            <span class="input-group-text">S</span>
                        </div>
                        <select class="custom-select select2" id="filter_status" data-placeholder="Select a status" multiple>
                            <?php foreach (get_jo_status('', true) as $val => $text): ?>
                                <option value="<?= $val ?>"><?= ucfirst($text) ?></option>
                            <?php endforeach; ?>
                        </select>                        
                        <!-- Quotation Type -->
                        <div class="input-group-prepend ml-1">
                            <span class="input-group-text">QT</span>
                        </div>
                        <select class="custom-select select2" id="filter_qtype" data-placeholder="Select a quotation type" multiple>
                            <?php foreach (get_tasklead_type() as $val => $text): ?>
                                <option value="<?= $val ?>"><?= ucfirst($text) ?></option>
                            <?php endforeach; ?>
                        </select>             
                        <!-- Is Manual Quotation -->
                        <div class="input-group-prepend ml-1">
                            <span class="input-group-text">IMQ</span>
                        </div>
                        <select class="custom-select select2" id="filter_is_manual" data-placeholder="Is manual quotation?">
                            <option value="">All</option>
                            <option value="1">Yes</option>
                            <option value="zero">No</option>
                        </select>   
                        <!-- Work Type -->
                        <div class="input-group-prepend ml-1">
                            <span class="input-group-text">WT</span>
                        </div>
                        <select class="custom-select select2" id="filter_work_type" data-placeholder="Select a work type" multiple>
                            <?php foreach (get_work_type() as $val => $text): ?>
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
                <div class="card-footer">
                    <a href="<?= url_to('job_order.export'); ?>" class="btn btn-success">Export All Job Orders</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->include('admin/job_order/forms'); ?>
<?= $this->include('templates/loading'); ?>
<?=$this->endSection();?>
