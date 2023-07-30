<?=$this->extend('templates/default');?>
<?=$this->section('content');?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">                    
            <div class="card">
                <div class="card-body">
                    <table id="dispatch_table" class="table table-hover table-striped nowrap">
                        <thead class="nowrap">
                            <tr>
                                <th>Actions</th>
                                <th>Dispatch ID</th>
                                <th>Schedule ID</th>
                                <th>Schedule Title</th>
                                <th>Customer</th>
                                <th>Dispatch Date</th>
                                <th>Dispatch Out</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                                <th>SR Number</th>
                                <th>Technicians</th>
                                <th>Service Type</th>
                                <th>With Permit</th>
                                <th>Comments</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->include('admin/dispatch/form'); ?>
<?= $this->include('templates/loading'); ?>
<?=$this->endSection();?>
