<!-- PO Items Modal -->
<div class="modal fade" id="po_items_modal" aria-hidden="true">
    <div class="modal-dialog modal-xl">
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
                                <th>Brand</th>
                                <th>Item Model</th>
                                <th>Item Description</th>
                                <th>Supplier</th>
                                <th>Unit</th>
                                <th>Size</th>
                                <th>Current Stocks</th>
                                <th>Qty In</th>
                                <th>Cost</th>
                                <th>Total Cost</th>
                                <th>Purpose</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <th colspan="11" class="text-right">Total Amount</th>
                                <th class="text-danger total_amount"></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>                
            </div>
        </div>
    </div>
</div>