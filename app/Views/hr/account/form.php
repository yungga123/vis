<!-- Modal -->
<div class="modal fade" id="account_modal" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="account_form" class="with-label-indicator" action="<?= url_to('account.save'); ?>" method="post" autocomplete="off">
                <?= csrf_field(); ?>
                <input type="hidden" id="account_id" name="id" readonly>
                <div class="modal-header">
                    <h5 class="modal-title">Add New Account</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="required"  for="access_level">Role/Access Level</label>
                        <select name="access_level" id="access_level" class="form-control">
                            <option value="">Choose an option</option>
                            <?= get_roles_options(); ?>
                        </select>
                        <small id="alert_access_level" class="text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label class="required" for="employee_id">Employee Name</label>
                        <select class="select2" name="employee_id" id="employee_id" data-placeholder="Select an employee" style="width: 100%;" required>
                            <?php $employees = get_employees(); 
                                if (! empty($employees)): ?>
                            <?php foreach ($employees as $val): ?>
                                <option value="<?= $val['employee_id'] ?>"><?= $val['employee_name'] ?></option>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <input type="hidden" id="employee_id1" name="employee_id1" readonly>
                        <small id="alert_employee_id" class="text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label class="required"  for="username">Username</label>
                        <input type="text" name="username" id="username" class="form-control" placeholder="Enter here...">
                        <input type="hidden" name="prev_username" id="prev_username" class="form-control" readonly>
                        <small id="alert_username" class="text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label class="required lbl_password"  for="password">Password</label>
                        <small id="small_password" class="text-muted" style="display: none;">Leave it blank if you will not update the password.</small>
                        <div class="input-group">
                            <input type="password" name="password" id="password" class="form-control" placeholder="Enter here...">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" id="show_password" type="button" title="Click here to show password!"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                        <small id="alert_password" class="text-danger"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>