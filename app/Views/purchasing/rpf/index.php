<?=$this->extend('templates/default');?>
<?=$this->section('content');?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table id="prf_table" class="table table-hover table-striped nowrap" width="100%">
                        <thead class="nowrap">
                            <tr> 
                                <th>Actions</th>
                                <th>Items</th>
                                <th>Status</th>
                                <th>PRF #</th>
                                <th>JO #</th>
                                <th>Quotation</th>
                                <th>Client</th>
                                <th>Work Type</th>
                                <th>Process Date</th>
                                <th>Remarks</th>
                                <th>Created By</th>
                                <th>Created At</th>
                                <th>Accepted By</th>
                                <th>Accepted At</th>
                                <th>Rejected By</th>
                                <th>Rejected At</th>
                                <th>Item Out By</th>
                                <th>Item Out At</th>
                                <th>Filed By</th>
                                <th>Filed At</th>
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