<!-- Content Wrapper. Contains page content -->
<?= $this->extend('templates/default') ;?>

<?= $this->section('content') ;?>

ADMIN DASHBOARD HERE!!

  <?= $this->section('CustomScript') ;?>
    <?= $this->include('admin_dashboard/script') ;?>
  <?= $this->endSection() ;?>


<?= $this->endSection() ;?>