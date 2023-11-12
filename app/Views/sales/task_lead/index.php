<?= $this->extend('templates/default'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <label>Filters by [Client Type, Percent (Except 100%) or Quarter]:</label>
            <div class="input-group" style="flex-wrap: nowrap; width: 100%;">
                <div class="input-group-prepend">
                    <span class="input-group-text">Client Type</span>
                </div>
                <select class="custom-select select2" id="filter_client_type" data-placeholder="Select a client type">
                    <option value="">All</option>
                    <option value="Commercial">Commercial</option>
                    <option value="Residential">Residential</option>
                </select>  
                <div class="input-group-prepend ml-1">
                    <span class="input-group-text">Percent</span>
                </div>
                <select class="custom-select select2" id="filter_status" data-placeholder="Select a percent" multiple>
                    <option value="10.00%">10%</option>
                    <option value="30.00%">30%</option>
                    <option value="50.00%">50%</option>
                    <option value="70.00%">70%</option>
                    <option value="90.00%">90%</option>
                </select>
                <div class="input-group-prepend ml-1">
                    <span class="input-group-text">Quarter</span>
                </div>
                <select class="custom-select select2" id="filter_quarter" data-placeholder="Select a quarter" multiple>
                    <option value="1">1st Quarter</option>
                    <option value="2">2nd Quarter</option>
                    <option value="3">3rd Quarter</option>
                    <option value="4">4th Quarter</option>
                </select>  
                <div class="input-group-append">
                    <button class="btn btn-outline-primary px-3" onclick="filterData()" type="button" title="Search filter">Filter</button>
                    <button class="btn btn-outline-secondary px-3 rounded-right" onclick="filterData(true)" type="button" title="Reset filter">Reset</button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <input type="hidden" id="get_quotation_num" value="<?= isset($quotation_num) ? $quotation_num : "" ?>">
            <table id="tasklead_table" class="table table-hover table-striped nowrap">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Tasklead ID</th>
                        <th>Employee Name</th>
                        <th>Quarter</th>
                        <th>Percent</th>
                        <th>Status</th>
                        <th>Client Type</th>
                        <th>Client Name</th>
                        <th>Branch Name</th>
                        <th>Contact Number</th>
                        <th>Project</th>
                        <th>Amount</th>
                        <th>Quotation Number</th>
                        <th>Quotation Type</th>
                        <th>Forecast Close Date</th>
                        <th>Min. Forecast</th>
                        <th>Max Forecast</th>
                        <th>Hit?</th>
                        <th>Remark Next Step</th>
                        <th>Close Deal Date</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Duration</th>
                        <th>Created By</th>
                        <th>Created At</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-between">
                <div>
                    <a href="<?= url_to('tasklead.export'); ?>" class="btn btn-success">Export All Task Leads Except Booked</a>
                    <a href="<?= url_to('tasklead.export') . '?booked=true'; ?>" class="btn btn-success">Export All Booked Task Leads</a>
                </div>
                <a href="<?= url_to('tasklead.booked.home') ?>" class="btn btn-success">View Booked Task Leads</a>
            </div>
        </div>
    </div>
</div>
<?= $this->include('sales/task_lead/tasklead_form'); ?>
<?= $this->include('templates/loading'); ?>
<?= $this->endSection(); ?>