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
                                <th>Status</th>
                                <th>PRF #</th>
                                <th>Item #</th>
                                <th>Category</th>
                                <th>Sub-Category</th>
                                <th>Item Brand</th>
                                <th>Item Model</th>
                                <th>Item Description</th>
                                <th>Current Stock</th>
                                <th>Quantity Out</th>
                                <th>Process Date</th>
                                <th>Created By</th>
                                <th>Created At</th>
                                <th>Accepted By</th>
                                <th>Accepted At</th>
                                <th>Rejected By</th>
                                <th>Rejected At</th>
                                <th>Item Out By</th>
                                <th>Item Out At</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->include('inventory/prf/form'); ?>
<?= $this->include('templates/loading'); ?>
<?=$this->endSection();?>