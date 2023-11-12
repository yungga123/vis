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
                                <th></th>
                                <th>Actions</th>
                                <th>Dispatch ID</th>
                                <th>Schedule ID</th>
                                <th>Schedule Title</th>
                                <th>Client</th>
                                <th>Client Type</th>
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
                                <th>Checked By</th>
                                <th>Dispatched By</th>
                                <th>Dispatched At</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="card-footer">
                    <a href="<?= url_to('dispatch.export'); ?>" class="btn btn-success">Export All Dispatch</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->include('admin/dispatch/form'); ?>
<?= $this->include('templates/loading'); ?>
<?=$this->endSection();?>
