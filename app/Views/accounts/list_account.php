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
                    <table id="accounts_table" class="table table-bordered table-striped nowrap">
                        <thead>
                            <tr>
                                <th>Employee ID</th>
                                <th>Action</th>
                                <th>Employee Name</th>
                                <th>Username</th>
                                <th>Password</th>
                                <th>Access Level</th>
                            </tr>
                        </thead>

                        <tfoot>
                            <tr>
                                <th>Employee ID</th>
                                <th>Action</th>
                                <th>Employee Name</th>
                                <th>Username</th>
                                <th>Password</th>
                                <th>Access Level</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="card-footer">
                    <a href="<?= site_url('employee-menu') ;?>" class="btn btn-secondary float-right"><i class="fas fa-undo"></i> RETURN TO MENU</a>
                </div>
            </div>

        </div>
    </section>

</div>


<div class="modal fade" id="modal-delete-account">
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
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="fas fa-times"></i> NO</button>
        <a href="#" class="btn btn-success href-account"><i class="fas fa-check"></i> YES</a>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->