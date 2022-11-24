<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Add Customer</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <section class="content">
    <div class="container-fluid">
      <?= form_open("add-customers",["id" => "form-addcustomer"]) ?>
      <div class="card">
        <div class="card-body">

          <div class="row">
            <div class="col-sm-6">

              <div class="card">
                <div class="card-header text-center">
                  <b>Customer Details</b>
                </div>

                <div class="card-body row">
                  <div class="col-sm-12">
                    <div class="form-group">
                      <label>Customer Name</label>
                      <input name="customer_name" id="customer_name" type="text" class="form-control" placeholder="JC Amoranto / Vinculum Tech">
                      <small id="small_customer_name" class="form-text text-muted"></small>
                    </div>
                    <div class="form-group">
                      <label>Contact Person</label>
                      <input name="contact_person" id="contact_person" type="text" class="form-control" placeholder="Mr. JC">
                      <small id="small_contact_person" class="form-text text-muted"></small>
                    </div>
                    <div class="form-group">
                      <label>Additional Notes</label>
                      <input name="notes" id="notes" type="text" class="form-control" placeholder="Enter...">
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
                      <input name="contact_number" id="contact_number" type="text" class="form-control" placeholder="0999XXXXXXX / 888X-XXXX">
                      <small id="small_contact_number" class="form-text text-muted"></small>
                    </div>
                    <div class="form-group">
                      <label>Email Address</label>
                      <input name="email_address" id="email_address" type="text" class="form-control" placeholder="email@example.com">
                      <small id="small_email_address" class="form-text text-muted"></small>
                    </div>
                    <div class="form-group">
                      <label>Source of Contact</label>
                      <input name="source" id="source" type="text" class="form-control" placeholder="Viber, FB, Telegram, Whatsapp, etc...">
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
                      <input name="address_province" id="address_province" type="text" id="province" class="form-control" placeholder="NCR">
                      <small id="small_address_province" class="form-text text-muted"></small>
                    </div>
                    <div class="form-group">
                      <label>City</label>
                      <input name="address_city" id="address_city" type="text" id="city" class="form-control" placeholder="Muntinlupa City">
                      <small id="small_address_city" class="form-text text-muted"></small>
                    </div>
                    <div class="form-group">
                      <label>Barangay</label>
                      <input name="address_brgy" id="address_brgy" type="text" id="barangay" class="form-control" placeholder="Putatan">
                      <small id="small_address_brgy" class="form-text text-muted"></small>
                    </div>
                    <div class="form-group">
                      <label>Detailed Address</label>
                      <input name="address_sub" id="address_sub" type="text" class="form-control" placeholder="Soldier's Hills, Blk 35 Lot 14">
                      <small id="small_address_sub" class="form-text text-muted"></small>
                    </div>
                  </div>

                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="card-footer">
          <button type="submit" class="btn btn-primary">Submit</button>
        </div>
      </div>
      <?= form_close() ?>

    </div>
  </section>

</div>