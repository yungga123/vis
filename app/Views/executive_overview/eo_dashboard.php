<?= $this->extend('templates/default') ;?>

<?= $this->section('content') ;?>
<div class="container-fluid">
  EO Dashboard here
</div>
<?= $this->endSection() ;?>

<?= $this->section('CustomScript') ;?>
<?= $this->include('executive_overview/script') ;?>
<?= $this->endSection() ;?>
