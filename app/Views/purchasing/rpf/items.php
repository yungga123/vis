<!-- RPF Items Modal -->
<div class="modal fade" id="rpf_items_modal" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form id="rpf_items_form" class="with-label-indicator" action="<?= url_to('rpf.change'); ?>" method="post" autocomplete="off">
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
                                    <th>Current Stocks</th>
                                    <th>Qty In</th>
                                    <th>Unit</th>
                                    <th>Cost</th>
                                    <th>Total Cost</th>
                                    <th>Received Qty</th>
                                    <th>Received Date</th>
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
                                    <td><strong>Total Amount</strong></td>
                                    <td><strong class="text-danger" id="total_amount"></strong></td>
                                    <td colspan="2">
                                        <div id="total_amount_received"></div>
                                        <small class="text-danger" id="alert_received_q"></small>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <p id="item_note"></p>
                    <input type="hidden" name="id" id="rpf_id_received" class="form-control" readonly>
                    <input type="hidden" name="status" id="status_received" class="form-control" readonly>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>                
                </div>
            </div>
        </form>
    </div>
</div>