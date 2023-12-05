<?=$this->extend('templates/default');?>
<?=$this->section('content');?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">            
            <div class="card">
                <div class="card-body">
                    <table id="account_table" class="table table-striped table-hover nowrap">
                        <thead class="nowrap">
                            <tr>
                                <th>Actions</th>
                                <th>Employee ID</th>
                                <th>Employee Name</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Created By</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->include('hr/account/form'); ?>
<?= $this->include('templates/loading'); ?>
<?=$this->endSection();?>
