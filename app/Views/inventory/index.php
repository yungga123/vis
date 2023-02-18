<?=$this->extend('templates/default');?>
<?=$this->section('content');?>
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <input type="hidden" id="edit_url" value="<?= url_to('inventory.edit'); ?>" disabled>
            <input type="hidden" id="remove_url" value="<?= url_to('inventory.delete'); ?>" disabled>
            <table id="inventory_table" class="table table-bordered table-striped nowrap" data-url="<?= url_to('inventory.list'); ?>">
                <thead>
                    <tr> 
                        <th>Action</th>
                        <th>Item Name</th>
                        <th>Brand</th>
                        <th>Item Type</th>
                        <th>Dealer's Price</th>
                        <th>Retail Price</th>
                        <th>Project Price</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Date of Purchase</th>
                        <th>Supplier</th>
                        <th>Location</th>
                        <th>Encoder</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Action</th>
                        <th>Item Name</th>
                        <th>Brand</th>
                        <th>Item Type</th>
                        <th>Dealer's Price</th>
                        <th>Retail Price</th>
                        <th>Project Price</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Date of Purchase</th>
                        <th>Supplier</th>
                        <th>Location</th>
                        <th>Encoder</th>
                        <th>Created At</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="card-footer">
            <!-- <a href="<?=site_url('employee-menu');?>" class="btn btn-secondary float-right"><i class="fas fa-undo"></i> RETURN TO MENU</a> -->
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="modal_inventory" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form_inventory" action="<?= url_to('inventory.save'); ?>" method="post" autocomplete="off">
                <?= csrf_field(); ?>
                <input type="hidden" id="inventory_id" name="id" readonly>
                <div class="modal-header">
                    <h5 class="modal-title">Add New Item</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="item_name">Item name</label>
                                <input type="text" name="item_name" id="item_name" class="form-control" placeholder="Enter...">
                                <small id="alert_item_name" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="item_brand">Brand</label>
                                <input type="text" name="item_brand" id="item_brand" class="form-control" placeholder="Enter...">
                                <small id="alert_item_brand" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="item_type">Item Type</label>
                                <input type="text" name="item_type" id="item_type" class="form-control" placeholder="Enter...">
                                <small id="alert_item_type" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="item_sdp">Dealer's Price</label>
                                <input type="number" name="item_sdp" id="item_sdp" class="form-control" placeholder="Enter...">
                                <small id="alert_item_sdp" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="item_srp">Retail Price</label>
                                <input type="number" name="item_srp" id="item_srp" class="form-control" placeholder="Enter...">
                                <small id="alert_item_srp" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="project_price">Project Price</label>
                                <input type="number" name="project_price" id="project_price" class="form-control" placeholder="Enter...">
                                <small id="alert_project_price" class="text-danger"></small>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="number">Quantity</label>
                                <input type="text" name="stocks" id="stocks" class="form-control" placeholder="Enter...">
                                <small id="alert_stocks" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="stock_unit">Unit</label>
                                <input type="text" name="stock_unit" id="stock_unit" class="form-control" placeholder="Enter...">
                                <small id="alert_stock_unit" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="date_of_purchase">Date of Purchase</label>
                                <input type="date" name="date_of_purchase" id="date_of_purchase" class="form-control" placeholder="Enter...">
                                <small id="alert_date_of_purchase" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="location">Item Location</label>
                                <input type="text" name="location" id="location" class="form-control" placeholder="Enter...">
                                <small id="alert_location" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="supplier">Supplier</label>
                                <input type="text" name="supplier" id="supplier" class="form-control" placeholder="Enter...">
                                <small id="alert_supplier" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="encoder">Encoder</label>
                                <input type="text" name="encoder" id="encoder" class="form-control" placeholder="Enter..." readonly value="<?= session('name') ?>">
                                <small id="alert_encoder" class="text-danger"></small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->include('templates/loading'); ?>
<?=$this->endSection();?>