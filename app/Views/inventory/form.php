<!-- Modal -->
<div class="modal fade" id="inventory_modal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="inventory_form" class="with-label-indicator" action="<?= url_to('inventory.save'); ?>" method="post" autocomplete="off">
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
                        <div class="col-12">
                            <div class="callout callout-info">
                                <p>Want to add new record for Category and Sub-Category? Go to <a href="<?= url_to('inventory.dropdown.home') ?>"><strong>Inventory Dropdowns</strong></a>!</p>
                            </div>
                        </div>
                        <div class="col-sm-6 mt-2">
                            <div class="form-group">
                                <label class="required" for="category">Category</label>
                                <select name="category" id="category" class="custom-select select2" data-placeholder="Select a Category">
                                    <option value="">Select a Category</option>
                                    <?= $categories ?>
                                </select>
                                <small id="alert_category" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label class="required" for="sub_category">Sub-Category</label>
                                <select name="sub_category" id="sub_category" class="custom-select select2" data-placeholder="Select a Sub-Category">
                                </select>
                                <small id="alert_sub_category" class="text-danger"></small>
                            </div>
                            <div class="form-group">        
                                <label class="required" for="item_brand">Item Brand</label>
                                <select name="item_brand" id="item_brand" class="custom-select select2 mt-2" data-placeholder="Select Item Brand" style="width: 100%;">
                                    <option value="">Select Item Brand</option>
                                </select>
                                <small id="alert_item_brand" class="text-danger"></small>
                                <p>Not in the list? <a href="#" role="button" onclick="openDropdownModal('Item Brand', 'BRAND', '#item_brand')">Add brand here</a>!</p>
                            </div>
                            <div class="form-group">
                                <label class="required" for="item_model">Item Model</label>
                                <input type="text" name="item_model" id="item_model" class="form-control" placeholder="Enter Item Model">
                                <small id="alert_item_model" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label class="required" for="item_description">Item Description</label>
                                <input type="text" name="item_description" id="item_description" class="form-control" placeholder="Enter Item Description">
                                <small id="alert_item_description" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label class="required" for="item_sdp">Dealer's Price</label>
                                <input type="number" name="item_sdp" id="item_sdp" class="form-control" placeholder="Enter Dealer's Price">
                                <small id="alert_item_sdp" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="item_srp">Retail Price</label>
                                <input type="number" name="item_srp" id="item_srp" class="form-control" placeholder="Enter Retail Price">
                                <small id="alert_item_srp" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="project_price">Project Price</label>
                                <input type="number" name="project_price" id="project_price" class="form-control" placeholder="Enter Project Price">
                                <small id="alert_project_price" class="text-danger"></small>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="item_size">Item Size</label>
                                <select name="item_size" id="item_size" class="custom-select select2 mt-2" data-placeholder="Select Item Size" style="width: 100%;">
                                    <option value="">Select Item Size</option>
                                </select>
                                <small id="alert_item_size" class="text-danger"></small>
                                <p>Not in the list? <a href="#" role="button" onclick="openDropdownModal('Item Size', 'SIZE', '#item_size')">Add size here</a>!</p>
                            </div>
                            <div class="form-group">
                                <label for="number">Quantity</label>
                                <input type="text" name="stocks" id="stocks" class="form-control" placeholder="Enter Quantity">
                                <small id="alert_stocks" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="stock_unit">Item Unit</label>
                                <select name="stock_unit" id="stock_unit" class="custom-select select2 mt-2" data-placeholder="Select Item Unit" style="width: 100%;">
                                </select>
                                <small id="alert_stock_unit" class="text-danger"></small>
                                <p>Not in the list? <a href="#" role="button" onclick="openDropdownModal('Item Unit', 'UNIT', '#stock_unit')">Add unit here</a>!</p>
                            </div>
                            <div class="form-group">
                                <label for="date_of_purchase">Date of Purchase</label>
                                <input type="date" name="date_of_purchase" id="date_of_purchase" class="form-control" placeholder="Enter...">
                                <small id="alert_date_of_purchase" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="location">Item Location</label>
                                <input type="text" name="location" id="location" class="form-control" placeholder="Enter Item Location">
                                <small id="alert_location" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="supplier_id">Supplier</label>
                                <small>Enter the supplier name to search.</small>
                                <select class="custom-select supplier_id" name="supplier_id" id="supplier_id" style="width: 100%;">
                                </select>
                                <small id="alert_supplier_id" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label class="required" for="encoder">Encoder</label>
                                <input type="text" name="created_by" id="encoder" class="form-control" placeholder="Enter..." readonly value="<?= session('name') ?>">
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
<!-- Modal for Dropdowns -->
<div class="modal fade" id="modal_dropdown" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">            
            <div class="modal-header">
                <h5 class="modal-title">Create Dropdown</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body"> 
                <form id="form_dropdown" action="<?= url_to('inventory.dropdown.save'); ?>" method="post" autocomplete="off" class="with-label-indicator">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="other_category_type" id="other_category_type" readonly>
                    <div class="form-group">
                        <label class="required" for="dropdown">Description</label>
                        <textarea rows="3" name="dropdown" id="dropdown" class="form-control" placeholder="Ex. Generic"></textarea>
                        <small id="alert_dropdown" class="text-danger"></small>
                        <p class="text-muted"><strong>Comma (,)</strong> separated for multiple insert. Ex. "Generic, Parasonic, Hikvision"</p>
                    </div>
                    <div class="d-flex justify-content-end mt-2">                        
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" data-target="#modal_dropdown">Close</button>
            </div>
        </div>
    </div>
</div>