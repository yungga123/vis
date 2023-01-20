<?= $this->extend('templates/default') ;?>


<?= $this->section('content') ;?>
<div class="container-fluid">

  <div class="row">
    <div class="col-lg-3 col-6">
      <!-- small card -->
      <div class="small-box bg-info">
        <div class="inner">
          <h4>Add Customer</h4>

          <p>Click Proceed</p>
        </div>
        <div class="icon">
          <i class="fas fa-user-plus"></i>
        </div>
        <a href="<?= site_url('add-customervt') ?>" class="small-box-footer">
          Proceed <i class="fas fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
      <!-- small card -->
      <div class="small-box bg-success">
        <div class="inner">
          <h4>Add Customer Branch</h4>

          <p>Click Proceed</p>
        </div>
        <div class="icon">
          <i class="fas fa-user-plus"></i>
        </div>
        <a href="<?= site_url("add_customervtbranch") ?>" class="small-box-footer">
          Proceed <i class="fas fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
      <!-- small card -->
      <div class="small-box bg-warning">
        <div class="inner">
          <h4>Customers List</h4>

          <p>Click Proceed</p>
        </div>
        <div class="icon">
          <i class="fas fa-list"></i>
        </div>
        <a href="<?= site_url('customervt-list'); ?>" class="small-box-footer">
          Proceed <i class="fas fa-arrow-circle-right"></i>
        </a>
      </div>
    </div>
    <!-- ./col -->
  </div>

</div>
<?= $this->endSection() ;?>

<?= $this->section('CustomScript') ;?>
<?= $this->include('customers_vt/script') ;?>
<?= $this->endSection() ;?>
