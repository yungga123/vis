<?= $this->extend('templates/default') ;?>
<?= $this->section('content') ;?>
<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-header">
                    <div class="mr-2 mb-2">
                        <strong>Filters by Employment Status, Gender or Date Hired (Start/End): </strong>
                    </div>
                    <div class="d-flex flex-md-row flex-column align-items-md-center">
                        <div class="mr-2 flex-fill mb-2 mb-md-0">
							<select class="custom-select select2" id="filter_status" data-placeholder="Select an employment status" multiple style="width: 100%;">
								<?php foreach (get_employment_status() as $val => $text): ?>
									<option value="<?= $val ?>"><?= $text ?></option>
								<?php endforeach; ?>
							</select>
                        </div>
						<div class="mr-2 flex-fill mb-2 mb-md-0">
							<select class="custom-select select2" id="filter_gender" data-placeholder="Select a gender" style="width: 100%;">
								<option value=""></option>
								<option value="Male">Male</option>
								<option value="Female">Female</option>
							</select>  
						</div>
                        <div class="mr-2 flex-fill mb-2 mb-md-0">
                            <input type="date" class="form-control" name="filter_start_date" id="filter_start_date" placeholder="Start Date">
                        </div>
                        <div class="mr-2 flex-fill mb-2 mb-md-0">
                            <input type="date" class="form-control" name="filter_end_date" id="filter_end_date" placeholder="End Date">
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
