<!-- Modal -->
<div class="modal fade" id="purchase_order_modal" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form id="purchase_order_form" class="with-label-indicator" action="<?= url_to('purchase_order.save'); ?>" method="post" autocomplete="off">
                <?= csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Add PO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info" role="alert">
                        <strong>Note:</strong> 
                        If not empty, initial dropdowns of <strong>Request to Purchase Forms</strong> are by 10. Type the 'RPF ID' to search if not in the options and then, click to select.
                    </div>
                    <div class="form-group">
                        <label class="required text-center" for="rpf_id">Request to Purchase Forms</label>
                        <input type="hidden" name="id" id="po_id" class="form-control" readonly>
                        <div class="table-responsive">
                            <table class="table" id="rpf_field_table" width="100%">
                                <thead>
                                    <tr>
                                        <th>RPF ID | Requested By</th>
                                        <th>Date Needed</th>
                                        <th>Requested At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <select class="custom-select rpf_id" name="rpf_id" id="rpf_id" style="width: 100%;"></select>
                                            <small id="alert_rpf_id" class="text-danger"></small>
                                        </td>
                                        <td><span id="date_needed_text"></span></td>
                                        <td><span id="requested_at_text"></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="d-none" id="rpf_item_details">
                        <h4 class="text-center">RPF Items</h4>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Item #</th>
                                        <th>Brand</th>
                                        <th>Model</th>
                                        <th>Description</th>
                                        <th>Supplier</th>
                                        <th>Current Stocks</th>
                                        <th>Qty In</th>
                                        <th>Unit</th>
                                        <th>Unit Cost</th>
                                        <th>Total Cost</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="9" class="text-right">Total Amount</th>
                                        <th class="text-danger total_amount"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="form-group d-none" id="attention_to_wrapper">
                        <label for="attention_to">Attention To</label>
                        <input type="text" name="attention_to" id="attention_to" class="form-control" placeholder="Enter a name">
                        <small id="alert_attention_to" class="text-danger"></small>
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