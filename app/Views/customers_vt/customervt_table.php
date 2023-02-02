<?= $this->extend('templates/default'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">

          <table id="customer_vt" class="table table-bordered table-striped nowrap" data-url="<?= isset($uri) ? site_url('customervt_dttable?' . $uri->getQuery()) : "" ?>">
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

<div class="modal fade" id="modal-delete-customervt">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Are you sure you want to continue?</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times"></i> NO</button>
        <button type="button" class="btn btn-success" id="btn-delete-customervt" data-url="<?= site_url('delete-customervt') ?>"><i class="fas fa-check"></i> YES</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<?= $this->include('customers_vt/ajax_editcustomervt') ?>
<?= $this->endSection(); ?>