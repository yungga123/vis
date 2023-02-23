<?= $this->extend('templates/default') ;?>


<?= $this->section('content') ;?>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
      <!-- Main Card -->
      <div class="card">
        <div class="card-header">

          <h3 class="card-title">Project Booked List</h3>
          <div class="card-tools">
            <form method='get' action="customervt-list" id="searchForm">

              <div class="input-group input-group-sm" style="width: 150px;">
                <input type="text" name="search" class="form-control float-right" value='<?= $search ?>' placeholder="Search">
                <div class="input-group-append">
                  <button type="submit" class="btn btn-default" id='btnsearch' onclick='document.getElementById("searchForm").submit();'>
                    <i class="fas fa-search"></i>
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
        <div class="card-body p-0">
          <!-- <table id="project_list_table_booked" class="table table-bordered table-striped nowrap">
                                <thead>
                                    <tr>
                                        <th>Project ID</th>
                                        <th>Action</th>
                                        <th>Account Manager</th>
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
                                        <th>Account Manager</th>
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
                            </table> -->

          <table class="table table-striped projects">
            <thead>
              <tr>
                <th style="width: 1%">
                  #
                </th>
                <th style="width: 20%">
                  Account Manager
                </th>
                <th style="width: 30%">
                  Customer
                </th>
                <th style="width: 15%;">
                  Project Progress
                </th>
                <th style="width: 10%;">

                </th>
              </tr>

            </thead>

            <tbody>

              <?php foreach ($booked_projects as $item) : ?>

                <tr>
                  <td><?= $item['id'] ?></td>
                  <td>
                    <?= $item['employee_name'] ?>
                    <p><small class="text-muted">Closed Deal: <?= $item['close_deal_date'] ?></small></p>
                  </td>
                  <td>
                    <?= $item['customer_name'] ?>
                    <p><small class="text-muted">Branch: <?= ($item['branch_name'] == '' ? "<i class='text-danger'>Not Set</i>" : $item['branch_name']) ?></small></p>
                    <p><small class="text-muted">Project: <?= $item['project'] ?></small></p>
                  </td>
                  <td><span class="badge badge-success"><?= $item['status_percent'] ?></span></td>
                  <td class="project-actions">
                    <a class="btn btn-primary btn-sm" href="<?= site_url('project-booked-details/') . $item['id']; ?>">
                      <i class="fas fa-folder">
                      </i>
                      View
                    </a>
                  </td>
                </tr>
              <?php endforeach ?>

            </tbody>
          </table>

        </div>

        <div class="card-footer">
          <?= $pager->makeLinks($page, $perPage, $total, 'custom_view') ?>
          <div class="float-right">
            <a href="<?= site_url('sales-dashboard') ?>" class="btn btn-success"><i class="fas fa-table"></i> Sales Dashboard</a>
            <a href="<?= site_url('tasklead') ?>" class="btn btn-secondary"><i class="fas fa-undo-alt"></i> Task Lead Menu</a>
          </div>

        </div>
      </div>


    </div>
  </div>
</div>
<?= $this->endSection() ;?>

<?= $this->section('CustomScript') ;?>
<?= $this->include('task_lead/script') ;?>
<?= $this->endSection() ;?>
