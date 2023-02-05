<?= $this->extend('templates/default') ;?>

<?= $this->section('content') ;?>
<div class="container-fluid">
  <div class="card">
    <div class="card-body">

      <div class="row">
        <div class="col-sm-6">

          <div class="card">
            <div class="card-header text-center">
              <b>Customer Details</b>
            </div>
            <?php
            if ($uri->getSegment(1) == 'edit-customervt') {
              echo form_open('edit-customervt/' . $id, ["id" => "form-editcustomervt"]);
            } else {
              echo form_open('add-customervt', ["id" => "form-customervt"]);
            }; ?>

            <div class="card-body row">
              <div class="col-sm-12">
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
                  <input name="address_brgy" id="address_brgy" type="text" id="barangay" class="form-control" placeholder="Putatan" value="">
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

    <div class="card-footer">
      <a class="btn btn-secondary" href="javascript:history.back();">Go Back</a>
      <button type="submit" class="btn btn-success float-right">Submit</button>
      <?= form_close() ?>
    </div>
  </div>

</div>
<?= $this->endSection() ;?>

<?= $this->section('CustomScript') ;?>
  <?= ($uri->getSegment(1)=='add-customervt') ? $this->include('customers_vt/add_customervt_script') : "" ;?>
  <?= ($uri->getSegment(1)=='edit-customervt') ? $this->include('customers_vt/edit_customervt_script') : "" ;?>
<?= $this->endSection() ;?>
