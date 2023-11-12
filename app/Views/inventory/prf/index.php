<?=$this->extend('templates/default');?>
<?=$this->section('content');?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="input-group" style="flex-wrap: nowrap; width: 100%;">
                        <div class="input-group-prepend">
                            <label class="input-group-text">Filter by Status</label>
                        </div>
                        <select class="custom-select select2" id="filter_status" data-placeholder="Select a status" multiple>
                            <?php foreach (get_prf_status('', true) as $val => $text): ?>
                                <option value="<?= $val ?>"><?= ucwords(str_replace('_', ' ', $text)) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="input-group-append">
                            <button class="btn btn-outline-primary px-3" onclick="filterData()" type="button" title="Search filter">Filter</button>
                            <button class="btn btn-outline-secondary px-3 rounded-right" onclick="filterData(true)" type="button" title="Reset filter">Reset</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table id="prf_table" class="table table-hover table-striped nowrap" width="100%">
                        <thead class="nowrap">
                            <tr> 
                                <th></th>
                                <th>Actions</th>
                                <th>Items</th>
                                <th>Status</th>
                                <th>PRF #</th>
                                <th>JO #</th>
                                <th>Quotation</th>
                                <th>Quotation Type</th>
                                <th>Client</th>
                                <th>Work Type</th>
                                <th>Date Requested</th>
                                <th>Date Committed</th>
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
                <div class="card-footer">
                    <a href="<?= url_to('prf.export'); ?>" class="btn btn-success">Export All PRFs</a>
                    <a href="<?= url_to('prf.export_items'); ?>" class="btn btn-success">Export All PRF Items</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->include('inventory/prf/form'); ?>
<?= $this->include('inventory/prf/items'); ?>
<?= $this->include('templates/loading'); ?>
<?=$this->endSection();?>