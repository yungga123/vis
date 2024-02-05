<!-- Employment Status Modal -->
<div class="modal fade" id="billing_invoice_modal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="billing_invoice_form" class="with-label-indicator" action="<?= url_to('finance.billing_invoice.save'); ?>" method="post" autocomplete="off">
                <?= csrf_field(); ?>
                <input type="hidden" id="id" name="id" readonly>
                <div class="modal-header">
                    <h5 class="modal-title">Create Billing Invoice</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="callout callout-info">
                        <strong>Note:</strong> 
                        If not empty, initial dropdowns of <strong>Task/Leads</strong> are by 10. Type the <strong>TASKLEAD ID, QUOTATION #, CLIENT OR MANAGER</strong> to search if not in the options and then, click to select.
                    </div>
                    <div class="form-group">
                        <label class="required mb-0" for="tasklead_id">Task/Leads</label>
                        <div>Format: TASKLEAD ID | QUOTATION # | CLIENT | MANAGER | QUOTATION TYPE</div>
                        <select class="custom-select" name="tasklead_id" id="tasklead_id" style="width: 100%;"></select required>
                        <div id="orig_tasklead"></div>
                        <small id="alert_tasklead_id" class="text-danger"></small>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required" for="bill_type">Bill Type</label>
                                <select class="form-control" name="bill_type" id="bill_type" style="width: 100%;" required>
                                    <?php foreach (get_bill_types() as $val => $text): ?>
                                        <option value="<?= $val ?>"><?= $text ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small id="alert_bill_type" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label class="required" for="payment_method">Payment Method</label>
                                <select class="form-control" name="payment_method" id="payment_method" style="width: 100%;" required>
                                    <?php foreach (get_supplier_mop() as $val => $text): ?>
                                        <option value="<?= $val ?>"><?= $text ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small id="alert_payment_method" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label class="required" for="status">Billing Status</label>
                                <select class="form-control" name="status" id="status" style="width: 100%;" required>
                                    <?php foreach (get_billing_status() as $val => $text): ?>
                                        <option value="<?= $val ?>"><?= $text ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small id="alert_status" class="text-danger"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required" for="due_date">Due Date</label>
                                <input type="date" class="form-control" name="due_date" id="due_date" placeholder="Due Date">
                                <small id="alert_due_date" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label class="required" for="billing_amount">Billing Amount</label>
                                <input type="number" class="form-control" name="billing_amount" id="billing_amount" placeholder="Billing Amount" step="0.01">
                                <small id="alert_billing_amount" class="text-danger"></small>
                            </div>
                            <div class="form-group amount_paid">
                                <label for="amount_paid">Amount Paid</label>
                                <input type="number" class="form-control" name="amount_paid" id="amount_paid" placeholder="Amount Paid" step="0.01">
                                <small id="alert_amount_paid" class="text-danger"></small>
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