<?=$this->extend('templates/default');?>
<?=$this->section('content');?>
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <input type="hidden" id="remove_url" value="<?=site_url('delete-account');?>" disabled>
            <table id="accounts_table" class="table table-bordered table-striped nowrap" data-url="<?=site_url('ajax-account')?>">
                <thead>
                    <tr>
                        <th>Employee ID</th>
                        <th>Action</th>
                        <th>Employee Name</th>
                        <th>Username</th>
                        <!-- <th>Password</th> -->
                        <th>Access Level</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Employee ID</th>
                        <th>Action</th>
                        <th>Employee Name</th>
                        <th>Username</th>
                        <!-- <th>Password</th> -->
                        <th>Access Level</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="card-footer">
            <a href="<?=site_url('employee-menu');?>" class="btn btn-secondary float-right"><i class="fas fa-undo"></i> RETURN TO MENU</a>
        </div>
    </div>
</div>
<?=$this->endSection();?>
