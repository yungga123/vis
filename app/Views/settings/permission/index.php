<?=$this->extend('templates/default');?>
<?=$this->section('content');?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">                    
            <div class="card">
                <div class="card-body">                    
                    <input type="hidden" id="edit_url" value="<?= url_to('permission.edit'); ?>" disabled>
                    <input type="hidden" id="remove_url" value="<?= url_to('permission.delete'); ?>" disabled>
                    <table id="permission_table" class="table table-hover table-striped nowrap" data-url="<?= url_to('permission.list'); ?>">
                        <thead class="nowrap">
                            <tr>
                                <th>Role</th>
                                <th>Module</th>
                                <th>Permissions</th>
                                <th width="10%">Action</th>
                                <!-- <th>Added By</th>
                                <th>Updated By</th>
                                <th>Created At</th>
                                <th>Updated At</th> -->
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="permission_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="permission_form" class="with-label-indicator" action="<?= url_to('permission.save'); ?>" method="post" autocomplete="off">
                <?= csrf_field(); ?>
                <input type="hidden" id="permission_id" name="id" readonly>
                <div class="modal-header">
                    <h5 class="modal-title">Add New Item</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="required" for="role_code">Role</label>
                        <select class="form-control" name="role_code" id="role_code" style="width: 100%;" required>
                        <option value="">Select Role</option>
                        
                            <!-- <option value="</?= $key ?>"></?= $val ?></option> -->
                            <optgroup label="Managerial Level">
                                <?php foreach (get_roles() as $key => $val): ?>
                                    <?php if (str_contains($key,'MANAGER')) : ?>
                                        <option value="<?= $key ?>"><?= $val ?></option>
                                    <?php endif ?>
                                <?php endforeach; ?>
                            </optgroup>
                            <optgroup label="Supervisory Level">
                                <?php foreach (get_roles() as $key => $val): ?>
                                    <?php if (str_contains($key,'SUPERVISOR')) : ?>
                                        <option value="<?= $key ?>"><?= $val ?></option>
                                    <?php endif ?>
                                <?php endforeach; ?>
                            </optgroup>

                            <optgroup label="Others">
                                <?php foreach (get_roles() as $key => $val): ?>
                                    <?php if (!str_contains($key,'SUPERVISOR') && !str_contains($key,'MANAGER')) : ?>
                                        <option value="<?= $key ?>"><?= $val ?></option>
                                    <?php endif ?>
                                <?php endforeach; ?>
                            </optgroup>
                        
                        </select>
                        <small id="alert_role_code" class="text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label class="required" for="module_code">Module</label>
                        <select class="form-control" name="module_code" id="module_code" style="width: 100%;" required>
                            <option value="">Select Module</option>
                            <?php $modules = get_modules(); unset($modules['DASHBOARD']);
                            foreach ($modules as $key => $val): ?>
                                <option value="<?= $key ?>"><?= $val ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small id="alert_module_code" class="text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label class="required" for="permissions">Permissions</label>
                        <select class="select2" name="permissions[]" id="permissions" multiple="multiple" data-placeholder="Select Permission" style="width: 100%;" required>
                        <?php foreach (get_actions() as $key => $val): ?>
                            <option value="<?= $key ?>"><?= $val ?></option>
                        <?php endforeach; ?>
                        </select>
                        <small id="alert_permissions" class="text-danger"></small>
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
