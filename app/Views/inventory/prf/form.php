<!-- PRF Modal -->
<div class="modal fade" id="prf_modal" aria-hidden="true">
    <div class="modal-dialog modal-xl">
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
                    <div class="row">
                        <div class="col-sm-12 col-lg-7">
                            <div class="form-group">
                                <input type="hidden" id="prf_id" name="id" readonly>
                                <label class="required" for="job_order_id">Job Order</label>
                                <div>Format: JO # | Quotation # | Client</div>
                                <!-- Select input -->
                                <select class="custom-select" name="job_order_id" id="job_order_id" style="width: 100%;">
                                </select>
                                <!-- Select input -->
                                <small>Only <strong>ACCEPTED</strong> and <strong>FILED</strong> will be displayed.</small>
                                <div class="d-none" id="orig_job_order"></div>
                                <div id="alert_job_order_id" class="text-sm text-danger"></div>
                                <div class="mt-2 job-order-details"></div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-lg-5">
                            <div class="form-group">
                                <label class="required" for="process_date">Date Needed</label>
                                <div>Format: MM/DD/YYYY</div>
                                <input type="date" name="process_date" id="process_date" class="form-control">
                                <small id="alert_process_date" class="text-danger"></small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label class="required text-center">Masterlist Items</label>
                                <div>Format: Model | Description | Supplier</div>
                            </div>
                            <div class="table-responsive">
                                <table class="table" id="item_field_table">
                                    <thead>
                                        <tr>
                                            <th width="55%">Item Details</th>
                                            <th width="15%" class="text-center">Item Unit</th>
                                            <th width="20%">Quantity Out</th>
                                            <th width="5%">Button</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <select class="custom-select inventory_id" name="inventory_id[]" style="width: 100%;"></select>
                                                <div class="original-item"></div>
                                            </td>
                                            <td class="text-center items-center">
                                                <input type="hidden" name="item_available[]" class="form-control item_available" placeholder="Stock" readonly>
                                                <div class="item-unit text-bold"></div>
                                            </td>
                                            <td>
                                                <input type="number" name="quantity_out[]" class="form-control quantity_out" placeholder="Quantity" min="1" required>
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
                                            <td>
                                                <small id="alert_quantity_out" class="text-danger"></small>
                                            </td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
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