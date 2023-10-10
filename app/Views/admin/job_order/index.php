<?=$this->extend('templates/default');?>
<?=$this->section('content');?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">                    
            <div class="card">
                <div class="card-header">
                    <label>Filters by (Status, Quotation Type and Work Type):</label>
                    <div class="input-group" style="flex-wrap: nowrap; width: 100%;">
                        <!-- Status -->
                        <div class="input-group-prepend">
                            <span class="input-group-text">Status</span>
                        </div>
                        <select class="custom-select select2" id="filter_status" data-placeholder="Select a status" multiple>
                        </select>
                        
                        <!-- Quotation Type -->
                        <div class="input-group-prepend ml-1">
                            <span class="input-group-text">Q Type</span>
                        </div>
                        <select class="custom-select select2" id="filter_qtype" data-placeholder="Select a quotation type" multiple>
                        </select>
                        
                        <!-- Work Type -->
                        <div class="input-group-prepend ml-1">
                            <span class="input-group-text">Work Type</span>
                        </div>
                        <select class="custom-select select2" id="filter_worktype" data-placeholder="Select a work type" multiple>
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
                                <th>Action</th>
                                <th>Status</th>
                                <th>JO #</th>
                                <th>Task Lead #</th>
                                <th>Quotation</th>
                                <th>Q Type</th>
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
