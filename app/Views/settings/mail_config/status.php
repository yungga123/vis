<?= $this->extend('templates/default') ;?>

<?= $this->section('content') ;?>
<div class="error-page">
    <h2 class="headline text-<?= esc($status); ?>">
        <?= $status === 'success'? 'Yey' : 'Oops'; ?>!
    </h2>

    <div class="error-content">
        <h3><?= esc($message); ?></h3>

        <p>
            <?= $status === 'success'? '' : 'Please try again!'; ?>
            <a href="<?= url_to('mail.home'); ?>">Click here</a> to return.
        </p>
    </div>
</div>
<!-- /.error-page -->
<?= $this->endSection() ;?>