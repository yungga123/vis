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
                            <?php foreach (get_rpf_status('', true) as $val => $text): ?>
                                <option value="<?= $val ?>"><?= ucfirst($text) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="input-group-append">
                            <button class="btn btn-outline-primary px-3" onclick="filterData()" type="button" title="Search filter">Filter</button>
                            <button class="btn btn-outline-secondary px-3 rounded-right" onclick="filterData(true)" type="button" title="Reset filter">Reset</button>
                        </div>
                    </div>
                </div>
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
                <div class="card-footer">
                    <a href="<?= url_to('rpf.export'); ?>" class="btn btn-success">Export All RPFs</a>
                    <a href="<?= url_to('rpf.export_items'); ?>" class="btn btn-success">Export All RPF Items</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->include('purchasing/rpf/form'); ?>
<?= $this->include('purchasing/rpf/items'); ?>
<?= $this->include('templates/loading'); ?>
<?=$this->endSection();?>