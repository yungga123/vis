<?=$this->extend('templates/default');?>
<?=$this->section('content');?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">                    
            <div class="card">
				<div class="card-header">
                    <div class="mr-2 mb-2">
                        <strong>Date Range Filter: </strong>
                    </div>
                    <div class="d-flex flex-md-row flex-column align-items-md-center">
                        <div class="mr-2 mb-2 mb-md-0">
                            <strong>Start Date: </strong>
                        </div>
                        <div class="mr-2 flex-fill mb-2 mb-md-0">
                            <input type="date" class="form-control" name="filter_start_date" id="filter_start_date" placeholder="Cut-Off Date">
                        </div>
                        <div class="mr-2 mb-2 mb-md-0">
                            <strong>End Date: </strong>
                        </div>
                        <div class="mr-2 flex-fill mb-2 mb-md-0">
                            <input type="date" class="form-control" name="filter_end_date" id="filter_end_date" placeholder="Cut-Off Date">
                        </div>
                        <div class="align-items-center justify-content-center d-flex d-md-block">
                            <button class="btn btn-outline-primary mr-1 mr-md-0" title="Filter" onclick="filterData()">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="btn btn-outline-secondary" title="Reset" onclick="filterData(true)">
                                <i class="fas fa-ban"></i>
                            </button>
                        </div>
                    </div>
				</div>
                <div class="card-body">
                    <table id="payslip_table" class="table table-hover table-striped nowrap">
                        <thead class="nowrap">
                            <tr>
                                <th></th>
                                <th>Actions</th>
                                <th>Payroll #</th>
                                <th>Employee ID</th>
                                <th>Employee Name</th>
                                <th>Position</th>
                                <th>Cut-Off Period</th>
                                <th>Cut-Off Pay</th>
                                <th>Gross Pay</th>
                                <th>Net Pay</th>
                                <th>Salary Type</th>
                                <th>Working Days</th>
                                <th>Notes</th>
                                <th>Processed By</th>
                                <th>Processed At</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->include('templates/loading'); ?>
<?=$this->endSection();?>
