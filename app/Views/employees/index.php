<?= $this->extend('templates/default') ;?>
<?= $this->section('content') ;?>
<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<input type="hidden" id="edit_url" value="<?= url_to('employee.edit'); ?>" disabled>
					<input type="hidden" id="remove_url" value="<?= url_to('employee.delete'); ?>" disabled>
					<table id="employee_table" class="table table-hover table-striped nowrap" data-url="<?= url_to('employee.list'); ?>">
						<thead class="nowrap">
							<tr>
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
								<th>Language</th>
								<th>Contact Number</th>
								<th>Email Address</th>
								<th>SSS Number</th>
								<th>TIN Number</th>
								<th>PhilHealth Number</th>
								<th>PAGIBIG Number</th>
								<th>Educational Attainment</th>
								<th>Course</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="employee_modal">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<form id="employee_form" class="with-label-indicator" action="<?= url_to('employee.save'); ?>" method="post" autocomplete="off">
                <?= csrf_field(); ?>
                <input type="hidden" id="id" name="id" readonly>
                <div class="modal-header">
                    <h5 class="modal-title">Add New Employee</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
					<div class="row">
						<div class="col-sm-12 col-md-6">
							<!-- Employee Details -->
							<div class="card card-primary">
								<div class="card-header text-center"><b>Employee Details</b></div>
								<div class="card-body">
									<div class="form-group">
										<label class="required" for="employee_id">Employee ID</label>
										<input type="text" class="form-control" name="employee_id" id="employee_id" placeholder="Enter here...">
										<input type="hidden" class="form-control" name="prev_employee_id" id="prev_employee_id" readonly>
										<small id="alert_employee_id" class="form-text text-danger"></small>
									</div>
									<div class="form-group">
										<label class="required" for="firstname">First Name</label>
										<input type="text" class="form-control" name="firstname" id="firstname" placeholder="Enter here...">
										<small id="alert_firstname" class="form-text text-danger"></small>
									</div>
									<div class="form-group">
										<label for="middlename">Middle Name</label>
										<input type="text" class="form-control" name="middlename" id="middlename" placeholder="Enter here...">
										<small id="alert_middlename" class="form-text text-danger"></small>
									</div>
									<div class="form-group">
										<label class="required" for="lastname">Last Name</label>
										<input type="text" class="form-control" name="lastname" id="lastname" placeholder="Enter here...">
										<small id="alert_lastname" class="form-text text-danger"></small>
									</div>
									<div class="form-group">
										<label class="required" for="gender">Gender</label>
										<select class="form-control" name="gender" id="gender">
											<option value="">---Please Select</option>
											<option value="Male">Male</option>
											<option value="Female">Female</option>
										</select>
										<small id="alert_gender" class="form-text text-danger"></small>
									</div>
									<div class="form-group">
										<label class="required" for="civil_status">Civil Status</label>
										<select class="form-control" name="civil_status" id="civil_status">
											<option value="">---Please select---</option>
											<option value="Single">Single</option>
											<option value="Married">Married</option>
											<option value="Widowed">Widowed</option>
											<option value="Separated">Separated</option>
										</select>
										<small id="alert_civil_status" class="form-text text-danger"></small>
									</div>
									<div class="form-group">
										<label class="required" for="date_of_birth">Date of Birth</label>
										<input type="date" class="form-control" name="date_of_birth" id="date_of_birth">
										<small id="alert_date_of_birth" class="form-text text-danger"></small>
									</div>
									<div class="form-group">
										<label class="required" for="place_of_birth">Place of Birth</label>
										<input type="text" class="form-control" name="place_of_birth" id="place_of_birth" placeholder="Enter here...">
										<small id="alert_place_of_birth" class="form-text text-danger"></small>
									</div>
									<div class="form-group">
										<label class="required" for="postal_code">Postal Code</label>
										<input type="text" class="form-control" name="postal_code" id="postal_code" placeholder="Enter here...">
										<small id="alert_postal_code" class="form-text text-danger"></small>
									</div>
									<div class="form-group">
										<label class="required" for="language">Language</label>
										<input type="text" class="form-control" name="language" id="language" placeholder="Enter here...">
										<small id="alert_language" class="form-text text-danger"></small>
									</div>
									<div class="form-group">
										<label class="required" for="address_province">Province</label>
										<input type="text" class="form-control" name="address_province" id="address_province" placeholder="Enter here...">
										<small id="alert_address_province" class="form-text text-danger"></small>
									</div>
									<div class="form-group">
										<label class="required" for="address_city">City/Municipality</label>
										<input type="text" class="form-control" name="address_city" id="address_city" placeholder="Enter here...">
										<small id="alert_address_city" class="form-text text-danger"></small>
									</div>
									<div class="form-group">
										<label class="required" for="address_brgy">Barangay</label>
										<input type="text" class="form-control" name="address_brgy" id="address_brgy" placeholder="Enter here...">
										<small id="alert_address_brgy" class="form-text text-danger"></small>
									</div>
									<div class="form-group">
										<label class="required" for="address_sub">Additional Address</label>
										<input type="text" class="form-control" name="address_sub" id="address_sub" placeholder="Enter here...">
										<small id="alert_address_sub" class="form-text text-danger"></small>
									</div>
									<div class="form-group">
										<label class="required" for="contact_number">Contact Number</label>
										<input type="text" class="form-control" name="contact_number" id="contact_number" placeholder="Enter here...">
										<small id="alert_contact_number" class="form-text text-danger"></small>
									</div>
									<div class="form-group">
										<label class="required" for="email_address">Email Address</label>
										<input type="text" class="form-control" name="email_address" id="email_address" placeholder="Enter here...">
										<small id="alert_email_address" class="form-text text-danger"></small>
									</div>
								</div>
							</div>
							<!-- Educational Background -->
							<div class="card card-primary">
								<div class="card-header text-center"><b>Educational Background</b></div>
								<div class="card-body">
									<div class="form-group">
										<label class="required" for="educational_attainment">Education Attainment</label>
										<input type="text" class="form-control" name="educational_attainment" id="educational_attainment" placeholder="Enter here...">
										<small id="alert_educational_attainment" class="form-text text-danger"></small>
									</div>
									<div class="form-group">
										<label for="course">Course (if college graduate)</label>
										<input type="text" class="form-control" name="course" id="course" placeholder="Enter here...">
										<small id="alert_course" class="form-text text-danger"></small>
									</div>
								</div>
							</div>
						</div>
						<div class="col-sm-12 col-md-6">
							<!-- Employee Benefits -->
							<div class="card card-primary">
								<div class="card-header text-center"><b>Employee Benefits</b></div>
								<div class="card-body">
									<div class="form-group">
										<label class="required" for="sss_no">SSS Number</label>
										<input type="text" class="form-control" name="sss_no" id="sss_no" placeholder="Enter here...">
										<small id="alert_sss_no" class="form-text text-danger"></small>
									</div>
									<div class="form-group">
										<label class="required" for="philhealth_no">PhilHealth Number</label>
										<input type="text" class="form-control" name="philhealth_no" id="philhealth_no" placeholder="Enter here...">
										<small id="alert_philhealth_no" class="form-text text-danger"></small>
									</div>
									<div class="form-group">
										<label class="required" for="pag_ibig_no">PAG-IBIG Number</label>
										<input type="text" class="form-control" name="pag_ibig_no" id="pag_ibig_no" placeholder="Enter here...">
										<small id="alert_pag_ibig_no" class="form-text text-danger"></small>
									</div>
									<div class="form-group">
										<label class="required" for="tin_no">TIN Number</label>
										<input type="text" class="form-control" name="tin_no" id="tin_no" placeholder="Enter here...">
										<small id="alert_tin_no" class="form-text text-danger"></small>
									</div>
								</div>
							</div>
							<!-- Employment Details -->
							<div class="card card-primary">
								<div class="card-header text-center"><b>Employment Details</b></div>
								<div class="card-body">
									<div class="form-group">
										<label class="required" for="position">Position</label>
										<input type="text" class="form-control" name="position" id="position" placeholder="Enter here...">
										<small id="alert_position" class="form-text text-danger"></small>
									</div>
									<div class="form-group">
										<label class="required" for="employment_status">Employment Status</label>
										<select type="text" class="form-control" name="employment_status" id="employment_status">
											<option value="">---Please Select---</option>
											<option value="Probation">Probation</option>
											<option value="Regular">Regular</option>
											<option value="Contractual">Contractual</option>
											<option value="Temporary">Temporary</option>
											<option value="Project-based">Project-Based</option>
										</select>
										<small id="alert_employment_status" class="form-text text-danger"></small>
									</div>
									<div class="form-group">
										<label class="required" for="date_hired">Date Hired</label>
										<input type="date" class="form-control" name="date_hired" id="date_hired">
										<small id="alert_date_hired" class="form-text text-danger"></small>
									</div>
									<div class="form-group">
										<label for="date_resigned">Date Resigned</label>
										<input type="date" class="form-control" name="date_resigned" id="date_resigned">
										<small id="alert_date_resigned" class="form-text text-danger"></small>
									</div>
								</div>
							</div>
							<!-- In case of emergency -->
							<div class="card card-primary">
								<div class="card-header text-center"><b>In Case of Emergency</b></div>
								<div class="card-body">
									<div class="form-group">
										<label class="required" for="emergency_name">Name</label>
										<input type="text" class="form-control" name="emergency_name" id="emergency_name" placeholder="Enter here...">
										<small id="alert_emergency_name" class="form-text text-danger"></small>
									</div>
									<div class="form-group">
										<label class="required" for="emergency_contact_no">Contact Number</label>
										<input type="text" class="form-control" name="emergency_contact_no" id="emergency_contact_no" placeholder="Enter here...">
										<small id="alert_emergency_contact_no" class="form-text text-danger"></small>
									</div>
									<div class="form-group">
										<label class="required" for="emergency_address">Address</label>
										<input type="text" class="form-control" name="emergency_address" id="emergency_address" placeholder="Enter here...">
										<small id="alert_emergency_address" class="form-text text-danger"></small>
									</div>
								</div>
							</div>
							<div class="card card-secondary">
								<div class="card-header text-center"><b>If Married</b></div>
								<div class="card-body">
									<div class="form-group">
										<label for="name_of_spouse">Spouse Name</label>
										<input type="text" class="form-control" name="name_of_spouse" id="name_of_spouse" placeholder="Enter here...">
										<small id="alert_name_of_spouse" class="form-text text-danger"></small>
									</div>
									<div class="form-group">
										<label for="spouse_contact_no">Contact Number</label>
										<input type="text" class="form-control" name="spouse_contact_no" id="spouse_contact_no" placeholder="Enter here...">
										<small id="alert_spouse_contact_no" class="form-text text-danger"></small>
									</div>
									<div class="form-group">
										<label for="no_of_children">Number of Children</label>
										<input type="text" class="form-control" name="no_of_children" id="no_of_children" placeholder="Enter here...">
										<small id="alert_no_of_children" class="form-text text-danger"></small>
									</div>
									<div class="form-group">
										<label for="spouse_address">Address of Spouse</label>
										<input type="text" class="form-control" name="spouse_address" id="spouse_address" placeholder="Enter here...">
										<small id="alert_spouse_address" class="form-text text-danger"></small>
									</div>
								</div>
							</div>
						</div>
					</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Save Changes</button>
                </div>
            </form>
		</div>
		<!-- /.modal-content -->
	</div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<?= $this->include('templates/loading'); ?>
<?= $this->endSection() ;?>
