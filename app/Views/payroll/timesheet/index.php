<?=$this->extend('templates/default');?>
<?=$this->section('content');?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <?= $this->include('payroll/timesheet/attendance'); ?>
            <div class="card">
				<div class="card-header">
                    <div class="d-flex flex-md-row flex-sm-column align-items-md-center">
                        <div class="mr-2 mb-sm-2 mb-md-0">
                            <strong>Date Range Filter: </strong>
                        </div>
                        <div class="mr-2 flex-fill mb-sm-2 mb-md-0">
                            <input type="date" class="form-control" name="filter_start_date" id="filter_start_date" placeholder="Clock Date">
                        </div>
                        <div class="mr-2 flex-fill mb-sm-2 mb-md-0">
                            <input type="date" class="form-control" name="filter_end_date" id="filter_end_date" placeholder="Clock Date">
                        </div>
                        <div class="mr-2 flex-fill mb-sm-2 mb-md-0 <?= $can_view_all ? '' : 'd-none' ?>">
                            <select name="view" id="filter_view" class="form-control">
                                <option value="" selected>All Timesheets</option>
                                <option value="my_timesheets">My Timesheets</option>
                            </select>
                        </div>
                        <div class="align-items-center justify-content-sm-center d-sm-flex">
                            <button class="btn btn-outline-primary mr-sm-1" title="Filter" onclick="filterData()">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="btn btn-outline-secondary" title="Reset" onclick="filterData(true)">
                                <i class="fas fa-ban"></i>
                            </button>
                        </div>
                    </div>
				</div>
                <div class="card-body">
                    <table id="timesheet_table" class="table table-hover table-striped nowrap">
                        <thead class="nowrap">
                            <tr>
                                <th></th>
                                <th>Actions</th>
                                <th>Employee ID</th>
                                <th>Employee Name</th>
                                <th>Clock Date</th>
                                <th>Clock In</th>
                                <th>Clock Out</th>
                                <th>Total Hours (Less 1hr Break)</th>
                                <th>Early In</th>
                                <th>Late</th>
                                <th>Early Out</th>
                                <th>Overtime</th>
                                <th>Clock Type</th>
                                <th>Remarks</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->include('payroll/timesheet/form'); ?>
<?= $this->include('templates/loading'); ?>
<?=$this->endSection();?>
