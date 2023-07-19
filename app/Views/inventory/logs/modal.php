<!-- Modal -->
<div class="modal fade" id="modal_inventory_logs" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form_inventory_logs" class="with-label-indicator" action="<?= url_to('inventory.logs.save'); ?>" method="post" autocomplete="off">
                <?= csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title">New Item In</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6 mt-2 d-none">
                            <input type="hidden" id="action_logs" name="action_logs" readonly>
                            <div class="form-group">
                                <label class="required" for="inventory_id">Item Number</label>
                                <input type="number" name="inventory_id" id="inventory_parent_id" class="form-control" placeholder="Enter Item Number" readonly>
                            </div>
                            <div class="form-group">
                                <label class="required" for="item_description_logs">Item Description</label>
                                <input type="text" name="item_description_logs" id="item_description_logs" class="form-control" placeholder="Enter Item Description" readonly>
                            </div>
                            <div class="form-group">
                                <label class="required" for="item_brand_logs">Item Brand</label>
                                <input type="text" name="item_brand_logs" id="item_brand_logs" class="form-control" placeholder="Enter Item Brand" readonly>
                            </div>
                            <div class="form-group">
                                <label class="required" for="item_model_logs">Item Model</label>
                                <input type="text" name="item_model_logs" id="item_model_logs" class="form-control" placeholder="Enter Item Model" readonly>
                            </div>
                            <div class="form-group">
                                <label class="required" for="item_sdp_logs">Dealer's Price</label>
                                <input type="number" name="item_sdp_logs" id="item_sdp_logs" class="form-control" placeholder="Enter Dealer's Price">
                                <small id="alert_item_sdp_logs" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="item_srp_logs">Retail Price</label>
                                <input type="number" name="item_srp_logs" id="item_srp_logs" class="form-control" placeholder="Enter Retail Price">
                                <small id="alert_item_srp_logs" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="project_price_logs">Project Price</label>
                                <input type="number" name="project_price_logs" id="project_price_logs" class="form-control" placeholder="Enter Project Price">
                                <small id="alert_project_price_logs" class="text-danger"></small>
                            </div>
                        </div>
                        <div class="col-sm-6 d-none">
                            <div class="form-group">
                                <label for="item_size_logs">Item Size</label>
                                <select name="item_size_logs" id="item_size_logs" class="custom-select select2 mt-2" data-placeholder="Select Item Size" style="width: 100%;">
                                    <option value="">Select Item Size</option>
                                </select>
                                <small id="alert_item_size_logs" class="text-danger"></small>
                                <p>Not in the list? <a href="#" role="button" onclick="openDropdownModal('Item Size', 'SIZE', '#item_size_logs')">Add size here</a>!</p>
                            </div>
                            <div class="form-group">
                                <label class="required" for="stocks_logs">Quantity</label>
                                <input type="text" name="stocks_logs" id="stocks_logs" class="form-control" placeholder="Enter Quantity">
                                <small id="alert_stocks_logs" class="text-danger"></small>
                                <input type="hidden" id="parent_stocks" name="parent_stocks" readonly>
                            </div>
                            <div class="form-group">
                                <label for="stock_unit_logs">Item Unit</label>
                                <select name="stock_unit_logs" id="stock_unit_logs" class="custom-select select2 mt-2" data-placeholder="Select Item Unit" style="width: 100%;">
                                </select>
                                <small id="alert_stock_unit_logs" class="text-danger"></small>
                                <p>Not in the list? <a href="#" role="button" onclick="openDropdownModal('Item Unit', 'UNIT', '#stock_unit_logs')">Add unit here</a>!</p>
                            </div>
                            <div class="form-group">
                                <label class="required" for="date_of_purchase_logs">Date of Purchase</label>
                                <input type="date" name="date_of_purchase_logs" id="date_of_purchase_logs" class="form-control" placeholder="Enter...">
                                <small id="alert_date_of_purchase_logs" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="location_logs">Item Location</label>
                                <input type="text" name="location_logs" id="location_logs" class="form-control" placeholder="Enter Item Location">
                                <small id="alert_location_logs" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="supplier_logs">Supplier</label>
                                <input type="text" name="supplier_logs" id="supplier_logs" class="form-control" placeholder="Enter Supplier">
                                <small id="alert_supplier_logs" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label class="required" for="encoder_logs">Encoder</label>
                                <input type="text" name="encoder_logs" id="encoder_logs" class="form-control" placeholder="Enter..." readonly value="<?= session('name') ?>">
                                <small id="alert_encoder_logs" class="text-danger"></small>
                            </div>
                        </div>
                    </div>
                    <div class="item-details-wrapper"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>