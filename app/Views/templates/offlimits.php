<?= $this->extend('templates/default') ;?>

<?= $this->section('content') ;?>
<div class="error-page">
    <h2 class="headline text-danger">NO!</h2>

    <div class="error-content">
        <h3><i class="fas fa-exclamation-triangle text-danger"></i> Your access is denied.</h3>

        <p>
            You are offlimits here!!!
            <a href="<?= $href ?>">Click here!</a> to return.
        </p>
    </div>
</div>
<!-- /.error-page -->
<?= $this->endSection() ;?>