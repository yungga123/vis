<?=$this->extend('templates/default');?>
<?=$this->section('content');?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">                    
            <div class="card">
				<div class="card-header">
                    <div class="input-group" style="flex-wrap: nowrap; width: 100%;">
                        <div class="input-group-prepend">
                            <label class="input-group-text">Filter By:</label>
                            <span class="input-group-text">Status</span>
                        </div>
                        <select class="custom-select select2" id="filter_status" data-placeholder="Select a status" multiple>
                            <?php foreach (get_leave_status('', true) as $val => $text): ?>
                                <option value="<?= $val ?>"><?= ucfirst($text) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="input-group-append">
                            <button class="btn btn-outline-primary px-3" onclick="filterData()" type="button" title="Search filter">Filter</button>
                            <button class="btn btn-outline-secondary px-3 rounded-right" onclick="filterData(true)" type="button" title="Reset filter">Reset</button>
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
