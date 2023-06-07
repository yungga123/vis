<?=$this->extend('templates/default');?>
<?=$this->section('content');?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="input-group" style="flex-wrap: nowrap; width: 100%;">
                        <div class="input-group-prepend">
                            <label class="input-group-text">Filter by</label>
                            <span class="input-group-text">Category</span>
                        </div>
                        <select class="custom-select select2" id="filter_category" data-placeholder="Select a Category" multiple>
                            <?= $categories ?>
                        </select>
                        <div class="input-group-prepend ml-1">
                            <span class="input-group-text">Dropdowns</span>
                        </div>
                        <select class="custom-select select2" id="filter_sub_category" data-placeholder="Select a Sub-Dropdowns" multiple>
                        </select>
                        <div class="input-group-append">
                            <button class="btn btn-outline-primary px-3" onclick="filterData()" type="button" title="Search filter">Filter</button>
                            <button class="btn btn-outline-secondary px-3 rounded-right" onclick="filterData(true)" type="button" title="Reset filter">Reset</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table id="inventory_table" class="table table-hover table-striped nowrap" width="100%">
                        <thead class="nowrap">
                            <tr> 
                                <th>Action</th>
                                <th>Item #</th>
                                <th>Category</th>
                                <th>Sub-Category</th>
                                <th>Item Brand</th>
                                <th>Item Model</th>
                                <th>Item Description</th>
                                <th>Item Size</th>
                                <th>Total</th>
                                <th>Stocks</th>
                                <th>Unit</th>
                                <th>Encoder</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="card-footer">
                    <a href="<?= url_to('inventory.dropdown.home'); ?>" class="btn btn-success">Inventory Dropdowns</a>
                    <a href="<?= url_to('inventory.logs.home'); ?>" class="btn btn-success">Inventory Logs</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->include('inventory/modal'); ?>
<?= $this->include('inventory/logs/modal'); ?>
<?= $this->include('templates/loading'); ?>
<?=$this->endSection();?>