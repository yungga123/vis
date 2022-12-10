<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><?= $page_title ?></h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
        <div class="container-fluid">
            <?= form_open('employee-add',["id" => "form-employee-add"]) ?>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <!-- Employee Details -->
                            <div class="card">
                                <div class="card-header text-center"><b>Employee Details</b></div>
                                <div class="card-body">

                                    <div class="form-group">
                                        <label for="employee_id">Employee ID</label>
                                        <input type="text" class="form-control" name="employee_id" id="employee_id" placeholder="Enter here...">
                                        <small id="small_employee_id" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="firstname">First Name</label>
                                        <input type="text" class="form-control" name="firstname" id="firstname" placeholder="Enter here...">
                                        <small id="small_firstname" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="middlename">Middle Name</label>
                                        <input type="text" class="form-control" name="middlename" id="middlename" placeholder="Enter here...">
                                        <small id="small_middlename" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="lastname">Last Name</label>
                                        <input type="text" class="form-control" name="lastname" id="lastname" placeholder="Enter here...">
                                        <small id="small_lastname" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="gender">Gender</label>
                                        <select class="form-control" name="gender" id="gender">
                                            <option value="">---Please Select</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            
                                        </select>
                                        <small id="small_gender" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="civil_status">Civil Status</label>
                                        <select class="form-control" name="civil_status" id="civil_status">
                                            <option value="">---Please select---</option>
                                            <option value="Single">Single</option>
                                            <option value="Married">Married</option>
                                            <option value="Widowed">Widowed</option>
                                        </select>
                                        <small id="small_civil_status" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="date_of_birth">Birthdate</label>
                                        <input type="date" class="form-control" name="date_of_birth" id="date_of_birth">
                                        <small id="small_date_of_birth" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="place_of_birth">Place of Birth</label>
                                        <input type="text" class="form-control" name="place_of_birth" id="place_of_birth" placeholder="Enter here...">
                                        <small id="small_place_of_birth" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="postal_code">Postal Code</label>
                                        <input type="text" class="form-control" name="postal_code" id="postal_code" placeholder="Enter here...">
                                        <small id="small_postal_code" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="language">Language</label>
                                        <input type="text" class="form-control" name="language" id="language" placeholder="Enter here...">
                                        <small id="small_language" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="address_province">Province</label>
                                        <input type="text" class="form-control" name="address_province" id="address_province" placeholder="Enter here...">
                                        <small id="small_address_province" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="address_city">City/Municipality</label>
                                        <input type="text" class="form-control" name="address_city" id="address_city" placeholder="Enter here...">
                                        <small id="small_address_city" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="address_brgy">Barangay</label>
                                        <input type="text" class="form-control" name="address_brgy" id="address_brgy" placeholder="Enter here...">
                                        <small id="small_address_brgy" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="address_sub">Additional Address</label>
                                        <input type="text" class="form-control" name="address_sub" id="address_sub" placeholder="Enter here...">
                                        <small id="small_address_sub" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="contact_number">Contact Number</label>
                                        <input type="text" class="form-control" name="contact_number" id="contact_number" placeholder="Enter here...">
                                        <small id="small_contact_number" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="email_address">Email Address</label>
                                        <input type="text" class="form-control" name="email_address" id="email_address" placeholder="Enter here...">
                                        <small id="small_email_address" class="form-text text-muted"></small>
                                    </div>


                                </div>
                            </div>

                            <!-- Educational Background -->
                            <div class="card">
                                <div class="card-header text-center"><b>Educational Background</b></div>
                                <div class="card-body">

                                    <div class="form-group">
                                        <label for="educational_attainment">Education Attainment</label>
                                        <input type="text" class="form-control" name="educational_attainment" id="educational_attainment" placeholder="Enter here...">
                                        <small id="small_educational_attainment" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="course">Course (if college graduate)</label>
                                        <input type="text" class="form-control" name="course" id="course" placeholder="Enter here...">
                                        <small id="small_course" class="form-text text-muted"></small>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <!-- Employee Benefits -->
                            <div class="card">
                                <div class="card-header text-center"><b>Employee Benefits</b></div>
                                <div class="card-body">

                                    <div class="form-group">
                                        <label for="sss_no">SSS Number</label>
                                        <input type="text" class="form-control" name="sss_no" id="sss_no" placeholder="Enter here...">
                                        <small id="small_sss_no" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="philhealth_no">PhilHealth Number</label>
                                        <input type="text" class="form-control" name="philhealth_no" id="philhealth_no" placeholder="Enter here...">
                                        <small id="small_philhealth_no" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="pag_ibig_no">PAG-IBIG Number</label>
                                        <input type="text" class="form-control" name="pag_ibig_no" id="pag_ibig_no" placeholder="Enter here...">
                                        <small id="small_pag_ibig_no" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="tin_no">TIN Number</label>
                                        <input type="text" class="form-control" name="tin_no" id="tin_no" placeholder="Enter here...">
                                        <small id="small_tin_no" class="form-text text-muted"></small>
                                    </div>

                                </div>
                            </div>

                            <!-- Employment Details -->
                            <div class="card">
                                <div class="card-header text-center"><b>Employment Details</b></div>
                                <div class="card-body">

                                    <div class="form-group">
                                        <label for="position">Position</label>
                                        <input type="text" class="form-control" name="position" id="position" placeholder="Enter here...">
                                        <small id="small_position" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="employment_status">Employment Status</label>
                                        <select type="text" class="form-control" name="employment_status" id="employment_status">
                                            <option value="">---Please Select---</option>
                                            <option value="Probation">Probation</option>
                                            <option value="Regular">Regular</option>
                                            <option value="Contractual">Contractual</option>
                                            <option value="Temporary">Temporary</option>
                                            <option value="Project-based">Project-Based</option>
                                        </select>
                                        <small id="small_employment_status" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="date_hired">Date Hired</label>
                                        <input type="date" class="form-control" name="date_hired" id="date_hired">
                                        <small id="small_date_hired" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="date_resigned">Date Resigned</label>
                                        <input type="date" class="form-control" name="date_resigned" id="date_resigned">
                                        <small id="small_date_resigned" class="form-text text-muted"></small>
                                    </div>
                                </div>
                            </div>
                            <!-- In case of emergency -->
                            <div class="card">
                                <div class="card-header text-center"><b>In Case of Emergency</b></div>
                                <div class="card-body">

                                    <div class="form-group">
                                        <label for="emergency_name">Name</label>
                                        <input type="text" class="form-control" name="emergency_name" id="emergency_name" placeholder="Enter here...">
                                        <small id="small_emergency_name" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="emergency_contact_no">Contact Number</label>
                                        <input type="text" class="form-control" name="emergency_contact_no" id="emergency_contact_no" placeholder="Enter here...">
                                        <small id="small_emergency_contact_no" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="emergency_address">Address</label>
                                        <input type="text" class="form-control" name="emergency_address" id="emergency_address" placeholder="Enter here...">
                                        <small id="small_emergency_address" class="form-text text-muted"></small>
                                    </div>

                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header text-center"><b>If Married</b></div>
                                <div class="card-body">
                                    
                                    <div class="form-group">
                                        <label for="name_of_spouse">Spouse Name</label>
                                        <input type="text" class="form-control" name="name_of_spouse" id="name_of_spouse" placeholder="Enter here...">
                                        <small id="small_name_of_spouse" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="spouse_contact_no">Contact Number</label>
                                        <input type="text" class="form-control" name="spouse_contact_no" id="spouse_contact_no" placeholder="Enter here...">
                                        <small id="small_spouse_contact_no" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="no_of_children">Number of Children</label>
                                        <input type="text" class="form-control" name="no_of_children" id="no_of_children" placeholder="Enter here...">
                                        <small id="small_no_of_children" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="spouse_address">Address of Spouse</label>
                                        <input type="text" class="form-control" name="spouse_address" id="spouse_address" placeholder="Enter here...">
                                        <small id="small_spouse_address" class="form-text text-muted"></small>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-success float-right"><b><i class="fas fa-check"></i> SUBMIT</b></button>
                </div>
            </div>
            <?= form_close() ?>
        </div>
    </section>

</div>