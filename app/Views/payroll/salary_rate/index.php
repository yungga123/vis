<?=$this->extend('templates/default');?>
<?=$this->section('content');?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">                    
            <div class="card">
                <div class="card-body">
                    <table id="salary_rate_table" class="table table-hover table-striped nowrap">
                        <thead class="nowrap">
                            <tr>
                                <th></th>
                                <th>Actions</th>
                                <th>Employee ID</th>
                                <th>Employee Name</th>
                                <th>Position</th>
                                <th>Employee Status</th>
                                <th>Rate Type</th>
                                <th>Salary Rate</th>
                                <th>Set By</th>
                                <th>Set At</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->include('payroll/salary_rate/form'); ?>
<?= $this->include('templates/loading'); ?>
<?=$this->endSection();?>
