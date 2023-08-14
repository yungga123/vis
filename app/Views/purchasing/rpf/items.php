<!-- RPF Items Modal -->
<div class="modal fade" id="rpf_items_modal" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form id="rpf_received_form" class="with-label-indicator" action="<?= url_to('rpf.change'); ?>" method="post" autocomplete="off">
            <?= csrf_field(); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">RPF Items Detials</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5 class="text-center text-bold" id="rpf_id_text"></h5>
                    <table class="table table-hover" id="rpf_items_table">
                        <thead>
                            <tr>
                                <th>Item #</th>
                                <th>Category</th>
                                <th>Item Model</th>
                                <th>Item Description</th>
                                <th>Current Stocks</th>
                                <th>Quantity In</th>
                                <th>Unit</th>
                                <th>Cost</th>
                                <th>Total Cost</th>
                                <th>Received Qty</th>
                                <th>Received Date</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <div class="form-group mt-3 d-none" id="received_remarks">
                        <input type="hidden" name="id" id="rpf_id_received" class="form-control" readonly>
                        <input type="hidden" name="status" id="status_received" class="form-control" readonly>

                        <label class="required" for="remarks">Remarks</label>
                        <textarea name="remarks" id="remarks" class="form-control" cols="3" rows="3" placeholder="Enter remarks"></textarea>
                        <small id="alert_remarks" class="text-danger"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>                
                </div>
            </div>
        </form>
    </div>
</div>