<?=$this->extend('templates/default');?>
<?=$this->section('content');?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">            
            <div class="card">
                <div class="card-body">
                    <input type="hidden" id="edit_url" value="<?= url_to('account.edit'); ?>" disabled>
                    <input type="hidden" id="remove_url" value="<?= url_to('account.delete'); ?>" disabled>
                    <table id="account_table" class="table table-striped table-hover nowrap" data-url="<?= url_to('account.list'); ?>">
                        <thead class="nowrap">
                            <tr>
                                <th>Employee ID</th>
                                <th>Employee Name</th>
                                <th>Username</th>
                                <th>Access Level</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="account_modal" tabindex="-1" aria-hidden="true">
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
                        <label class="required" for="employee_id">Employee Name</label>
                        <select name="employee_id" id="employee_id" class="form-control" data-value="<?= $account_data['employee_id'] ?? ''; ?>">
                            <option value="">---Please Select---</option>
                            <?php foreach ($employees as $item) : ?>
                                <option value="<?= $item['employee_id']; ?>"><?= $item['employee_id'] . ' - ' . $item['firstname'] . ' ' . $item['lastname'] ?></option>
                            <?php endforeach ?>
                        </select>
                        <input type="hidden" id="employee_id1" name="employee_id1" readonly>
                        <small id="alert_employee_id" class="text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label class="required"  for="username">Username</label>
                        <input type="text" name="username" id="username" class="form-control" placeholder="Enter here..." value="<?= $account_data['username'] ?? ''; ?>">
                        <input type="hidden" name="prev_username" id="prev_username" class="form-control" value="<?= $account_data['username'] ?? ''; ?>" readonly>
                        <small id="alert_username" class="text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label class="required lbl_password"  for="password">Password</label>
                        <small id="small_password" class="text-muted" style="display: none;">Leave it blank if you will not update the password.</small>
                        <input type="text" name="password" id="password" class="form-control" placeholder="Enter here...">
                        <small id="alert_password" class="text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label class="required"  for="access_level">Access Level</label>
                        <select name="access_level" id="access_level" class="form-control" data-value="<?= $account_data['access_level'] ?? ''; ?>">
                            <option value="">---Please Select---</option>
                            <?php foreach ($access_level as $key => $val): ?>
                                <option value="<?= $key; ?>"><?= $val; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small id="alert_access_level" class="text-danger"></small>
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
<?= $this->include('templates/loading'); ?>
<?=$this->endSection();?>
