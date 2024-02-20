<?=$this->extend('templates/default');?>
<?=$this->section('content');?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="mr-2 mb-2">
                        <strong>Filters By: Category and Dropdowns: </strong>
                    </div>
                    <div class="d-flex flex-md-row flex-column align-items-md-center">
                        <div class="mr-2 flex-fill mb-2 mb-md-0">
                            <select class="custom-select select2" id="filter_category" data-placeholder="Select a category" multiple style="width: 100%;">
                                <?= $categories_filter ?>
                            </select>
                        </div>
                        <div class="mr-2 flex-fill mb-2 mb-md-0">
                            <select class="custom-select select2" id="filter_sub_category" data-placeholder="Select a sub-dropdowns" multiple style="width: 100%;">
                            </select>
                        </div>
                        <div class="align-items-center justify-content-center d-flex">
                            <button class="btn btn-outline-primary mr-1" title="Filter" onclick="filterData()">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="btn btn-outline-secondary" title="Reset" onclick="filterData(true)">
                                <i class="fas fa-ban"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table id="inventory_table" class="table table-hover table-striped nowrap" width="100%">
                        <thead class="nowrap">
                            <tr> 
                                <th></th>
                                <th>Actions</th>
                                <th>Item #</th>
                                <th>Supplier</th>
                                <th>Category</th>
                                <th>Sub-Category</th>
                                <th>Item Brand</th>
                                <th>Item Model</th>
                                <th>Item Description</th>
                                <th>Item Size</th>
                                <th>Item Unit</th>
                                <th>Quantity</th>
                                <th>Dealer's Price</th>
                                <th>Total Price</th>
                                <th>Retail Price</th>
                                <th>Project Price</th>
                                <th>Date Purchase</th>
                                <th>Location</th>
                                <th>Encoder</th>
                                <th>Encoded At</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="<?= url_to('inventory.dropdown.home'); ?>" class="btn btn-success">Inventory Dropdowns</a>
                            <a href="<?= url_to('inventory.logs.home'); ?>" class="btn btn-success">Inventory Logs</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->include('inventory/form'); ?>
<?= $this->include('inventory/logs/form'); ?>
<?= $this->include('templates/loading'); ?>
<?=$this->endSection();?>