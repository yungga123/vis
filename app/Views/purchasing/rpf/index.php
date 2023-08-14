<?=$this->extend('templates/default');?>
<?=$this->section('content');?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="rpf_table" class="table table-hover table-striped nowrap" width="100%">
                        <thead class="nowrap">
                            <tr> 
                                <th>Actions</th>
                                <th>Items</th>
                                <th>Status</th>
                                <th>RPF #</th>
                                <th>Date Needed</th>
                                <th>Requested By</th>
                                <th>Requested At</th>
                                <th>Accepted By</th>
                                <th>Accepted At</th>
                                <th>Reviewed By</th>
                                <th>Reviewed At</th>
                                <th>Received By</th>
                                <th>Received At</th>
                                <th>Rejected By</th>
                                <th>Rejected At</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->include('purchasing/rpf/form'); ?>
<?= $this->include('purchasing/rpf/items'); ?>
<?= $this->include('templates/loading'); ?>
<?=$this->endSection();?>