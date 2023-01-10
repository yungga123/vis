<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"><?= $page_title ?></h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body">
                    <?= form_open('add-customerbranch',["id" => "form-addcustomerbranch"]) ?>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="card">
                                <div class="card-header"><b>Customer Details</b></div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="customer_id">Main Customer</label>
                                        <select class="form-control" name="customer_id" id="customer_id">
                                            <option value="">---Please Select---</option>
                                            <?php foreach ($customers as $customer) : ?>
                                                <option value="<?= $customer['id'] ?>"><?= $customer['customer_name'] ?></option>
                                            <?php endforeach ?>
                                        </select>
                                        <small id="small_customer_id" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="branch_name">Branch Name</label>
                                        <input type="text" class="form-control" name="branch_name" id="branch_name" placeholder="Enter here...">
                                        <small id="small_branch_name" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="contact_person">Contact Person</label>
                                        <input type="text" class="form-control" name="contact_person" id="contact_person" placeholder="Enter here...">
                                        <small id="small_contact_person" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="contact_number">Contact Number</label>
                                        <input type="text" class="form-control" name="contact_number" id="contact_number" placeholder="Enter here...">
                                        <small id="small_contact_number" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="email_address">Email Address</label>
                                        <input type="text" class="form-control" name="email_address" id="email_address" placeholder="Enter here...">
                                        <small id="small_email_address" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="notes">Notes</label>
                                        <input type="text" class="form-control" name="notes" id="notes" placeholder="Enter here...">
                                        <small id="small_notes" class="form-text text-muted"></small>
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
                                        <label for="address_province">Province</label>
                                        <input type="text" class="form-control" name="address_province" id="address_province" placeholder="Enter here...">
                                        <small id="small_address_province" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="address_city">City</label>
                                        <input type="text" class="form-control" name="address_city" id="address_city" placeholder="Enter here...">
                                        <small id="small_address_city" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="address_brgy">Barangay</label>
                                        <input type="text" class="form-control" name="address_brgy" id="address_brgy" placeholder="Enter here...">
                                        <small id="small_address_brgy" class="form-text text-muted"></small>
                                    </div>

                                    <div class="form-group">
                                        <label for="address_sub">Sub Address</label>
                                        <input type="text" class="form-control" name="address_sub" id="address_sub" placeholder="Enter here...">
                                        <small id="small_address_sub" class="form-text text-muted"></small>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-success float-right">Submit</button>
                </div>
                <?= form_close() ?>
            </div>

        </div>
    </section>

</div>