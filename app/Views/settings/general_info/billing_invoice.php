<div class="card">
    <div class="card-header">
        <h3 class="card-title">Billing Invoice</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>         
    <div class="card-body">
        <form id="form_billing_invoice" action="<?= url_to('general_info.save') ?>">
            <?= csrf_field(); ?>
            <div class="form-group">
                <label class="required" for="billing_invoice_overdue_interest_per_day">Overdue Interest Per Day %</label>
                <input type="text" name="billing_invoice_overdue_interest_per_day" id="billing_invoice_overdue_interest_per_day" class="form-control" placeholder="Ex. 0.23%">
                <small id="alert_billing_invoice_overdue_interest_per_day" class="text-danger"></small>
            </div>
            <div class="form-group">
                <label class="required" for="billing_invoice_overdue_interest_per_month">Overdue Interest Per Month %</label>
                <input type="text" name="billing_invoice_overdue_interest_per_month" id="billing_invoice_overdue_interest_per_month" class="form-control" placeholder="Ex. 7%">
                <small id="alert_billing_invoice_overdue_interest_per_month" class="text-danger"></small>
            </div>
            <?= $this->include('settings/general_info/button-save'); ?>
        </form>
    </div>
</div>