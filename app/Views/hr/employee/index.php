<?= $this->extend('templates/default') ;?>
<?= $this->section('content') ;?>
<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<div class="card">
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
