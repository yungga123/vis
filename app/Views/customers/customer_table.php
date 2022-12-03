<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">List of Customers</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Customer List</h3>
            </div>

            <div class="card-body">
              <table id="myTable" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Customer ID</th>
                    <th>Action</th>
                    <th>Customer Name</th>
                    <th>Contact Person</th>
                    <th>Address</th>
                    <th>Email Address</th>
                    <th>Contact Number</th>
                    <th>Source</th>
                    <th>Notes</th>
                  </tr>
                </thead>

                <tfoot>
                  <tr>
                    <th>Customer ID</th>
                    <th>Action</th>
                    <th>Customer Name</th>
                    <th>Contact Person</th>
                    <th>Address</th>
                    <th>Email Address</th>
                    <th>Contact Number</th>
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
  </section>

</div>

<div class="modal fade" id="modal-delete-customer">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Delete Data</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Confirm to delete.</p>
        <input type="hidden" name="modal_customer_id" id="modal_customer_id">
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times"></i> NO</button>
        <button type="button" class="btn btn-success"><i class="fas fa-check"></i> YES</button>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->