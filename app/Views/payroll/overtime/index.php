<?=$this->extend('templates/default');?>
<?=$this->section('content');?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">                    
            <div class="card">
				<div class="card-header">
                    <div class="mr-2 mb-2">
                        <strong>Filters by Status and Overtime Date (Start and End): </strong>
                    </div>
                    <div class="d-flex flex-md-row flex-column align-items-md-center">
                        <div class="mr-2 flex-fill mb-2 mb-md-0">
                            <select class="custom-select select2" id="filter_status" data-placeholder="Select a status" multiple style="width: 100%;">
                                <?php foreach (get_leave_status('', true) as $val => $text): ?>
                                    <option value="<?= $val ?>"><?= ucfirst($text) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mr-2 flex-fill mb-2 mb-md-0">
                            <input type="date" class="form-control" name="filter_start_date" id="filter_start_date" placeholder="Start Date">
                        </div>
                        <div class="mr-2 flex-fill mb-2 mb-md-0">
                            <input type="date" class="form-control" name="filter_end_date" id="filter_end_date" placeholder="End Date">
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
                    <table id="overtime_table" class="table table-hover table-striped nowrap">
                        <thead class="nowrap">
                            <tr>
                                <th></th>
                                <th>Actions</th>
                                <th>Status</th>
                                <th>Employee ID</th>
                                <th>Employee Name</th>
                                <th>Date</th>
                                <th>Time Start</th>
                                <th>Time End</th>
                                <th>Total Hours</th>
                                <th>Reason</th>
                                <th>Remark</th>
                                <th>Filed At</th>
                                <th>Processed By</th>
                                <th>Processed At</th>
                                <th>Approved By</th>
                                <th>Approved At</th>
                                <th>Discarded By</th>
                                <th>Discarded At</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->include('payroll/overtime/form'); ?>
<?= $this->include('payroll/overtime/change'); ?>
<?= $this->include('templates/loading'); ?>
<?=$this->endSection();?>
