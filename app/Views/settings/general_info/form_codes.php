<div class="card">
    <div class="card-header">
        <h3 class="card-title">Form Codes</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>         
    <div class="card-body">
        <form id="form_form_codes" action="<?= url_to('general_info.save') ?>">
            <?= csrf_field(); ?>
            <div class="form-group">
                <label class="required" for="purchase_order_form_code">Purchase Order</label>
                <input type="text" name="purchase_order_form_code" id="purchase_order_form_code" class="form-control" placeholder="Ex. F06">
                <small id="alert_purchase_order_form_code" class="text-danger"></small>
            </div>
            <div class="form-group">
                <label class="required" for="billing_invoice_form_code">Billing Invoice</label>
                <input type="text" name="billing_invoice_form_code" id="billing_invoice_form_code" class="form-control" placeholder="Ex. F07">
                <small id="alert_billing_invoice_form_code" class="text-danger"></small>
            </div>
            <?= $this->include('settings/general_info/button-save'); ?>
        </form>
    </div>
</div>