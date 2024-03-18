<div class="card">
    <div class="card-header">
        <h3 class="card-title">System Info</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>         
    <div class="card-body">
        <form id="form_system_info" action="<?= url_to('general_info.save') ?>">
            <?= csrf_field(); ?>
            <div class="form-group">
                <label class="required" for="system_name">System Name</label>
                <input type="text" name="system_name" id="system_name" class="form-control" placeholder="System Name">
                <small id="alert_system_name" class="text-danger"></small>
            </div>
            <div class="form-group">
                <label class="required" for="vat_percent">VAT Percentage %</label>
                <input type="text" name="vat_percent" id="vat_percent" class="form-control" placeholder="Ex. 12">
                <small id="alert_vat_percent" class="text-danger"></small>
            </div>
            <?= $this->include('settings/general_info/button-save'); ?>
        </form>
    </div>
</div>