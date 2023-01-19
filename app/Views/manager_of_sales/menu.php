<?= $this->extend('templates/default') ;?>


<?= $this->section('content') ?>
<div class="row">
  <div class="col-lg-3 col-6">
    <!-- small box -->
    <div class="small-box bg-success">
      <div class="inner">
        <h3>999</h3>

        <p>Consolidated Sales Forecast</p>
      </div>
      <div class="icon">
        <i class="fas fa-tasks"></i>
      </div>
      <a href="<?= site_url('consolidated-sales-forecast'); ?>" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <div class="col-lg-3 col-6">
    <!-- small box -->
    <div class="small-box bg-warning">
      <div class="inner">
        <h3>999</h3>

        <p>Over-all Stats</p>
      </div>
      <div class="icon">
        <i class="fas fa-pager"></i>
      </div>
      <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
</div>


<?= $this->endSection() ?>