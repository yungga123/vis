<?= $this->extend('templates/default') ;?>
<?= $this->section('content') ;?>
<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-header">
                    <div class="input-group" style="flex-wrap: nowrap; width: 100%;">
                        <div class="input-group-prepend">
                            <label class="input-group-text">Filter By:</label>
                            <span class="input-group-text">Employment Status</span>
                        </div>
                        <select class="custom-select select2" id="filter_status" data-placeholder="Select an employment status" multiple>
                            <?php foreach (get_employment_status() as $val => $text): ?>
                                <option value="<?= $val ?>"><?= $text ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="input-group-append">
                            <button class="btn btn-outline-primary px-3" onclick="filterData()" type="button" title="Search filter">Filter</button>
                            <button class="btn btn-outline-secondary px-3 rounded-right" onclick="filterData(true)" type="button" title="Reset filter">Reset</button>
                        </div>
                    </div>
				</div>
				<div class="card-body">
					<table id="employee_table" class="table table-hover table-striped nowrap" width="100%">
						<thead class="nowrap">
							<tr>
								<th></th>
								<th>Action</th>
								<th>Employee ID</th>
								<th>Employee Name</th>
								<th>Address</th>
								<th>Gender</th>
								<th>Civil Status</th>
								<th>Birthdate</th>
								<th>Birthplace</th>
								<th>Position</th>
								<th>Employment Status</th>
								<th>Date Hired</th>
								<th>Date Resigned</th>
								<th>Contact Number</th>
								<th>Email Address</th>
								<th>SSS Number</th>
								<th>TIN Number</th>
								<th>PhilHealth Number</th>
								<th>PAGIBIG Number</th>
								<th>Educational Attainment</th>
								<th>Course</th>
								<th>Emergency Name</th>
								<th>Emergency Contact Number</th>
								<th>Emergency Address</th>
								<th>Spouse Name</th>
								<th>Spouse Contact Number</th>
								<th>No. of Children</th>
								<th>Spouse Address</th>
								<th>Created By</th>
								<th>Created At</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<?= $this->include('hr/employee/form'); ?>
<?= $this->include('hr/employee/change'); ?>
<?= $this->include('templates/loading'); ?>
<?= $this->endSection() ;?>
