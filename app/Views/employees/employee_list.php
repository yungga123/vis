<?= $this->extend('templates/default') ;?>


<?= $this->section('content') ;?>
<div class="container-fluid">
  <div class="card">
    <div class="card-body">
      <table id="employee_table" class="table table-bordered table-striped nowrap" data-url="<?= url_to('employees') ?>">
        <thead>
          <tr>
            <th>Employee ID</th>
            <th>Action</th>
            <th>Employee Name</th>
            <th>Address</th>
            <th>Gender</th>
            <th>Civil Status</th>
            <th>Birthdate</th>
            <th>Birthplace</th>
            <th>Position</th>
            <th>Employment Status</th>
            <th>Date Hired</th>
            <th>Language</th>
            <th>Contact Number</th>
            <th>Email Address</th>
            <th>SSS Number</th>
            <th>TIN Number</th>
            <th>PhilHealth Number</th>
            <th>PAGIBIG Number</th>
            <th>Educational Attainment</th>
            <th>Course</th>
          </tr>
        </thead>

        <tfoot>
          <tr>
            <th>Employee ID</th>
            <th>Action</th>
            <th>Employee Name</th>
            <th>Address</th>
            <th>Gender</th>
            <th>Civil Status</th>
            <th>Birthdate</th>
            <th>Birthplace</th>
            <th>Position</th>
            <th>Employment Status</th>
            <th>Date Hired</th>
            <th>Language</th>
            <th>Contact Number</th>
            <th>Email Address</th>
            <th>SSS Number</th>
            <th>TIN Number</th>
            <th>PhilHealth Number</th>
            <th>PAGIBIG Number</th>
            <th>Educational Attainment</th>
            <th>Course</th>
          </tr>
        </tfoot>
      </table>
    </div>
    <div class="card-footer">
      <a href="<?= site_url('employee-menu') ?>" class="btn btn-secondary"><i class="fas fa-undo"></i> Go to Employee Menu</a>
    </div>
  </div>
</div>

<div class="modal fade" id="modal-delete-employee">
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
        <a href="#" class="btn btn-success href-employee"><i class="fas fa-check"></i> YES</a>
      </div>
    </div>
    <!-- /.modal-content -->
  </div>
  <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<?= $this->endSection() ;?>