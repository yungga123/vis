<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Project List</h1>
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
                <div class="col-md-12">
                    <!-- Main Card -->
                    <div class="card">
                        <div class="card-body">
                            <table id="project_list_table" class="table table-bordered table-striped nowrap">
                                <thead>
                                    <tr>
                                        <th>Project ID</th>
                                        <th>Action</th>
                                        <th>Forecast By</th>
                                        <th>Quarter</th>
                                        <th>Percent</th>
                                        <th>Status</th>
                                        <th>Customer</th>
                                        <th>Contact Number</th>
                                        <th>Project</th>
                                        <th>Amount</th>
                                        <th>Quotation Number</th>
                                        <th>Forecast Close Date</th>
                                        <th>Min Forecast Date</th>
                                        <th>Max Forecast Date</th>
                                        <th>Hit</th>
                                        <th>Remark Next Step</th>
                                        <th>Closed Deal Date</th>
                                        <th>Project Date Start</th>
                                        <th>Project Date Finish</th>
                                        <th>Project Duration</th>
                                    </tr>
                                </thead>

                                <tfoot>
                                    <tr>
                                        <th>Project ID</th>
                                        <th>Action</th>
                                        <th>Forecast By</th>
                                        <th>Quarter</th>
                                        <th>Percent</th>
                                        <th>Status</th>
                                        <th>Customer</th>
                                        <th>Contact Number</th>
                                        <th>Project</th>
                                        <th>Amount</th>
                                        <th>Quotation Number</th>
                                        <th>Forecast Close Date</th>
                                        <th>Min Forecast Date</th>
                                        <th>Max Forecast Date</th>
                                        <th>Hit</th>
                                        <th>Remark Next Step</th>
                                        <th>Closed Deal Date</th>
                                        <th>Project Date Start</th>
                                        <th>Project Date Finish</th>
                                        <th>Project Duration</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer">
                      <div class="float-right">
                        <a href="<?= site_url('sales-dashboard') ?>" class="btn btn-success"><i class="fas fa-table"></i> Sales Dashboard</a>
                        <a href="<?= site_url('tasklead') ?>" class="btn btn-secondary"><i class="fas fa-undo-alt"></i> Task Lead Menu</a>
                      </div>
                      
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>


<div class="modal fade" id="modal-delete-tasklead">
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
        <a href="button" class="btn btn-success href-tasklead"><i class="fas fa-check"></i> YES</a>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->

<div class="modal fade" id="modal-update-tasklead">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-body">
        <a href="#" class="btn btn-danger btn-lg btn-block href-identified"><i class="fas fa-search"></i> Identified</a>
        <a href="#" class="btn btn-secondary btn-lg btn-block href-qualified"><i class="fas fa-door-open"></i> Qualified</a>
        <a href="#" class="btn btn-warning btn-lg btn-block href-developed"><i class="fas fa-lightbulb"></i> Developed Solution</a>
        <a href="#" class="btn btn-info btn-lg btn-block href-evaluation"><i class="fas fa-calculator"></i> Evaluation</a>
        <a href="#" class="btn btn-primary btn-lg btn-block href-negotiation"><i class="fas fa-handshake"></i> Negotiation</a>
        <a href="#" class="btn btn-success btn-lg btn-block href-booked"><i class="fas fa-calendar-check"></i> Booked</a>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->