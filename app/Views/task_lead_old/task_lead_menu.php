<?= $this->extend('templates/default'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">

  <div class="card">
    <div class="card-header">
      <h3 class="card-title">Manage Forecast</h3>
    </div>
    <div class="card-body">
      <div class="row">

        <div class="col-lg-3 col-6">
          <!-- small box -->
          <div class="small-box bg-success">
            <div class="inner">
              <h4>Add Project</h4>


              <p>Make sure customer is added.</p>
            </div>
            <div class="icon">
              <i class="fas fa-tasks"></i>
            </div>
            <!-- <a href="</?= site_url('tasklead-addproject') ?>" class="small-box-footer">Proceed <i class="fas fa-arrow-circle-right"></i></a> -->
            <!-- <a href="</?= site_url('tasklead-addproject') ?>" class="small-box-footer">Proceed <i class="fas fa-arrow-circle-right"></i></a> -->
            <a href="javascript:void(0)" class="small-box-footer" data-toggle="modal" data-target="#modalCustomerSelect">Proceed <i class="fas fa-arrow-circle-right"></i></a>
          </div>

        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-warning">
            <div class="inner">
              <h4>Booked Projects</h4>

              <p>View booked projects.</p>
            </div>
            <div class="icon">
              <i class="fas fa-tasks"></i>
            </div>
            <a href="<?= site_url('project-list-booked'); ?>" class="small-box-footer">Proceed <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-secondary">
            <div class="inner">
              <h4>Booked Projects (Consolidated)</h4>

              <p>View booked projects.</p>
            </div>
            <div class="icon">
              <i class="fas fa-tasks"></i>
            </div>
            <a href="<?= site_url('manager-project-list-booked'); ?>" class="small-box-footer">Proceed <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-lg-3 col-6">
          <div class="small-box bg-success">
            <div class="inner">
              <h4>Add Customer Forecast</h4>


              <p>Forecast Customers</p>
            </div>
            <div class="icon">
              <i class="fas fa-tasks"></i>
            </div>
            <a href="<?= site_url('add-customer') ?>" class="small-box-footer">Proceed <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-warning">
            <div class="inner">
              <h4>Add Customer Branch</h4>


              <p>Forecast Customers</p>
            </div>
            <div class="icon">
              <i class="fas fa-tasks"></i>
            </div>
            <a href="<?= site_url('add-customer-branch') ?>" class="small-box-footer">Proceed <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

        <div class="col-lg-3 col-6">
          <div class="small-box bg-secondary">
            <div class="inner">
              <h4>Forecast Customer List</h4>


              <p>View your projects here.</p>
            </div>
            <div class="icon">
              <i class="fas fa-tasks"></i>
            </div>
            <a href="<?= site_url('customers-list') ?>" class="small-box-footer">Proceed <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>

      </div>


    </div>

  </div>
</div>


<div class="card">
  <div class="card-header">
    <h3 class="card-title">For New Customers(Forecast)</h3>
  </div>
  <div class="card-body">
    <div class="row">



      <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
          <div class="inner">
            <h4>Project List</h4>


            <p>View your projects here.</p>
          </div>
          <div class="icon">
            <i class="fas fa-tasks"></i>
          </div>
          <a href="<?= site_url('project-list') ?>" class="small-box-footer">Proceed <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>

      <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
          <div class="inner">
            <h4>Project List (Consolidated)</h4>


            <p>Only Managers have access.</p>
          </div>
          <div class="icon">
            <i class="fas fa-tasks"></i>
          </div>
          <a href="<?= site_url('manager-project-list'); ?>" class="small-box-footer">Proceed <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>


    </div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h3 class="card-title">For Existing Customers(Old/Official Vinculum Customers)</h3>
  </div>
  <div class="card-body">
    <div class="row">

      <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
          <div class="inner">
            <h4>Project List</h4>


            <p>View your projects here.</p>
          </div>
          <div class="icon">
            <i class="fas fa-tasks"></i>
          </div>
          <a href="<?= site_url('project-list?existing_customer=1') ?>" class="small-box-footer">Proceed <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>

      <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
          <div class="inner">
            <h4>Project List (Consolidated)</h4>


            <p>Only Managers have access.</p>
          </div>
          <div class="icon">
            <i class="fas fa-tasks"></i>
          </div>
          <a href="<?= site_url('manager-project-list?existing_customer=1'); ?>" class="small-box-footer">Proceed <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>

    </div>
  </div>
</div>


</div>




<!-- Modal -->
<div class="modal fade" id="modalCustomerSelect" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Select Customer</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <?= form_open('tasklead-addproject', ["method" => "get"]) ?>
        <div class="form-group">
          <label for="forecast_custmer">Select Customer (Forecast)</label>
          <select class="form-control" name="forecast_custmer" id="forecast_custmer" required>
            <option value="">---Please Select---</option>
            <?php foreach ($customers as $item) : ?>
              <option value="<?= $item['id'] ?>"><?= $item['customer_name'] ?></option>
            <?php endforeach ?>
          </select>
        </div>

        <button type="submit" class="btn btn-success">Select</button>
        <?= form_close() ?>

        <br>
        <br>
        <br>

        <?= form_open("add-project-existingcustomer", ["method" => "get"]) ?>
        <div class="form-group">
          <label for="existing_customer">Select Customer (Existing)</label>
          <select class="form-control" name="existing_customer" id="existing_customer" required>
            <option value="">---Please Select----</option>
            <?php foreach ($customersVt as $item) : ?>
              <option value="<?= $item['id'] ?>"><?= $item['customer_name'] ?></option>
            <?php endforeach ?>
          </select>
        </div>
        <button type="submit" class="btn btn-success">Select</button>
        <?= form_close() ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('CustomScript'); ?>
<?= $this->include('task_lead/script'); ?>
<?= $this->endSection(); ?>