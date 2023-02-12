<div class="modal fade" id="modal_branchcustomervt" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form_branchcustomervt" action="<?= url_to('customervtbranch.save'); ?>" method="post" autocomplete="off">
                <?= csrf_field(); ?>
                <input type="hidden" id="branch_id" name="id" readonly>
                <input type="hidden" id="get_customer_url" name="get_customer_url" value="<?= url_to('customervtbranch.getcustomer') ?>" readonly>
                <input type="hidden" id="editBranch_url" value="<?= url_to('customervtbranch.edit') ?>">
                <input type="hidden" id="removeBranch_url" value="<?= url_to('customervtbranch.delete') ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Add Customer Branch</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="card">
                                <div class="card-header"><b>Customer Details</b></div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="bcustomer_id">Main Customer</label>
                                        <input type="text" class="form-control" name="customer_id" id="bcustomer_id" readonly>
                                        <small id="small_bcustomer_id" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="bbranch_name">Branch Name</label>
                                        <input type="text" class="form-control" name="branch_name" id="bbranch_name" placeholder="Enter here...">
                                        <small id="small_bbranch_name" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="bcontact_person">Contact Person</label>
                                        <input type="text" class="form-control" name="contact_person" id="bcontact_person" placeholder="Enter here...">
                                        <small id="small_bcontact_person" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="bcontact_number">Contact Number</label>
                                        <input type="text" class="form-control" name="contact_number" id="bcontact_number" placeholder="Enter here...">
                                        <small id="small_bcontact_number" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="bemail_address">Email Address</label>
                                        <input type="text" class="form-control" name="email_address" id="bemail_address" placeholder="Enter here...">
                                        <small id="small_bemail_address" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="bnotes">Notes</label>
                                        <input type="text" class="form-control" name="notes" id="bnotes" placeholder="Enter here...">
                                        <small id="small_bnotes" class="form-text text-muted"></small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="card">
                                <div class="card-header text-center">
                                    <b>Address</b>
                                </div>

                                <div class="card-body">

                                    <div class="form-group">
                                        <label for="baddress_province">Province</label>
                                        <input type="text" class="form-control" name="address_province" id="baddress_province" placeholder="Enter here...">
                                        <small id="small_baddress_province" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="baddress_city">City</label>
                                        <input type="text" class="form-control" name="address_city" id="baddress_city" placeholder="Enter here...">
                                        <small id="small_baddress_city" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="baddress_brgy">Barangay</label>
                                        <input type="text" class="form-control" name="address_brgy" id="baddress_brgy" placeholder="Enter here...">
                                        <small id="small_baddress_brgy" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="baddress_sub">Sub Address</label>
                                        <input type="text" class="form-control" name="address_sub" id="baddress_sub" placeholder="Enter here...">
                                        <small id="small_baddress_sub" class="form-text text-muted"></small>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success float-right" onclick="addBranch()">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>