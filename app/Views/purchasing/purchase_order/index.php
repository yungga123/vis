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
                            <?php foreach (get_po_status('', true) as $val => $text): ?>
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
                    <table id="purchase_order_table" class="table table-hover table-striped nowrap" width="100%">
                        <thead class="nowrap">
                            <tr> 
                                <th></th>
                                <th>Actions</th>
                                <th>Items</th>
                                <th>Status</th>
                                <th>PO ID</th>
                                <th>Supplier</th>
                                <th>Attention To</th>
                                <th>Requested By</th>
                                <th>Requested At</th>
                                <th>Generated By</th>
                                <th>Generated At</th>
                                <th>Approved By</th>
                                <th>Approved At</th>
                                <th>Filed By</th>
                                <th>Filed At</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="card-footer">
                    <a href="<?= url_to('purchase_order.export'); ?>" class="btn btn-success">Export All POs</a>
                    <a href="<?= url_to('purchase_order.export_items'); ?>" class="btn btn-success">Export All PO Items</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->include('purchasing/purchase_order/form'); ?>
<?= $this->include('purchasing/purchase_order/items'); ?>
<?= $this->include('templates/loading'); ?>
<?=$this->endSection();?>