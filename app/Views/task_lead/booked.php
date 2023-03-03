<?= $this->extend('templates/default'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <table id="tasklead_booked_table" class="table table-hover table-striped nowrap" data-url="<?= url_to('tasklead.booked.list'); ?>" width="100%">
                <thead class="nowrap">
                    <tr>
                        <th># ID</th>
                        <th>Account Manager</th>
                        <th>Customer</th>
                        <th>Project Progress</th>
                        <th>Details</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
        
    </div>
</div>
<?= $this->include('templates/loading'); ?>
<?= $this->endSection(); ?>