<?=$this->extend('templates/default');?>
<?=$this->section('content');?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">                    
            <div class="card">
                <div class="card-body">
                    <table id="manage_leave_table" class="table table-hover table-striped nowrap">
                        <thead class="nowrap">
                            <tr>
                                <th></th>
                                <th>Actions</th>
                                <th>Status</th>
                                <th>Employee ID</th>
                                <th>Employee Name</th>
                                <th>Leave Type</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Leave Reason</th>
                                <th>Leave Remark</th>
                                <th>Applied At</th>
                                <th>Processed By</th>
                                <th>Processed At</th>
                                <th>Approved By</th>
                                <th>Approved At</th>
                                <th>Discarded By</th>
                                <th>Discarded At</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->include('payroll/manage_leave/form'); ?>
<?= $this->include('payroll/manage_leave/change'); ?>
<?= $this->include('templates/loading'); ?>
<?=$this->endSection();?>
