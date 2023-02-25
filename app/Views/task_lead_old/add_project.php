<?= $this->extend('templates/default') ;?>


<?= $this->section('content') ;?>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">

      <?php
      switch ($uri->getSegment(1)) {
        case 'tasklead-addproject':
          echo form_open("post-addproject", ["id" => "post-addproject"]);
          break;
        case 'tasklead-editproject':
          echo form_open("post-editproject", ["id" => "post-editproject"]);
          echo "<input type='hidden' name='id' id='id' value='" . $id . "'>";
          break;
        case 'add-project-existingcustomer':
          echo form_open("add-project-existingcustomer", ["id" => "post-addproject"]);

          break;

        default:
          # code...
          break;
      }
      ?>

      <input type="hidden" name="employee_id" id="employee_id" value="<?= $_SESSION['employee_id'] ?>">
      <!-- Main Card -->
      <div class="card">
        <div class="card-header">
          Fill-in the following
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="quarter">Quarter</label>
                <select class="form-control" name="quarter" id="quarter">
                  <option value="">--Please Select--</option>
                  <option value="1">1st Quarter</option>
                  <option value="2">2nd Quarter</option>
                  <option value="3">3rd Quarter</option>
                  <option value="4">4th Quarter</option>
                </select>
                <small id="small_quarter" class="form-text text-muted"></small>
              </div>
              <div class="form-group">
                <label for="status">Status</label>
                <select name="status" id="status" class="form-control">
                  <option value="10.00">10% - Identified</option>
                  <option value="30.00">30% - Qualified</option>
                  <option value="50.00">50% - Developed Solution</option>
                  <option value="70.00">70% - Evaluation</option>
                  <option value="90.00">90% - Negotiation</option>
                </select>
                <small id="small_status" class="form-text text-muted"></small>
              </div>
              <div class="form-group">
                <label for="customer_id">Customer</label>
                <select class="form-control" name="customer_id" id="customer_id">
                  <option value="">--Please Select--</option>
                  <?php foreach ($customers as $row) { ?>
                    <option value="<?= $row['id'] ?>">
                      <?= $row['customer_name'] ?>
                    </option>
                  <?php } ?>
                </select>
                <small id="small_customer_id" class="form-text text-muted"></small>
              </div>

              <div class="form-group">
                <label for="branch_id">Branch</label>
                <select class="form-control" name="branch_id" id="branch_id">
                  <option value="">--Please Select--</option>
                  <?php if ($customers_branch) : ?>
                    <?php foreach ($customers_branch as $row) { ?>
                      <option value="<?= $row['id'] ?>">
                        <?= $row['branch_name'] ?>
                      </option>
                    <?php } ?>
                  <?php endif ?>

                </select>
                <small id="small_branch_id" class="form-text text-muted"></small>
              </div>

              <div class="form-group">
                <label for="project">Project</label>
                <input name="project" id="project" type="text" class="form-control" placeholder="Enter Project">
                <small id="small_project" class="form-text text-muted"></small>
              </div>
              <div class="form-group">
                <label for="project_amount">Project Amount</label>
                <input name="project_amount" id="project_amount" type="text" class="form-control" placeholder="Input amount in numbers">
                <small id="small_project_amount" class="form-text text-muted"></small>
              </div>
              <div class="form-group">
                <label for="quotation_num">Quotation Number</label>
                <input name="quotation_num" id="quotation_num" type="text" class="form-control" placeholder="Do not put anything here, this is system generated after developed solution" readonly>
                <small id="small_quotation_num" class="form-text text-muted"></small>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="forecast_close_date">Forecast Close Date</label>
                <input name="forecast_close_date" id="forecast_close_date" type="date" class="form-control" placeholder="Input amount in numbers">
                <small id="small_forecast_close_date" class="form-text text-muted"></small>
              </div>
              <div class="form-group">
                <label for="min_forecast_date">Min. Forecast Date</label>
                <input name="min_forecast_date" id="min_forecast_date" type="date" class="form-control" placeholder="Input amount in numbers">
                <small id="small_min_forecast_date" class="form-text text-muted"></small>
              </div>
              <div class="form-group">
                <label for="max_forecast_date">Max Forecast Date</label>
                <input name="max_forecast_date" id="max_forecast_date" type="date" class="form-control" placeholder="Input amount in numbers">
                <small id="small_max_forecast_date" class="form-text text-muted"></small>
              </div>
              <div class="form-group">
                <label for="remark_next_step">Remark Next Step</label>
                <textarea name="remark_next_step" id="remark_next_step" type="text" class="form-control" placeholder="Enter here..."></textarea>
                <small id="small_remark_next_step" class="form-text text-muted"></small>
              </div>
              <div class="form-group">
                <label for="close_deal_date">Closed Deal Date</label>
                <input name="close_deal_date" id="close_deal_date" type="date" class="form-control" placeholder="Input amount in numbers">
                <small id="small_close_deal_date" class="form-text text-muted"></small>
              </div>
              <div class="form-group">
                <label for="project_start_date">Project Start Date</label>
                <input name="project_start_date" id="project_start_date" type="date" class="form-control" placeholder="Input amount in numbers">
                <small id="small_project_start_date" class="form-text text-muted"></small>
              </div>
              <div class="form-group">
                <label for="project_finish_date">Project Finish Date</label>
                <input name="project_finish_date" id="project_finish_date" type="date" class="form-control" placeholder="Input amount in numbers">
                <small id="small_project_finish_date" class="form-text text-muted"></small>
              </div>
            </div>
          </div>
        </div>
        <div class="card-footer">
          <a href="<?= site_url('sales-dashboard') ?>" class="btn btn-success"><i class="fas fa-table"></i> Sales Dashboard</a>
          <a href="<?= site_url('tasklead') ?>" class="btn btn-secondary"><i class="fas fa-undo-alt"></i> Task Lead Menu</a>
          <button type="submit" class="btn btn-success float-right"><i class="fas fa-check"></i> Submit</button>
        </div>
      </div>
      <?= form_close() ?>
    </div>
  </div>
</div>
<?= $this->endSection() ;?>

<?= $this->section('CustomScript') ;?>
  <?= ($uri->getSegment(1)=='tasklead-addproject') ? $this->include('task_lead/add_project_script') : "" ;?>
  <?= ($uri->getSegment(1)=='add-project-existingcustomer') ? $this->include('task_lead/add_project_script') : "" ;?>
  <?= ($uri->getSegment(1)=='add-project-existingcustomer') ? $this->include('task_lead/addProjectExistingCustomerScript') : "" ;?>
                      

  <?= $this->include('task_lead/script') ;?>
<?= $this->endSection() ;?>
