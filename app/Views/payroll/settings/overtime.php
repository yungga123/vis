<div class="card">
    <div class="card-header">
        <h3 class="card-title">Overtime, Night Diff, Rest Day Work & Holidays (%)</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>         
    <div class="card-body">
        <form id="form_overtime" class="with-label-indicator" action="<?= url_to('payroll.settings.save') ?>">
            <?= csrf_field(); ?>            
            <input type="hidden" name="rules" value="overtime" class="form-control" readonly>
            <div>
                <p><strong>Please input data/numbers by percentage (%)!</strong></p>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <tbody>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <label class="required" for="overtime">Regular Overtime %</label>
                                    <input type="text" name="overtime" id="overtime" class="form-control" placeholder="Ex. 25%" step="00.01" data-percentage>
                                    <small id="alert_overtime" class="text-danger"></small>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <label class="required" for="night_diff">Nigth Diff</label>
                                    <input type="text" name="night_diff" id="night_diff" class="form-control" placeholder="Ex. 10%" step="00.01" data-percentage>
                                    <small id="alert_night_diff" class="text-danger"></small>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Rest Day / Holiday Working</strong></td>
                            <td><strong>Overtime</strong></td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <label class="required" for="rest_day">Rest Day %</label>
                                    <input type="text" name="rest_day" id="rest_day" class="form-control" placeholder="Ex. 130%" step="00.01" data-percentage>
                                    <small id="alert_rest_day" class="text-danger"></small>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <label class="required" for="rest_day_overtime">RD Overtime %</label>
                                    <input type="text" name="rest_day_overtime" id="rest_day_overtime" class="form-control" placeholder="Ex. 16.90%" step="00.01" data-percentage>
                                    <small id="alert_rest_day_overtime" class="text-danger"></small>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <label class="required" for="regular_holiday">Regular Holiday %</label>
                                    <input type="text" name="regular_holiday" id="regular_holiday" class="form-control" placeholder="Ex. 200%" step="00.01" data-percentage>
                                    <small id="alert_regular_holiday" class="text-danger"></small>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <label class="required" for="regular_holiday_overtime">RH Overtime %</label>
                                    <input type="text" name="regular_holiday_overtime" id="regular_holiday_overtime" class="form-control" placeholder="Ex. 26%" step="00.01" data-percentage>
                                    <small id="alert_regular_holiday_overtime" class="text-danger"></small>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="form-group">
                                    <label class="required" for="special_holiday">Special Holiday %</label>
                                    <input type="text" name="special_holiday" id="special_holiday" class="form-control" placeholder="Ex. 130%" step="00.01" data-percentage>
                                    <small id="alert_special_holiday" class="text-danger"></small>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <label class="required" for="special_holiday_overtime">SH Overtime %</label>
                                    <input type="text" name="special_holiday_overtime" id="special_holiday_overtime" class="form-control" placeholder="Ex. 16.90%" step="00.01" data-percentage>
                                    <small id="alert_special_holiday_overtime" class="text-danger"></small>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <?= $this->include('payroll/settings/button-save'); ?>
        </form>
    </div>
</div>