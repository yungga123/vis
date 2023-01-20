<?= $this->extend('templates/default') ;?>

<?= $this->section('content') ;?>
<div class="row">

  <div class="col-lg-3 col-6">
    <!-- small box -->
    <div class="small-box bg-info">
      <div class="inner">
        <h3><?= $count_tasklead ?></h3>

        <p>Task/Leads Monitoring</p>
      </div>
      <div class="icon">
        <i class="fas fa-tasks"></i>
      </div>
      <a href="<?= site_url('tasklead') ?>" class="small-box-footer">Proceed <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>

  <div class="col-lg-3 col-6">
    <!-- small box -->
    <div class="small-box bg-success">
      <div class="inner">
        <h3>0</h3>

        <p>Manager of Sales</p>
      </div>
      <div class="icon">
        <i class="fas fa-chart-bar"></i>
      </div>
      <a href="<?= site_url('manager-of-sales') ?>" class="small-box-footer">Proceed <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>

  <div class="col-lg-3 col-6">
    <!-- small box -->
    <div class="small-box bg-warning">
      <div class="inner">
        <h3>0</h3>

        <p>Add Customer (Forecast)</p>
      </div>
      <div class="icon">
        <i class="fas fa-user-plus"></i>
      </div>
      <a href="<?= site_url('add-customer') ?>" class="small-box-footer">Proceed <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>


  <div class="col-lg-3 col-6">
    <!-- small box -->
    <div class="small-box bg-danger">
      <div class="inner">
        <h3>0</h3>

        <p>Add Customer Branch (Forecast)</p>
      </div>
      <div class="icon">
        <i class="fas fa-user-plus"></i>
      </div>
      <a href="<?= site_url('add-customer-branch') ?>" class="small-box-footer">Proceed <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>



</div>

<div class="row">

  <div class="col-lg-3 col-6">
    <!-- small box -->
    <div class="small-box bg-secondary">
      <div class="inner">
        <h3>0</h3>

        <p>Customer List (Forecast)</p>
      </div>
      <div class="icon">
        <i class="fas fa-users"></i>
      </div>
      <a href="<?= site_url('customers-list') ?>" class="small-box-footer">Proceed <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>


</div>
<?= $this->endSection() ;?>

<?= $this->section('CustomScript') ;?>
  <?= $this->include('sales_dashboard/script') ;?>
<?= $this->endSection() ;?>
