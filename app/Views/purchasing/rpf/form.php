<!-- RPF Modal -->
<div class="modal fade" id="rpf_modal" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form id="rpf_form" class="with-label-indicator" action="<?= url_to('rpf.save'); ?>" method="post" autocomplete="off">
                <?= csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Add RPF</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">                    
                    <div class="alert alert-info" role="alert">
                        <strong>Note:</strong> 
                        If not empty, initial dropdowns of <strong>Inventory Masterlist & Supplier</strong> are by 10. Type the ITEM MODEL & DESCRIPTION and SUPPLIER NAME to search if not in the options and then, click to select.
                    </div>
                    <div class="form-group">
                        <label class="required text-center">Masterlist Items</label>
                        <div>Format: Item # | Model | Description</div>
                    </div>
                    <div>
                        <table class="table" id="item_field_table">
                            <thead>
                                <tr>
                                    <th width="30%">Item Details</th>
                                    <th width="5%">Available</th>
                                    <th width="10%">Quantity In</th>
                                    <th width="30%">Supplier</th>
                                    <th width="22%">Purpose <i>(Optional)</i></th>
                                    <th width="3%"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <select class="custom-select inventory_id" name="inventory_id[]" style="width: 100%;"></select>
                                    </td>
                                    <td>
                                        <input type="number" name="item_available[]" class="form-control item_available" placeholder="Stock" readonly>
                                    </td>
                                    <td>
                                        <input type="number" name="quantity_in[]" class="form-control quantity_in" placeholder="Qty" min="1" required>
                                    </td>
                                    <td>
                                        <select class="custom-select supplier_id" name="supplier_id[]" style="width: 100%;"></select>
                                    </td>
                                    <td>
                                        <input type="text" name="purpose[]" class="form-control purpose" placeholder="Purpose">
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-success" onclick="toggleItemField()" title="Add new item field">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td>
                                        <small id="alert_inventory_id" class="text-danger"></small>
                                    </td>
                                    <td></td>
                                    <td>
                                        <small id="alert_quantity_in" class="text-danger"></small>
                                    </td>
                                    <td>
                                        <small id="alert_supplier_id" class="text-danger"></small>
                                    </td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="form-group">
                        <label class="required" for="date_needed">Delivery Date</label>
                        <input type="date" name="date_needed" id="date_needed" class="form-control" value="<?= current_date() ?>">
                        <small id="alert_date_needed" class="text-danger"></small>
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