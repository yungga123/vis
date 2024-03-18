<!-- PO Items Modal -->
<div class="modal fade" id="po_items_modal" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form id="po_items_form" class="with-label-indicator" action="<?= url_to('purchase_order.change'); ?>" method="post" autocomplete="off">
            <?= csrf_field(); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Purchase Order Items</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5 class="text-center text-bold" id="po_id_text"></h5>
                    <div class="table-responsive">
                        <table class="table table-hover" id="po_items_table">
                            <thead>
                                <tr>
                                    <th>RPF #</th>
                                    <th>Item #</th>
                                    <th>Category</th>
                                    <th>Item Model</th>
                                    <th>Item Description</th>
                                    <th>Supplier</th>
                                    <th>Unit</th>
                                    <th>Size</th>
                                    <th>Current Stocks</th>
                                    <th>Qty In</th>
                                    <th>Cost</th>
                                    <th>Discount</th>
                                    <th>Total Cost</th>
                                    <th>Received Qty</th>
                                    <th>Received Date</th>
                                    <th>Purpose</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr class="text-bold">
                                    <th colspan="10" class="text-right">Total Amount:</th>
                                    <td class="text-danger total_cost"></td>
                                    <td class="text-danger total_discount"></td>
                                    <td class="text-danger total_amount"></td>
                                    <td colspan="2">
                                        <div class="text-danger" id="total_amount_received"></div>
                                        <small class="text-danger" id="alert_received_q"></small>
                                    </td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <input type="hidden" name="id" id="po_id_received" class="form-control" readonly>
                    <input type="hidden" name="rpf_id" id="rpf_id_received" class="form-control" readonly>
                    <input type="hidden" name="status" id="status_received" class="form-control" readonly>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>                
                </div>
            </div>
        </form>
    </div>
</div>