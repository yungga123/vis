<!-- RPF Items Modal -->
<div class="modal fade" id="rpf_items_modal" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <?= csrf_field(); ?>
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">RPF Item Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5 class="text-center text-bold" id="rpf_id_text"></h5>
                <div class="table-responsive">
                    <table class="table table-hover" id="rpf_items_table">
                        <thead>
                            <tr>
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
                                <th>Total Cost</th>
                                <th>Purpose</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><strong>Total Amount</strong></td>
                                <td><strong class="text-danger" id="total_amount"></strong></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <p id="item_note"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>                
            </div>
        </div>
    </div>
</div>