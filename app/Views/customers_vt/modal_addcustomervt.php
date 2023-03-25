<div class="modal fade" id="modal_customervt" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form_customervt" class="with-label-indicator" action="<?= url_to('customervt.save'); ?>" method="post" autocomplete="off">
                <?= csrf_field(); ?>
                <input type="hidden" id="customervt_id" name="id" readonly>
                <div class="modal-header">
                    <h5 class="modal-title">Add New Customer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="card">
                                <div class="card-header text-center">
                                    <b>Customer Details</b>
                                </div>
                                <div class="card-body row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="required">New Client?</label>
                                            <select name="forecast" id="forecast" type="text" class="form-control" required>
                                                <option value="">---Please Select---</option>
                                                <option value="1">Yes</option>
                                                <option value="0">No</option>
                                            </select>
                                            <small id="alert_forecast" class="form-text text-danger"></small>
                                        </div>
                                        <div class="form-group">
                                            <label class="required">Customer Name</label>
                                            <input name="customer_name" id="customer_name" type="text" class="form-control" placeholder="JC Amoranto / Vinculum Tech" value="" required>
                                            <small id="alert_customer_name" class="form-text text-danger"></small>
                                        </div>
                                        <div class="form-group">
                                            <label class="required">Contact Person</label>
                                            <input name="contact_person" id="contact_person" type="text" class="form-control" placeholder="Mr. JC" value="" required>
                                            <small id="alert_contact_person" class="form-text text-danger"></small>
                                        </div>
                                        <div class="form-group">
                                            <label class="required">Additional Notes</label>
                                            <input name="notes" id="notes" type="text" class="form-control" placeholder="Enter..." value="" required>
                                            <small id="alert_notes" class="form-text text-danger"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header text-center">
                                    <b>Contact Details</b>
                                </div>
                                <div class="card-body row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="required">Contact Number</label>
                                            <input name="contact_number" id="contact_number" type="text" class="form-control" placeholder="0999XXXXXXX / 888X-XXXX" value="" required>
                                            <small id="alert_contact_number" class="form-text text-danger"></small>
                                        </div>
                                        <div class="form-group">
                                            <label>Email Address</label>
                                            <input name="email_address" id="email_address" type="text" class="form-control" placeholder="email@example.com" value="">
                                            <small id="alert_email_address" class="form-text text-danger"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="card">
                                <div class="card-header text-center">
                                    <b>Address</b>
                                </div>
                                <div class="card-body row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="required">Province</label>
                                            <input name="address_province" id="address_province" type="text" id="province" class="form-control" placeholder="NCR" value="" required>
                                            <small id="alert_address_province" class="form-text text-danger"></small>
                                        </div>
                                        <div class="form-group">
                                            <label class="required">City</label>
                                            <input name="address_city" id="address_city" type="text" id="city" class="form-control" placeholder="Muntinlupa City" value="" required>
                                            <small id="alert_address_city" class="form-text text-danger"></small>
                                        </div>
                                        <div class="form-group">
                                            <label class="required">Barangay</label>
                                            <input name="address_brgy" id="address_brgy" type="text" id="barangay" class="form-control" placeholder="Putatan" value="" required>
                                            <small id="alert_address_brgy" class="form-text text-danger"></small>
                                        </div>
                                        <div class="form-group">
                                            <label class="required">Detailed Address</label>
                                            <input name="address_sub" id="address_sub" type="text" class="form-control" placeholder="Soldier's Hills, Blk 35 Lot 14" value="" required>
                                            <small id="alert_address_sub" class="form-text text-danger"></small>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header text-center">
                                    <b>Referrals</b>
                                </div>
                                <div class="card-body row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="optional">Referred By</label>
                                            <input name="referred_by" id="referred_by" type="text" id="province" class="form-control" placeholder="Enter referral here" value="">
                                            <small id="alert_referred_by" class="form-text text-danger"></small>
                                        </div>

                                        <div class="form-group">
                                            <label class="required">Source of Contact</label>
                                            <select name="source" id="source" type="text" id="province" class="form-control" placeholder="NCR" value="" required>
                                                <option value="">---Please Select---</option>
                                                <option value="BNI REFERRAL">BNI REFERRAL</option>
                                                <option value="SOCIAL MEDIA">SOCIAL MEDIA</option>
                                                <option value="WALK IN ">WALK IN </option>
                                                <option value="SATURATION">SATURATION</option>
                                                <option value="THIRD PARTY REFERRAL">THIRD PARTY REFERRAL</option>
                                            </select>
                                            <small id="alert_source" class="form-text text-danger"></small>
                                        </div>
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