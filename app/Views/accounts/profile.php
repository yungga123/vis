<?= $this->extend('templates/default'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <!-- Profile Image -->
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center">
                    <img class="profile-user-img img-fluid img-circle"
                        style="height: 100px;"
                        src="<?= $profile_img; ?>"
                        alt="User profile picture">
                    </div>
                    <h3 class="profile-username text-center"><?= esc(session()->get('name')); ?></h3>
                    <p class="text-muted text-center"><?= esc(ucwords($account['position'])); ?></p>
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            Gender <a class="float-right"><?= esc(ucwords($account['gender'])); ?></a>
                        </li>
                        <li class="list-group-item">
                            Civil Status <a class="float-right"><?= esc(ucwords($account['civil_status'])); ?></a>
                        </li>
                        <li class="list-group-item">
                            Birthday <a class="float-right"><?= esc(ucwords($account['date_of_birth'])); ?></a>
                        </li>
                    </ul>
                    <button class="btn btn-primary btn-block" data-toggle="modal" data-target="#modal_profile_img">
                        <b>Change Profile Image</b>
                    </button>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <!-- About Me Box -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">About Me</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <strong><i class="fas fa-envelope mr-1"></i> Email Address</strong>
                    <p class="text-muted"><?= esc($account['email_address']); ?></p>
                    <hr>
                    <strong><i class="fas fa-address-book mr-1"></i> Contact Number</strong>
                    <p class="text-muted"><?= esc($account['contact_number']); ?></p>
                    <hr>
                    <strong><i class="fas fa-map-marker-alt mr-1"></i> Address</strong>
                    <p class="text-muted"><?= esc($account['address']); ?></p>
                    <hr>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header p-2">
                    <ul class="nav nav-pills">
                        <li class="nav-item"><a class="nav-link active" href="#change_password" data-toggle="tab">Change Password</a></li>
                    </ul>
                </div><!-- /.card-header -->
                <div class="card-body">
                    <div class="tab-content">
                        <div class="active tab-pane" id="change_password">
                            <form id="form_account" class="form-horizontal" action="<?= url_to('account.change_pass'); ?>" method="POST" autocomplete="off">
                                <?= csrf_field(); ?>
                                <div class="form-group row">
                                    <label for="employee_id" class="col-sm-2 col-form-label">Employee ID</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="employee_id" id="employee_id" placeholder="Employee ID" value="<?= esc(session()->get('employee_id')); ?>" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="access_level" class="col-sm-2 col-form-label">Access Level</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="access_level" id="access_level" placeholder="Access Level" value="<?= esc(account_access_level(false, session('access_level'))); ?>" readonly>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="username" class="col-sm-2 col-form-label">Username</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="username" id="username" placeholder="Username" value="<?= esc(session()->get('username')); ?>" readonly>
                                        <small id="alert_username" class="form-text text-danger"></small>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="current_password" class="col-sm-2 col-form-label">Current Password</label>
                                    <div class="col-sm-10">
                                        <input type="password" class="form-control" name="current_password" id="current_password" placeholder="Current Password" required>
                                        <small id="alert_current_password" class="form-text text-danger"></small>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="new_password" class="col-sm-2 col-form-label">New Password</label>
                                    <div class="col-sm-10">
                                        <input type="password" class="form-control" name="password" id="password" placeholder="New Password" required>
                                        <small id="alert_password" class="form-text text-danger"></small>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="confirm_password" class="col-sm-2 col-form-label">Confirm New Password</label>
                                    <div class="col-sm-10">
                                        <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm New Password" required>
                                        <small id="alert_confirm_password" class="form-text text-danger"></small>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="offset-sm-2 col-sm-10">
                                    <button type="submit" class="btn btn-danger">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.tab-pane -->
                    </div>
                    <!-- /.tab-content -->
                </div><!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="modal_profile_img" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="form_profile_img" action="<?= url_to('account.profile.image'); ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field(); ?>                
                <div class="modal-header">
                    <h5 class="modal-title">Change Profile Image</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <label for="profile_img">Image Upload</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" name="profile_img" id="profile_img" required>
                        <label class="custom-file-label" for="profile_img">Choose file...</label>
                        <div><small class="text-danger" id="alert_profile_img"></small></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->include('templates/loading'); ?>
<?= $this->endSection(); ?>