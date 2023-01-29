<?= $this->extend('templates/default'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <table id="accounts_table" class="table table-bordered table-striped nowrap" data-url="<?= site_url('ajax-account') ?>">
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Action</th>
                        <th>Employee Name</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Access Level</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Employee ID</th>
                        <th>Action</th>
                        <th>Employee Name</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Access Level</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="card-footer">
            <a href="<?= site_url('employee-menu'); ?>" class="btn btn-secondary float-right"><i class="fas fa-undo"></i> RETURN TO MENU</a>
        </div>
    </div>
    <div class="modal fade" id="modal-delete-account">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Are you sure you want to continue?</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times"></i> NO</button>
                    <button type="button" class="btn btn-success" id="btn-delete-account" data-url="<?= site_url('delete-account') ?>"><i class="fas fa-check"></i> YES</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <!-- /.modal -->
</div>
<?= $this->endSection(); ?>
