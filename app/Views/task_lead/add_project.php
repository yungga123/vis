<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Add Project</h1>
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
          <?= form_open("post-addproject",["id" => "post-addproject"]) ?>
          <!-- Main Card -->
          <div class="card">
            <div class="card-header">
              Fill-in the following
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Quarter</label>
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
                    <label>Status</label>
                    <select name="status" id="status" class="form-control">
                      <option value="">---Please Select---</option>
                      <option value="10">10%</option>
                      <option value="30">30%</option>
                      <option value="50">50%</option>
                      <option value="70">70%</option>
                      <option value="90">90%</option>
                      <option value="100">100%</option>
                    </select>
                    <small id="small_status" class="form-text text-muted"></small>
                  </div>
                  <div class="form-group">
                    <label>Customer</label>
                    <select class="form-control" name="customer_id" id="customer_id">
                      <option value="">--Please Select--</option>
                      <?php foreach ($customers as $row) { ?>
                        <option value="<?= $row['id'] ?>"><?= $row['customer_name'] ?></option>
                      <?php } ?>
                    </select>
                    <small id="small_customer_id" class="form-text text-muted"></small>
                  </div>
                  <div class="form-group">
                    <label>Project</label>
                    <input name="project" id="project" type="text" class="form-control" placeholder="Enter Project">
                    <small id="small_project" class="form-text text-muted"></small>
                  </div>
                  <div class="form-group">
                    <label>Project Amount</label>
                    <input name="project_amount" id="project_amount" type="text" class="form-control" placeholder="Input amount in numbers">
                    <small id="small_project_amount" class="form-text text-muted"></small>
                  </div>
                  <div class="form-group">
                    <label>Quotation Number</label>
                    <input name="quotation_num" id="quotation_num" type="text" class="form-control" placeholder="Enter quotation number">
                    <small id="small_quotation_num" class="form-text text-muted"></small>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Forecast Close Date</label>
                    <input name="forecast_close_date" id="forecast_close_date" type="date" class="form-control" placeholder="Input amount in numbers">
                    <small id="small_forecast_close_date" class="form-text text-muted"></small>
                  </div>
                  <div class="form-group">
                    <label>Remark Next Step</label>
                    <textarea name="remark_next_step" id="remark_next_step" type="text" class="form-control" placeholder="Enter here..."></textarea>
                    <small id="small_remark_next_step" class="form-text text-muted"></small>
                  </div>
                  <div class="form-group">
                    <label>Closed Deal Date</label>
                    <input name="close_deal_date" id="close_deal_date" type="date" class="form-control" placeholder="Input amount in numbers">
                    <small id="small_close_deal_date" class="form-text text-muted"></small>
                  </div>
                  <div class="form-group">
                    <label>Project Start Date</label>
                    <input name="project_start_date" id="project_start_date" type="date" class="form-control" placeholder="Input amount in numbers">
                    <small id="small_project_start_date" class="form-text text-muted"></small>
                  </div>
                  <div class="form-group">
                    <label>Project Finish Date</label>
                    <input name="project_finish_date" id="project_finish_date" type="date" class="form-control" placeholder="Input amount in numbers">
                    <small id="small_project_finish_date" class="form-text text-muted"></small>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-footer">
              <button type="submit" class="btn btn-success float-right">Submit</button>
            </div>
          </div>
          <?= form_close() ?>
        </div>
      </div>
    </div>
  </section>

</div>