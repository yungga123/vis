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
                        src="<?= base_url($user['avatar']); ?>"
                        alt="User profile picture">
                    </div>
                    <h3 class="profile-username text-center"><?= esc(session()->get('name')); ?></h3>
                    <p class="text-muted text-center"><?= esc(ucwords($user['position'])); ?></p>
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            Gender <a class="float-right"><?= esc(ucwords($user['gender'])); ?></a>
                        </li>
                        <li class="list-group-item">
                            Civil Status <a class="float-right"><?= esc(ucwords($user['civil_status'])); ?></a>
                        </li>
                        <li class="list-group-item">
                            Birthday <a class="float-right"><?= esc(ucwords($user['date_of_birth'])); ?></a>
                        </li>
                    </ul>
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
                    <p class="text-muted"><?= esc($user['email_address']); ?></p>
                    <hr>
                    <strong><i class="fas fa-address-book mr-1"></i> Contact Number</strong>
                    <p class="text-muted"><?= esc($user['contact_number']); ?></p>
                    <hr>
                    <strong><i class="fas fa-map-marker-alt mr-1"></i> Address</strong>
                    <p class="text-muted"><?= esc($user['address']); ?></p>
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
                            <form id="form_user" class="form-horizontal" method="POST" autocomplete="off">
                                <?= csrf_field(); ?>
                                <div class="form-group row">
                                    <label for="employee_id" class="col-sm-2 col-form-label">Employee ID</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="employee_id" id="employee_id" placeholder="Employee ID" value="<?= esc(session()->get('employee_id')); ?>" disabled>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="access_level" class="col-sm-2 col-form-label">Access Level</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="access_level" id="access_level" placeholder="Access Level" value="<?= esc(user_access_level(session()->get('access_level'))); ?>" disabled>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="username" class="col-sm-2 col-form-label">Username</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" name="username" id="username" placeholder="Username" value="<?= esc(session()->get('username')); ?>" disabled>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="current_password" class="col-sm-2 col-form-label">Current Password</label>
                                    <div class="col-sm-10">
                                        <input type="password" class="form-control" name="current_password" id="current_password" placeholder="Current Password" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="new_password" class="col-sm-2 col-form-label">New Password</label>
                                    <div class="col-sm-10">
                                        <input type="password" class="form-control" name="password" id="password" placeholder="New Password" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="confirm_password" class="col-sm-2 col-form-label">Confirm New Password</label>
                                    <div class="col-sm-10">
                                        <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm New Password" required>
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
<?= $this->endSection(); ?>
<?= $this->section('CustomScript'); ?>
    <?= $this->include('accounts/user/script'); ?>
<?= $this->endSection(); ?>