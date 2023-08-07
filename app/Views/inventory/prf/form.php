<!-- PRF Modal -->
<div class="modal fade" id="prf_modal" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="prf_form" class="with-label-indicator" action="<?= url_to('prf.save'); ?>" method="post" autocomplete="off">
                <?= csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Add PRF</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">                    
                    <div class="alert alert-info" role="alert">
                        <strong>Note:</strong> 
                        If not empty, initial dropdowns of <strong>Job Order & Inventory Masterlist</strong> are by 10. Type the QUOTATION NUMBER and ITEM MODEL & DESCRIPTION to search if not in the options and then, click to select.
                    </div>
                    <div class="form-group">
                        <input type="hidden" id="prf_id" name="id" readonly>
                        <label class="required" for="job_order_id">Job Order</label>
                        <span>Format: JO # | Quotation # | Client</span>
                        <!-- Select input -->
                        <select class="custom-select" name="job_order_id" id="job_order_id" style="width: 100%;">
                        </select>
                        <!-- Select input -->
                        <small>Only <strong>ACCEPTED</strong> and <strong>FILED</strong> will be displayed.</small>
                        <div class="d-none" id="orig_job_order"></div>
                        <small id="alert_job_order_id" class="text-danger"></small>
                        <div class="mt-2 job-order-details"></div>
                    </div>
                    <div class="form-group">
                        <label class="required" for="inventory_id">Masterlist Item</label>
                        <span>Format: Item # | Model | Description</span>
                        <!-- Select input -->
                        <select class="custom-select" name="inventory_id" id="inventory_id" style="width: 100%;">
                        </select>
                        <!-- Select input -->
                        <div class="d-none" id="orig_item"></div>
                        <small id="alert_inventory_id" class="text-danger"></small>
                        <div class="mt-2 item-details"></div>
                    </div>
                    <div class="form-group">
                        <label class="required" for="quantity_out">Stock/s Out</label>
                        <input type="number" name="quantity_out" id="quantity_out" class="form-control" placeholder="Enter quantity out">
                        <small id="alert_quantity_out" class="text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label class="required" for="process_date">Process Date</label>
                        <input type="date" name="process_date" id="process_date" class="form-control">
                        <small id="alert_process_date" class="text-danger"></small>
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