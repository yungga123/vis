<?= $this->extend('templates/default'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="input-group" style="flex-wrap: nowrap; width: 100%;">
                <div class="input-group-prepend">
                    <label class="input-group-text">Filter by Percent (Except 100%)</label>
                </div>
                <select class="custom-select select2" id="filter_status" data-placeholder="Select a percent" multiple>
                    <option value="10.00%">10%</option>
                    <option value="30.00%">30%</option>
                    <option value="50.00%">50%</option>
                    <option value="70.00%">70%</option>
                    <option value="90.00%">90%</option>
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
                        <th>Qtn Number</th>
                        <th>Forecast Close Date</th>
                        <th>Min. Forecast</th>
                        <th>Max Forecast</th>
                        <th>Hit?</th>
                        <th>Remark Next Step</th>
                        <th>Close Deal Date</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Duration</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="card-footer">
            <div class="float-right">
                <a class="btn btn-success" href="<?= url_to('tasklead.booked.home') ?>">View Booked Taskleads</a>
            </div>
        </div>
    </div>
</div>
<?= $this->include('sales/task_lead/tasklead_form'); ?>
<?= $this->include('templates/loading'); ?>
<?= $this->endSection(); ?>