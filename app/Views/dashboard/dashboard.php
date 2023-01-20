<?= $this->extend('templates/default'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
  <div class="row">
    <div class="col-lg-3 col-6">
      <!-- small card -->
      <div class="small-box bg-info">
        <div class="inner">
          <h4>Sales Dashboard</h4>

          <p>Click Proceed</p>
        </div>
        <div class="icon">
          <i class="fas fa-shopping-cart"></i>
        </div>
        <a href="<?= site_url('sales-dashboard') ?>" class="small-box-footer">
          Proceed <i class="fas fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
      <!-- small card -->
      <div class="small-box bg-success">
        <div class="inner">
          <h4>Admin Dashboard</h4>

          <p>Click Proceed</p>
        </div>
        <div class="icon">
          <i class="ion ion-stats-bars"></i>
        </div>
        <a href="<?= site_url('admin-dashboard') ?>" class="small-box-footer">
          Proceed <i class="fas fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
      <!-- small card -->
      <div class="small-box bg-warning">
        <div class="inner">
          <h4>HR Dashboard</h4>

          <p>Click Proceed</p>
        </div>
        <div class="icon">
          <i class="fas fa-user-plus"></i>
        </div>
        <a href="<?= site_url('employee-menu') ?>" class="small-box-footer">
          Proceed <i class="fas fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
      <!-- small card -->
      <div class="small-box bg-danger">
        <div class="inner">
          <h4>Executive Overview</h4>

          <p>Click Proceed</p>
        </div>
        <div class="icon">
          <i class="fas fa-chart-pie"></i>
        </div>
        <a href="<?= site_url('executive-overview') ?>" class="small-box-footer">
          Proceed <i class="fas fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>
    <!-- ./col -->
  </div>

  <div class="row">
    <div class="col-lg-3 col-6">
      <!-- small card -->
      <div class="small-box bg-secondary">
        <div class="inner">
          <h4>Customers</h4>

          <p>Click Proceed</p>
        </div>
        <div class="icon">
          <i class="fas fa-users"></i>
        </div>
        <a href="<?= site_url('customersvt_menu') ?>" class="small-box-footer">
          Proceed <i class="fas fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>
    <!-- ./col -->
  </div>
</div>


<?= $this->endSection(); ?>

<?= $this->section('CustomScript'); ?>
<?= $this->include('dashboard/script'); ?>
<?= $this->endSection(); ?>