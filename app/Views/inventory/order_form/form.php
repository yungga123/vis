<!-- PRF Modal -->
<div class="modal fade" id="order_form_modal" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form id="order_form_form" class="with-label-indicator" action="<?= url_to('inventory.order_form.save'); ?>" method="post" autocomplete="off">
                <?= csrf_field(); ?>
                <input type="hidden" id="id" name="id" readonly>
                <input type="hidden" id="total_amount" name="total_amount" readonly>
                <input type="hidden" id="total_discount" name="total_discount" readonly>
                <input type="hidden" id="total_amount_no_discount" readonly>
                <div class="modal-header">
                    <h5 class="modal-title">Add PRF</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">                    
                    <div class="callout callout-info mb-3">
                        <strong>Note:</strong> 
                        If not empty, initial dropdowns of <strong>Client</strong> is by 10. Type the <strong>CLIENT NAME or ID</strong> to search if not in the options and then, click to select.
                    </div>
                    <div class="row">
                        <div class="col-sm-12 col-lg-6">
                            <div class="form-group">
                                <label class="required" for="customer_id">Client</label>
                                <div class="mb-2">                                    
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="customer_type" id="commercial" value="commercial" checked>
                                        <label class="form-check-label" for="commercial">Commercial</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="customer_type" id="residential" value="residential">
                                        <label class="form-check-label" for="residential">Residentail</label>
                                    </div>
                                </div>
                                <select class="custom-select" id="customer_id" name="customer_id" style="width: 100%;"></select>
                                <small id="alert_customer_id" class="text-danger"></small>
                            </div>
                            <div class="form-group d-none" id="client_branch_wrapper">
                                <label for="customer_branch_id">Client Branch</label>
                                <select class="custom-select" id="customer_branch_id" name="customer_branch_id" style="width: 100%;"></select>
                                <small id="alert_customer_branch_id" class="text-danger"></small>
                            </div>
                        </div>
                        <div class="col-sm-12 col-lg-6">
                            <div class="row">
                                <div class="col-sm-12 col-lg-6">
                                    <div class="form-group">
                                        <label class="required" for="purchase_date">Purchase Date</label>
                                        <input type="date" name="purchase_date" id="purchase_date" class="form-control" value="<?= current_date() ?>">
                                        <small id="alert_purchase_date" class="text-danger"></small>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-lg-6">
                                    <div class="form-group">
                                        <label class="required" for="purchase_time">Purchase Time</label>
                                        <input type="time" name="purchase_time" id="purchase_time" class="form-control" value="">
                                        <small id="alert_purchase_time" class="text-danger"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="required text-center">Masterlist Items</label>
                                <div>Format: Item # | Model | Description | Size</div>
                            </div>
                            <div class="table-responsive">
                                <table class="table" id="item_field_table">
                                    <thead>
                                        <tr class="text-bold">
                                            <td widtd="40%">Item Details</td>
                                            <td class="text-center">Current Stocks</td>
                                            <td class="text-center">Item Price</td>
                                            <td>Quantity</td>
                                            <td>Discount</td>
                                            <td>Total Price</td>
                                            <td>Button</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr id="row_0">
                                            <td>
                                                <select class="custom-select inventory_id" name="inventory_id[]" style="width: 100%;"></select>
                                                <div class="original-item"></div>
                                            </td>
                                            <td class="text-center item_stocks"></td>
                                            <td class="item_price">
                                                <span></span>
                                                <input type="hidden" name="item_price[]" readonly>
                                            </td>
                                            <td class="quantity">
                                                <input type="number" name="quantity[]" class="form-control quantity" placeholder="Quantity" min="1" step="0.5" onkeyup="calculate(this.value, 'row_0', 'quantity')" required>
                                            </td>
                                            <td class="discount">
                                                <input type="number" name="discount[]" class="form-control discount" placeholder="Discount" step="0.01" onkeyup="calculate(this.value, 'row_0', 'discount')">
                                            </td>
                                            <td class="total_price">
                                                <span></span>
                                                <input type="hidden" name="total_price[]" readonly>
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
                                            <td colspan="2" class="text-right text-bold">Grand Totals</td>
                                            <td class="total_item_price text-bold text-danger"></td>
                                            <td class="total_quantity text-bold text-danger"></td>
                                            <td class="total_discount text-bold text-danger"></td>
                                            <td class="grand_total text-bold text-danger"></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <hr>
                            <div class="row d-none with_vat mt-3">
                                <div class="col-sm-12 col-md-6">
                                    <div class="form-group">
                                        <label class="not-include" for="vat_amount">Vat Amount</label>
                                        <input type="number" class="form-control" name="vat_amount" id="vat_amount" placeholder="Vat Amount" step="0.01" readonly>
                                        <small id="alert_vat_amount" class="text-danger"></small>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6">
                                    <div class="form-group">
                                        <label class="not-include" for="grand_total">Grand Total Vat Inclusive</label>
                                        <input type="number" class="form-control" name="grand_total" id="grand_total" placeholder="Grand Total" step="0.01" readonly>
                                        <small id="alert_grand_total" class="text-danger"></small>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input class="custom-control-input" type="checkbox" name="with_vat" id="with_vat" value="1">
                                    <label for="with_vat" class="custom-control-label">With Vat<span></span>?</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="remarks">Remarks</label>
                                <textarea name="remarks" id="remarks" class="form-control" cols="3" rows="3" placeholder="Enter remarks"></textarea>
                                <small id="alert_remarks" class="text-danger"></small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success btn-save">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>