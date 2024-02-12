<?=$this->extend('templates/default');?>
<?=$this->section('content');?>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12 col-md-7">
            <?= $this->include('settings/general_info/system'); ?>
        </div>
        <div class="col-sm-12 col-md-5">
            <?= $this->include('settings/general_info/logo'); ?>
        </div>
        <div class="col-12">
            <?= $this->include('settings/general_info/company'); ?>
        </div>
        <div class="col-sm-12 col-md-4">
            <?= $this->include('settings/general_info/form_codes'); ?>
        </div>
        <div class="col-sm-12 col-md-4">
            <?= $this->include('settings/general_info/billing_invoice'); ?>
        </div>
    </div>
</div>
<?= $this->include('templates/loading'); ?>
<?=$this->endSection();?>
