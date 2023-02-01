<?= $this->extend('templates/default'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">

          <table id="customer_vt" class="table table-bordered table-striped nowrap" data-url="<?=isset($uri) ? site_url('customervt_dttable?'.$uri->getQuery()) : "" ?>">
            <thead>
              <tr>
                <th>Customer ID</th>
                <th>Action</th>
                <th>Customer Type</th>
                <th>Customer Name</th>
                <th>Contact Person</th>
                <th>Address</th>
                <th>Contact Number</th>
                <th>Email Address</th>
                <th>Source</th>
                <th>Notes</th>
              </tr>
            </thead>
            <tfoot>
              <tr>
                <th>Customer ID</th>
                <th>Action</th>
                <th>Customer Type</th>
                <th>Customer Name</th>
                <th>Contact Person</th>
                <th>Address</th>
                <th>Contact Number</th>
                <th>Email Address</th>
                <th>Source</th>
                <th>Notes</th>
              </tr>
            </tfoot>
          </table>

        </div>
      </div>
    </div>
  </div>
</div>

<?= $this->include('customers_vt/ajax_editcustomervt') ?>
<?= $this->endSection(); ?>