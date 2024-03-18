<div class="card">
    <div class="card-header">
        <h3 class="card-title">Withholding Tax Table - Monthly</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>         
    <div class="card-body">
        <div class="row">
            <div class="col-sm-12 col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">List</h5>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-striped table-valign-middle" id="tax_table">
                            <thead>
                                <tr>
                                    <td><strong>Compensation Range</strong></td>
                                    <td><strong>Fixed Tax Amount</strong></td>
                                    <td><strong>Compensation Level</strong></td>
                                    <td><strong>Tax Rate %</strong></td>
                                    <td></td>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div>
                    <p>You can check the reference here - from <a href="https://www.bir.gov.ph/index.php/tax-information/withholding-tax.html" target="_blank" rel="noopener noreferrer">BIR</a> and <a href="https://www.omnicalculator.com/finance/income-tax-philippines#how-to-calculate-income-tax-in-the-philippines" target="_blank" rel="noopener noreferrer">this - for the list format</a>.</p>
                </div>
            </div>
            <div class="col-sm-12 col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Form</h5>
                    </div>
                    <div class="card-body">
                        <form id="form_bir_tax" class="with-label-indicator" action="<?= url_to('payroll.settings.tax.save') ?>">
                            <?= csrf_field(); ?>
                            <input type="hidden" name="id" id="tax_id" class="form-control" readonly>

                            <label><strong>Compensation Range (Monthly)</strong></label>
                            <div class="form-group">
                                <label class="required" for="compensation_range_start">Monthly Start</label>
                                <input type="number" name="compensation_range_start" id="compensation_range_start" class="form-control" placeholder="0.00" step="00.01">
                                <small id="alert_compensation_range_start" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label class="required" for="compensation_range_end">Monthly End</label>
                                <input type="number" name="compensation_range_end" id="compensation_range_end" class="form-control" placeholder="0.00" step="00.01">
                                <small id="alert_compensation_range_end" class="text-danger"></small>
                                <div class="row mt-2">
                                    <div class="col-sm-12 col-md-4">
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="rb_amount" name="rb_type" class="custom-control-input rb_type" value="amount" checked>
                                            <label class="custom-control-label" for="rb_amount">Amount</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="rb_below" name="rb_type" class="custom-control-input rb_type" value="below">
                                            <label class="custom-control-label" for="rb_below">Below</label>
                                        </div>
                                    </div>
                                    <div class="col-sm-12 col-md-4">
                                        <div class="custom-control custom-radio custom-control-inline">
                                            <input type="radio" id="rb_above" name="rb_type" class="custom-control-input rb_type" value="above">
                                            <label class="custom-control-label" for="rb_above">Above</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="required" for="fixed_tax_amount">Fixed Tax Amount</label>
                                <input type="number" name="fixed_tax_amount" id="fixed_tax_amount" class="form-control" placeholder="0.00" step="00.01">
                                <small id="alert_fixed_tax_amount" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label class="required" for="compensation_level">Compensation Level</label>
                                <input type="number" name="compensation_level" id="compensation_level" class="form-control" placeholder="00.00" step="00.01">
                                <small id="alert_compensation_level" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label class="required" for="tax_rate">Tax Rate %</label>
                                <input type="text" name="tax_rate" id="tax_rate" class="form-control" placeholder="Ex. 15%" step="00.01" data-percentage>
                                <small id="alert_tax_rate" class="text-danger"></small>
                            </div>
                            <?= $this->include('payroll/settings/button-save'); ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>