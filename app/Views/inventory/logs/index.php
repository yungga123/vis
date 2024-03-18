<?=$this->extend('templates/default');?>
<?=$this->section('content');?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="mr-2 mb-2">
                        <strong>Filters by Logs Type, Category or Dropdowns:</strong>
                    </div>
                    <div class="d-flex flex-md-row flex-column align-items-md-center">
                        <div class="mr-2 flex-fill mb-2 mb-md-0">
                            <select class="custom-select select2" name="filter_action" id="filter_action" data-placeholder="Select a logs type" style="width: 100%;">
                                <option value="">All</option>
                                <option value="ITEM_IN">Item In</option>
                                <option value="ITEM_OUT">Item Out</option>
                            </select>
                        </div>
                        <div class="mr-2 flex-fill mb-2 mb-md-0">
                            <select class="custom-select select2" id="filter_category_logs" data-placeholder="Select a category" multiple style="width: 100%;">
                                <?= $categories ?>
                            </select>
                        </div>
                        <div class="mr-2 flex-fill mb-2 mb-md-0">
                            <select class="custom-select select2" id="filter_sub_category_logs" data-placeholder="Select a sub-dropdowns" multiple style="width: 100%;">
                            </select>
                        </div>
                        <div class="align-items-center justify-content-center d-flex">
                            <button class="btn btn-outline-primary mr-1" title="Filter" onclick="filterDataLogs()">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="btn btn-outline-secondary" title="Reset" onclick="filterDataLogs(true)">
                                <i class="fas fa-ban"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table id="table_inventory_logs" class="table table-hover table-striped nowrap" width="100%">
                        <thead class="nowrap">
                            <tr> 
                                <th>Logs Type</th>
                                <th>Item #</th>
                                <th>Supplier</th>
                                <th>Category</th>
                                <th>Sub-Category</th>
                                <th>Item Brand</th>
                                <th>Item Model</th>
                                <th>Item Description</th>
                                <th>Quantity</th>
                                <th>Prev Stocks</th>
                                <th>Current Stocks</th>
                                <th>Item Size</th>
                                <th>Unit</th>
                                <th>Status</th>
                                <th>Status Date</th>
                                <th>Encoder</th>
                                <th>Encoded At</th>
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
<?= $this->include('inventory/logs/form'); ?>
<?=$this->endSection();?>