<?= $this->extend('templates/default') ;?>

<?= $this->section('content') ;?>

<div class="card">
  <div class="card-header">
    <h3 class="card-title">Projects Detail</h3>

  </div>
  <div class="card-body">
    <div class="row">
      <div class="col-12 col-md-12 col-lg-8 order-2 order-md-1">
        <div class="row">
          <div class="col-12 col-sm-4">
            <div class="info-box bg-light">
              <div class="info-box-content">
                <span class="info-box-text text-center text-muted">Project Cost</span>
                <span class="info-box-number text-center text-muted mb-0"><?= $project_detail['project_amount']; ?></span>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-4">
            <div class="info-box bg-light">
              <div class="info-box-content">
                <span class="info-box-text text-center text-muted">Project Start</span>
                <span class="info-box-number text-center text-muted mb-0"><?= $project_detail['project_start_date']; ?></span>
              </div>
            </div>
          </div>
          <div class="col-12 col-sm-4">
            <div class="info-box bg-light">
              <div class="info-box-content">
                <span class="info-box-text text-center text-muted">Project Expected End</span>
                <span class="info-box-number text-center text-muted mb-0"><?= $project_detail['project_finish_date']; ?></span>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-12">
            <h4>Activity</h4>

            <?php foreach ($tasklead_history as $item) : ?>
              <div class="post">
                <h3>Status: <?= $item['status_percent']; ?> (<?= $item['status']; ?>)</h3>
                <p>
                  <?php
                  switch ($item['status']) {
                    case '10.00%':

                      echo "<p>Created On: " . $tasklead_data['created_at'] . "</p>";
                      echo "<p>Remark Next Step: " . $item['remark_next_step'] . "</p>";

                      break;

                    case '30.00%':
                      echo "<p>Project: " . $item['project'] . "</p>";
                      echo "<p>Remark Next Step: " . $item['remark_next_step'] . "</p>";
                      break;

                    case '50.00%':

                      echo "<p>Quotation Number: " . $item['quotation_num'] . "</p>";
                      echo "<p>Remark Next Step: " . $item['remark_next_step'] . "</p>";
                      echo "<p>Project Amount: " . $item['project_amount'] . "</p>";
                      echo "<p>Forecast Close Date: " . $item['forecast_close_date'] . "</p>";
                      echo "<p>Min Forecast Date: " . $item['min_forecast_date'] . "</p>";
                      echo "<p>Max Forecast Date: " . $item['max_forecast_date'] . "</p>";

                      break;

                    case '70.00%':

                      echo "<p>Remark Next Step: " . $item['remark_next_step'] . "</p>";

                      break;

                    case '90.00%':

                      echo "<p>Remark Next Step: " . $item['remark_next_step'] . "</p>";

                      break;

                    case '100.00%':

                      echo "<p>Remark Next Step: " . $item['remark_next_step'] . "</p>";
                      echo "<p>Forecast Close Date: " . $item['forecast_close_date'] . "</p>";
                      echo "<p>Project Date Start: " . $item['project_start_date'] . "</p>";
                      echo "<p>Project Date Finish: " . $item['project_finish_date'] . "</p>";
                      echo "<p>Project Duration: " . $item['project_duration'] . "</p>";
                      break;

                    default:
                      # code...
                      break;
                  }; ?>
                </p>
              </div>
            <?php endforeach ?>


          </div>

        </div>
      </div>
      <div class="col-12 col-md-12 col-lg-4 order-1 order-md-2">

        <br>
        <div class="text-muted">
          <p class="text-sm">Project
            <b class="d-block"><?= $project_detail['project']; ?></b>
          </p>

          <p class="text-sm">Client
            <b class="d-block"><?= $project_detail['customer_name']; ?></b>
          </p>

          <p class="text-sm">Branch
            <b class="d-block"><?= $project_detail['branch_name']; ?></b>
          </p>

          <p class="text-sm">Contact Number
            <b class="d-block"><?= $project_detail['contact_number']; ?></b>
          </p>
          <p class="text-sm">Quotation Number
            <b class="d-block"><?= $project_detail['quotation_num']; ?></b>
          </p>
          <p class="text-sm">Forecast Close date
            <b class="d-block"><?= $project_detail['forecast_close_date']; ?></b>
          </p>
          <p class="text-sm">Min Forecast Date
            <b class="d-block"><?= $project_detail['min_forecast_date']; ?></b>
          </p>
          <p class="text-sm">Max Forecast Date
            <b class="d-block"><?= $project_detail['max_forecast_date']; ?></b>
          </p>
          <p class="text-sm">Hit?
            <b class="d-block"><?= $project_detail['status1']; ?></b>
          </p>



          <p class="text-sm">Account Manager
            <b class="d-block"><?= $project_detail['employee_name']; ?></b>
          </p>

        </div>

        <h5 class="mt-5 text-muted">Project files</h5>
        <ul class="list-unstyled">

          <?php foreach ($map as $item) : ?>
            <li>
              <a href="<?= base_url('uploads/project-booked/' . $id . '/' . $item); ?>" class="btn-link text-secondary"><i class="far fa-fw fa-file-word"></i> <?= $item; ?></a>
            </li>
          <?php endforeach ?>

        </ul>
        <div class="text-center mt-5 mb-3">
          <a href="#" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modal-addfile">Add files</a>
          <?php foreach ($errors as $error) : ?>
            <li><?= esc($error) ?></li>
          <?php endforeach ?>
        </div>
      </div>
    </div>
  </div>
  <!-- /.card-body -->
</div>

<?= form_open_multipart('post-tasklead-upload/' . $project_detail['id']); ?>
<div class="modal fade" id="modal-addfile">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Add File</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <?= form_upload('project_file', '', ["id" => "project_file"]); ?>
      </div>
      <div class="modal-footer justify-content-between">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Submit</button>
      </div>
    </div>

  </div>
</div>
<?= form_close(); ?>

<?= $this->endSection() ;?>

<?= $this->section('CustomScript') ;?>
<?= $this->include('task_lead/script') ;?>
<?= $this->endSection() ;?>
