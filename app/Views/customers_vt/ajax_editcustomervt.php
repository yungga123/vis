<div class="modal fade" id="modal-edit-customervt">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Edit Customer</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row form">
                    <div class="col-sm-6">

                        <div class="card">
                            <div class="card-header text-center">
                                <b>Customer Details</b>
                            </div>
                            <?php echo form_open('', ["id" => "form-editcustomervt"]); ?>

                            <div class="card-body row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label>Customer Type</label>
                                        <select name="customer_type" id="customer_type" type="text" class="form-control">
                                            <option value="">---Please Select---</option>
                                            <option value="commercial">Commercial</option>
                                            <option value="residential">Residential</option>
                                        </select>
                                        <small id="small_customer_type" class="form-text text-muted"></small>
                                    </div>
                                    <div class="form-group">
                                        <label>Customer Name</label>
                                        <input name="customer_name" id="customer_name" type="text" class="form-control" placeholder="JC Amoranto / Vinculum Tech" value="">
                                        <small id="small_customer_name" class="form-text text-muted"></small>
                                    </div>
                                    <div class="form-group">
                                        <label>Contact Person</label>
                                        <input name="contact_person" id="contact_person" type="text" class="form-control" placeholder="Mr. JC" value="">
                                        <small id="small_contact_person" class="form-text text-muted"></small>
                                    </div>
                                    <div class="form-group">
                                        <label>Additional Notes</label>
                                        <input name="notes" id="notes" type="text" class="form-control" placeholder="Enter..." value="">
                                        <small id="small_notes" class="form-text text-muted"></small>
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
                                        <label>Contact Number</label>
                                        <input name="contact_number" id="contact_number" type="text" class="form-control" placeholder="0999XXXXXXX / 888X-XXXX" value="">
                                        <small id="small_contact_number" class="form-text text-muted"></small>
                                    </div>
                                    <div class="form-group">
                                        <label>Email Address</label>
                                        <input name="email_address" id="email_address" type="text" class="form-control" placeholder="email@example.com" value="">
                                        <small id="small_email_address" class="form-text text-muted"></small>
                                    </div>
                                    <div class="form-group">
                                        <label>Source of Contact</label>
                                        <input name="source" id="source" type="text" class="form-control" placeholder="Viber, FB, Telegram, Whatsapp, etc..." value="">
                                        <small id="small_source" class="form-text text-muted"></small>
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
                                        <label>Province</label>
                                        <input name="address_province" id="address_province" type="text" id="province" class="form-control" placeholder="NCR" value="">
                                        <small id="small_address_province" class="form-text text-muted"></small>
                                    </div>
                                    <div class="form-group">
                                        <label>City</label>
                                        <input name="address_city" id="address_city" type="text" id="city" class="form-control" placeholder="Muntinlupa City" value="">
                                        <small id="small_address_city" class="form-text text-muted"></small>
                                    </div>
                                    <div class="form-group">
                                        <label>Barangay</label>
                                        <input name="address_brgy" id="address_brgy" type="text" class="form-control" placeholder="Putatan" value="">
                                        <small id="small_address_brgy" class="form-text text-muted"></small>
                                    </div>
                                    <div class="form-group">
                                        <label>Detailed Address</label>
                                        <input name="address_sub" id="address_sub" type="text" class="form-control" placeholder="Soldier's Hills, Blk 35 Lot 14" value="">
                                        <small id="small_address_sub" class="form-text text-muted"></small>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
            <?= form_close() ;?>
        </div>
    </div>
</div>