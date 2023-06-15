<?=$this->extend('templates/default');?>
<?=$this->section('content');?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <label>Filters by (Logs Type, Category and Dropdowns):</label>
                    <div class="row">
                        <div class="col-2 pr-0">
                            <select class="custom-select" name="filter_action" id="filter_action">
                                <option value="all">All</option>
                                <option value="ITEM_IN">Item In</option>
                                <option value="ITEM_OUT">Item Out</option>
                            </select>
                        </div>
                        <div class="col-10 pl-0">
                            <div class="input-group" style="flex-wrap: nowrap; width: 100%;">
                                <select class="custom-select select2 mr-1" id="filter_category_logs" data-placeholder="Select a Category" multiple>
                                    <?= $categories ?>
                                </select>
                                <select class="custom-select select2 round-left-0" id="filter_sub_category_logs" data-placeholder="Select a Sub-Dropdowns" multiple>
                                </select>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-primary px-3" onclick="filterDataLogs()" type="button" title="Search filter">Filter</button>
                                    <button class="btn btn-outline-secondary px-3 rounded-right" onclick="filterDataLogs(true)" type="button" title="Reset filter">Reset</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table id="table_inventory_logs" class="table table-hover table-striped nowrap" width="100%">
                        <thead class="nowrap">
                            <tr> 
                                <!-- <th>Action</th> -->
                                <th>Logs Type</th>
                                <th>Item #</th>
                                <th>Category</th>
                                <th>Sub-Category</th>
                                <th>Item Brand</th>
                                <th>Item Model</th>
                                <th>Item Description</th>
                                <th>Item Size</th>
                                <th>Stocks</th>
                                <th>Unit</th>
                                <th>Encoder</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="card-footer">
                    <a href="<?= url_to('inventory.home'); ?>" class="btn btn-success">Inventory Masterlist</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->include('inventory/logs/modal'); ?>
<?=$this->endSection();?>