<?=$this->extend('templates/default');?>
<?=$this->section('content');?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">                    
            <div class="card">
                <div class="card-body">                    
                    <input type="hidden" id="edit_url" value="<?= url_to('roles.edit'); ?>" disabled>
                    <input type="hidden" id="remove_url" value="<?= url_to('roles.delete'); ?>" disabled>
                    <table id="roles_table" class="table table-hover table-striped nowrap" data-url="<?= url_to('roles.list'); ?>">
                        <thead class="nowrap">
                            <tr>
                                <th>Role Code</th>
                                <th>Description</th>
                                <th width="10%">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="roles_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="roles_form" class="with-label-indicator" action="<?= url_to('roles.save'); ?>" method="post" autocomplete="off">
                <?= csrf_field(); ?>
                <input type="hidden" id="role_id" name="id" readonly>
                <div class="modal-header">
                    <h5 class="modal-title">Add Role</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="required" for="role_code">Role Code</label>
                        <input type="text" name="role_code" id="role_code" class="form-control text-uppercase" placeholder="Enter role code">
                        <input type="hidden" name="prev_role_code" id="prev_role_code" readonly>
                        <small id="alert_role_code" class="text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label class="required" for="description">Description</label>
                        <input type="text" name="description" id="description" class="form-control text-capitalize" placeholder="Enter description">
                        <small id="alert_description" class="text-danger"></small>
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
