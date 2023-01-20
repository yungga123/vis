<?= $this->extend('templates/default') ;?>


<?= $this->section('content') ;?>
<div class="error-page">
    <h2 class="headline text-success">OK!</h2>

    <div class="error-content">
        <h3><i class="fas fa-exclamation-triangle text-success"></i> Data has been deleted.</h3>

        <p>
            To restore this data, you can contact the system administrator.
            <a href="<?= $href ?>">Click here!</a> to return to table.
        </p>
    </div>
</div>
<!-- /.error-page -->
<?= $this->endSection() ;?>