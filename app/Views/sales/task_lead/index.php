<?= $this->extend('templates/default'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="mr-2 mb-2">
                <strong>Filters by Client Type, Percent (Except 100%) or Quarter: </strong>
            </div>
            <div class="d-flex flex-md-row flex-column align-items-md-center">
                <div class="mr-2 flex-fill mb-2 mb-md-0">
                    <select class="custom-select select2" id="filter_client_type" data-placeholder="Select a client type" style="width: 100%;">
                        <option value="">All</option>
                        <option value="Commercial">Commercial</option>
                        <option value="Residential">Residential</option>
                    </select>  
                </div>
                <div class="mr-2 flex-fill mb-2 mb-md-0">
                    <select class="custom-select select2" id="filter_status" data-placeholder="Select a percent" multiple style="width: 100%;">
                        <option value="10.00%">10%</option>
                        <option value="30.00%">30%</option>
                        <option value="50.00%">50%</option>
                        <option value="70.00%">70%</option>
                        <option value="90.00%">90%</option>
                    </select>
                </div>
                <div class="mr-2 flex-fill mb-2 mb-md-0">
                    <select class="custom-select select2" id="filter_quarter" data-placeholder="Select a quarter" multiple style="width: 100%;">
                        <option value="1">1st Quarter</option>
                        <option value="2">2nd Quarter</option>
                        <option value="3">3rd Quarter</option>
                        <option value="4">4th Quarter</option>
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
            <input type="hidden" id="get_quotation_num" value="<?= isset($quotation_num) ? $quotation_num : "" ?>">
            <table id="tasklead_table" class="table table-hover table-striped nowrap">
                <thead>
                    <tr>
                        <th width="1%"></th>
                        <th>Action</th>
                        <th>Tasklead ID</th>
                        <th>Employee Name</th>
                        <th>Quarter</th>
                        <th>Percent</th>
                        <th>Status</th>
                        <th>Client Name</th>
                        <th>Client Type</th>
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
            <a href="<?= url_to('tasklead.booked.home') ?>" class="btn btn-success">View Booked Task Leads</a>
        </div>
    </div>
</div>
<?= $this->include('sales/task_lead/tasklead_form'); ?>
<?= $this->include('templates/loading'); ?>
<?= $this->endSection(); ?>