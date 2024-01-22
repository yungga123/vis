<!-- PRF Items Modal -->
<div class="modal fade" id="prf_items_modal" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form id="prf_file_form" class="with-label-indicator" action="<?= url_to('prf.change'); ?>" method="post" autocomplete="off">
            <?= csrf_field(); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">PRF Item Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5 class="text-center text-bold" id="prf_id_text"></h5>
                    <div class="table-responsive">
                        <table class="table table-hover" id="prf_items_table">
                            <thead>
                                <tr>
                                    <th>Item #</th>
                                    <th>Supplier</th>
                                    <th>Category</th>
                                    <th>Item Model</th>
                                    <th>Item Description</th>
                                    <th>Item Unit</th>
                                    <th>Current Stocks</th>
                                    <th>Quantity Out</th>
                                    <th>Returned</th>
                                    <th>Consumed</th>
                                    <th>Returned Date</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <p class="mt-4 text-center" id="note_item_out"></p>
                    <div class="form-group mt-3" id="file_remarks">
                        <input type="hidden" name="id" id="prf_id_file" class="form-control" readonly>
                        <input type="hidden" name="status" id="status_file" class="form-control" readonly>

                        <label class="required" for="remarks">Remarks</label>
                        <textarea name="remarks" id="remarks" class="form-control" cols="3" rows="3" placeholder="Enter remarks"></textarea>
                        <small id="alert_remarks" class="text-danger"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <div class="change-btn"></div>        
                </div>
            </div>
        </form>
    </div>
</div>