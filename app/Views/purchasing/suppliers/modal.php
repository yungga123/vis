<!-- Supplier Add Form (MODAL) -->
<div class="modal fade" id="modal_add_supplier" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form_add_supplier" class="with-label-indicator" action="<?= url_to('purchasing.suppliers.save'); ?>" method="post" autocomplete="off">
                <?= csrf_field(); ?>
                <input type="hidden" id="supplier_id" name="id" readonly>
                <div class="modal-header">
                    <h5 class="modal-title">Add New Supplier</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Row -->
                    <div class="row">
                        <!-- Col -->
                        <div class="col-sm-6">
                            <!-- Form Text -->
                            <div class="form-group">
                                <label class="required" for="supplier_name">Supplier Name</label>
                                <input type="text" name="supplier_name" id="supplier_name" class="form-control" placeholder="Enter here...">
                                <small id="alert_supplier_name" class="text-danger"></small>
                            </div>
                            <!-- Form Text -->
                            <div class="form-group">
                                <label class="required" for="supplier_type">Type of Supplier</label>
                                <select type="select" name="supplier_type" id="supplier_type" class="form-control" onchange='selectedOthers(this.value)'>
                                    <?php foreach (get_supplier_type() as $val => $text): ?>
                                        <option value="<?= $val ?>"><?= ucfirst($text) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" name="others_supplier_type" id="others_supplier_type" class="form-control mt-2" placeholder="Please Specify here...">
                                <small id="alert_supplier_type" class="text-danger"></small>
                            </div>
                            <!-- Form Text -->
                            <div class="form-group">
                                <label class="required" for="address">Address</label>
                                <input type="text" name="address" id="address" class="form-control" placeholder="Enter here...">
                                <small id="alert_address" class="text-danger"></small>
                            </div>
                            <!-- Form Text -->
                            <div class="form-group">
                                <label class="required" for="contact_person">Contact Person</label>
                                <input type="text" name="contact_person" id="contact_person" class="form-control" placeholder="Enter here...">
                                <small id="alert_contact_person" class="text-danger"></small>
                            </div>
                            <!-- Form Text -->
                            <div class="form-group">
                                <label class="required" for="contact_number">Contact Number</label>
                                <input type="text" name="contact_number" id="contact_number" class="form-control" placeholder="Enter here...">
                                <small id="alert_contact_number" class="text-danger"></small>
                            </div>
                            <!-- Form Text -->
                            <div class="form-group">
                                <label class="required" for="product">Product</label>
                                <input type="text" name="product" id="product" class="form-control" placeholder="Enter here...">
                                <small id="alert_product" class="text-danger"></small>
                            </div>
                            <!-- Form Text -->
                            <div class="form-group">
                                <label for="viber">Viber Account</label>
                                <input type="text" name="viber" id="viber" class="form-control" placeholder="Enter here...">
                                <small id="alert_viber" class="text-danger"></small>
                            </div>
                            <!-- Form Text -->
                            <div class="form-group">
                                <label for="email_address">Email Address</label>
                                <input type="text" name="email_address" id="email_address" class="form-control" placeholder="Enter here...">
                                <small id="alert_email_address" class="text-danger"></small>
                            </div>
                        </div>
                        <!-- Col -->
                        <div class="col-sm-6">
                            <!-- Form Text -->
                            <div class="form-group">
                                <label class="required" for="payment_terms">Payment Terms</label>
                                <select type="text" name="payment_terms" id="payment_terms" class="form-control">
                                    <?php foreach (get_payment_terms() as $key => $val): ?>
                                        <option value="<?= $key ?>"><?= $val ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small id="alert_payment_terms" class="text-danger"></small>
                            </div>
                            <!-- Form Text -->
                            <div class="form-group">
                                <label class="required" for="payment_mode">Mode of Payment</label>
                                <select name="payment_mode" id="payment_mode" class="form-control" onchange="selectedPaymentMode(this.value)">
                                    <?php foreach (get_supplier_mop() as $val => $text): ?>
                                        <option value="<?= $val ?>"><?= ucfirst($text) ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" name="others_payment_mode" id="others_payment_mode" class="form-control mt-2" placeholder="Please Specify here...">
                                <small id="alert_payment_mode" class="text-danger"></small>
                            </div>
                            <!-- Form Text -->
                            <div class="form-group">
                                <label class="required" for="remarks">Additional Remarks</label>
                                <input type="text" name="remarks" id="remarks" class="form-control" placeholder="Enter here...">
                                <small id="alert_remarks" class="text-danger"></small>
                            </div>
                            <div class="card">
                                <div class="card-header">
                                    <b>Bank Details</b>
                                </div>
                                <div class="card-body">
                                    <!-- Form Text -->
                                    <div class="form-group">
                                        <label class="optional" for="bank_name">Bank Name</label>
                                        <input type="text" name="bank_name" id="bank_name" class="form-control" placeholder="Enter here...">
                                        <small id="alert_bank_name" class="text-danger"></small>
                                    </div>
                                     <!-- Form Text -->
                                     <div class="form-group">
                                        <label class="optional" for="bank_account_name">Bank Account Name</label>
                                        <input type="text" name="bank_account_name" id="bank_account_name" class="form-control" placeholder="Enter here...">
                                        <small id="alert_bank_account_name" class="text-danger"></small>
                                    </div>
                                    <!-- Form Text -->
                                    <div class="form-group">
                                        <label class="optional" for="bank_number">Bank Number</label>
                                        <input type="text" name="bank_number" id="bank_number" class="form-control" placeholder="Enter here...">
                                        <small id="alert_bank_number" class="text-danger"></small>
                                    </div>
                                </div>
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