<div class="modal fade" id="customer_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form id="customer_form" class="with-label-indicator" action="<?= url_to('customer.save'); ?>" method="post" autocomplete="off">
                <?= csrf_field(); ?>
                <input type="hidden" id="customer_id" name="id" readonly>
                <div class="modal-header">
                    <h5 class="modal-title">Add New Client</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12 col-lg-6">
                            <div class="card">
                                <div class="card-header text-center">
                                    <h5>Client Details</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label class="required">Client Type</label>
                                        <select name="type" id="type" class="form-control" required>
                                            <option value="COMMERCIAL">Commercial</option>
                                            <option value="RESIDENTIAL">Residential</option>
                                        </select>
                                        <small id="alert_type" class="form-text text-danger"></small>
                                    </div>
                                    <div class="form-group">
                                        <label class="required">New Client?</label>
                                        <select name="forecast" id="forecast" class="form-control" required>
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                        <small id="alert_forecast" class="form-text text-danger"></small>
                                    </div>
                                    <div class="form-group">
                                        <label class="required">Client Name</label>
                                        <input name="name" id="name" type="text" class="form-control" placeholder="JC Amoranto / Vinculum Tech"  required>
                                        <small id="alert_name" class="form-text text-danger"></small>
                                    </div>
                                    <div class="form-group">
                                        <label class="required">Contact Person</label>
                                        <input name="contact_person" id="contact_person" type="text" class="form-control" placeholder="Mr. JC"  required>
                                        <small id="alert_contact_person" class="form-text text-danger"></small>
                                    </div>
                                    <div class="form-group">
                                        <label>Notes</label>
                                        <input name="notes" id="notes" type="text" class="form-control" placeholder="Enter...">
                                        <small id="alert_notes" class="form-text text-danger"></small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-lg-6">
                            <div class="card">
                                <div class="card-header text-center">
                                    <h5>Contact Details</h5>
                                </div>
                                <div class="card-body row">
                                    <div class="col-sm-12">
                                        <p id="unformatted_cn"></p>
                                        <div class="form-group">
                                            <label class="required">Mobile Number</label>
                                            <input name="contact_number" id="contact_number" type="text" class="form-control" placeholder="09XX-XXX-XXXX" required>
                                            <small id="alert_contact_number" class="form-text text-danger"></small>
                                        </div>
                                        <div class="form-group">
                                            <label>Mobile Number 2</label>
                                            <input name="contact_number2" id="contact_number2" type="text" class="form-control" placeholder="09XX-XXX-XXXX">
                                            <small id="alert_contact_number" class="form-text text-danger"></small>
                                        </div>
                                        <div class="form-group">
                                            <label>Telephone Number</label>
                                            <input name="telephone" id="telephone" type="text" class="form-control" placeholder="(02) 888X-XXXX">
                                            <small id="alert_telephone" class="form-text text-danger"></small>
                                        </div>
                                        <div class="form-group">
                                            <label>Email Address</label>
                                            <input name="email_address" id="email_address" type="text" class="form-control" placeholder="email@example.com" >
                                            <small id="alert_email_address" class="form-text text-danger"></small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-lg-6">
                            <div class="card">
                                <div class="card-header text-center">
                                    <h5>Address</h5>
                                </div>
                                <div class="card-body row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="required">Province</label>
                                            <input name="province" id="province" type="text" id="province" class="form-control" placeholder="NCR"  required>
                                            <small id="alert_province" class="form-text text-danger"></small>
                                        </div>
                                        <div class="form-group">
                                            <label class="required">City</label>
                                            <input name="city" id="city" type="text" id="city" class="form-control" placeholder="Muntinlupa City"  required>
                                            <small id="alert_city" class="form-text text-danger"></small>
                                        </div>
                                        <div class="form-group">
                                            <label>Barangay</label>
                                            <input name="barangay" id="barangay" type="text" id="barangay" class="form-control" placeholder="Putatan">
                                            <small id="alert_barangay" class="form-text text-danger"></small>
                                        </div>
                                        <div class="form-group">
                                            <label class="required">Detailed Address</label>
                                            <input name="subdivision" id="subdivision" type="text" class="form-control" placeholder="Soldier's Hills, Blk 35 Lot 14"  required>
                                            <small id="alert_subdivision" class="form-text text-danger"></small>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="col-sm-12 col-lg-6">
                            <div class="card">
                                <div class="card-header text-center">
                                    <h5>Referrals</h5>
                                </div>
                                <div class="card-body row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="optional">Referred By</label>
                                            <input name="referred_by" id="referred_by" type="text" class="form-control" placeholder="Enter referral here" >
                                            <small id="alert_referred_by" class="form-text text-danger"></small>
                                        </div>

                                        <div class="form-group">
                                            <label class="required">Source of Contact</label>
                                            <select name="source" id="source" class="form-control" required>
                                                <?php foreach (get_client_sources() as $key => $val ): ?>
                                                    <option value="<?= $key ?>"><?= $val ?></option>
                                                <?php endforeach; ?>
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