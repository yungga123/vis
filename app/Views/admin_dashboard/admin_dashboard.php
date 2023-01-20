<!-- Content Wrapper. Contains page content -->
<?= $this->extend('templates/default'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
  ADMIN DASHBOARD HERE!!
</div>


<?= $this->endSection(); ?>

<?= $this->section('CustomScript'); ?>
<?= $this->include('admin_dashboard/script'); ?>
<?= $this->endSection(); ?>