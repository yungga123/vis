<div class="card">
    <div class="card-header">
        <h3 class="card-title">Government Mandatory Contributions</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>         
    <div class="card-body">
        <form id="form_government" class="with-label-indicator" action="<?= url_to('payroll.settings.save') ?>">
            <?= csrf_field(); ?>
            <input type="hidden" name="rules" value="government" class="form-control" readonly>
            <div class="row">
                <div class="col-12">
                    <p><strong>Please make sure to enter the accurate data.</strong></p>
                </div>
                <!-- <div class="col-sm-12 col-md-4"> -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">SSS</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-12 col-md-12">
                                    <div class="form-group">
                                        <div class="text-bold mb-2">Contribution Rate %</div>
                                        <div class="row">
                                            <div class="col-sm-12 col-md-6">
                                                <label class="required" for="sss_contri_rate_employeer">Employeer</label>
                                                <input type="text" name="sss_contri_rate_employeer" id="sss_contri_rate_employeer" class="form-control" placeholder="Ex. 9.5%" data-percentage>
                                            </div>
                                            <div class="col-sm-12 col-md-6">
                                                <label class="required" for="sss_contri_rate_employee">Employee</label>
                                                <input type="text" name="sss_contri_rate_employee" id="sss_contri_rate_employee" class="form-control" placeholder="Ex. 4.5%" data-percentage>
                                            </div>
                                        </div>
                                        <small id="alert_sss_contri_rate_employeer" class="text-danger"></small>
                                        <small id="alert_sss_contri_rate_employee" class="text-danger"></small>
                                    </div>
                                    <hr />
                                </div>
                                <div class="col-sm-12 col-md-6">
                                    <div class="form-group">
                                        <label class="required" for="sss_salary_range_min">Staring Salary Range Amount</label>
                                        <input type="number" name="sss_salary_range_min" id="sss_salary_range_min" class="form-control" placeholder="Ex. 4,250" step="00.01">
                                        <small id="alert_sss_salary_range_min" class="text-danger"></small>
                                        <div class="text-sm mt-1">The staring amount of the compensation range (Ex. Below - 4,250).</div>
                                    </div>
                                    <div class="form-group">
                                        <label class="required" for="sss_salary_range_max">Last Salary Range Amount</label>
                                        <input type="number" name="sss_salary_range_max" id="sss_salary_range_max" class="form-control" placeholder="Ex. 29,750" step="00.01">
                                        <small id="alert_sss_salary_range_max" class="text-danger"></small>
                                        <div class="text-sm mt-1">The very last amount of the compensation range (Ex. 29,750 - Over).</div>
                                    </div>
                                    <div class="form-group">
                                        <label class="required" for="sss_next_diff_range_start_amount">Diff to Next Salary Range Start Amount</label>
                                        <input type="number" name="sss_next_diff_range_start_amount" id="sss_next_diff_range_start_amount" class="form-control" placeholder="Ex. 499.99" step="00.01">
                                        <small id="alert_sss_next_diff_range_start_amount" class="text-danger"></small>
                                        <div class="text-sm mt-1">The amount difference before the next range of compensation (Ex. 4250 + 499.99 = 4749.99 - which is the 2nd half of the range).</div>
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-6">
                                    <div class="form-group">
                                        <label class="required" for="sss_starting_msc">Starting MSC Total Amount</label>
                                        <input type="number" name="sss_starting_msc" id="sss_starting_msc" class="form-control" placeholder="Ex. 4,000" step="00.01">
                                        <small id="alert_sss_starting_msc" class="text-danger"></small>
                                        <div class="text-sm mt-1">The starting Monthly Salary Credit (MSC) total amount (Ex. 4,000).</div>
                                    </div>
                                    <div class="form-group">
                                        <label class="required" for="sss_last_msc">Last MSC Total Amount</label>
                                        <input type="number" name="sss_last_msc" id="sss_last_msc" class="form-control" placeholder="Ex. 30,000" step="00.01">
                                        <small id="alert_sss_last_msc" class="text-danger"></small>
                                        <div class="text-sm mt-1">The last Monthly Salary Credit (MSC) total amount (Ex. 30,000).</div>
                                    </div>
                                    <div class="form-group">
                                        <label class="required" for="sss_next_diff_msc_total_amount">Diff to Next MSC Total Amount</label>
                                        <input type="number" name="sss_next_diff_msc_total_amount" id="sss_next_diff_msc_total_amount" class="form-control" placeholder="Ex. 500" step="00.01">
                                        <small id="alert_sss_next_diff_msc_total_amount" class="text-danger"></small>
                                        <div class="text-sm mt-1">The amount difference to the next MSC total amount (Ex. 500).</div>
                                    </div>
                                </div>
                            </div>
                            <p>You can check the reference here - <a href="https://www.sss.gov.ph/sss/DownloadContent?fileName=2023-Schedule-of-Contributions.pdf" target="_blank">from SSS</a>.</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Pag-IBIG HDMF</h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <p><strong>Contribution Rate %</strong></p>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="required" for="pagibig_contri_rate_employeer">Employeer</label>
                                        <input type="text" name="pagibig_contri_rate_employeer" id="pagibig_contri_rate_employeer" class="form-control" placeholder="Ex. 2%" step="00.01" data-percentage>
                                        <small id="alert_pagibig_contri_rate_employeer" class="text-danger"></small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="required" for="pagibig_contri_rate_employee">Employee</label>
                                        <input type="text" name="pagibig_contri_rate_employee" id="pagibig_contri_rate_employee" class="form-control" placeholder="Ex. 2%" step="00.01" data-percentage>
                                        <small id="alert_pagibig_contri_rate_employee" class="text-danger"></small>
                                    </div>
                                </div>
                            </div>
                            <hr />
                            <div class="form-group">
                                <label class="required" for="pagibig_max_monthly_contri">Max Monthly Contribution</label>
                                <input type="number" name="pagibig_max_monthly_contri" id="pagibig_max_monthly_contri" class="form-control" placeholder="Ex. 200" step="00.01">
                                <small id="alert_pagibig_max_monthly_contri" class="text-danger"></small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">PhilHealth</h4>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="required" for="philhealth_contri_rate">Contribution Rate %</label>
                                <input type="text" name="philhealth_contri_rate" id="philhealth_contri_rate" class="form-control" placeholder="Ex. 4%" step="00.01" data-percentage>
                                <small id="alert_philhealth_contri_rate" class="text-danger"></small>
                            </div>
                            <hr />
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="required" for="philhealth_income_floor">Income Floor</label>
                                        <input type="number" name="philhealth_income_floor" id="philhealth_income_floor" class="form-control" placeholder="Ex. 10,000" step="00.01">
                                        <small id="alert_philhealth_income_floor" class="text-danger"></small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="required" for="philhealth_if_monthly_premium">Monthly Premium</label>
                                        <input type="number" name="philhealth_if_monthly_premium" id="philhealth_if_monthly_premium" class="form-control" placeholder="Ex. 400" step="00.01">
                                        <small id="alert_philhealth_if_monthly_premium" class="text-danger"></small>
                                    </div>
                                </div>
                            </div>
                            <hr />
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="required" for="philhealth_income_ceiling">Income Ceiling</label>
                                        <input type="number" name="philhealth_income_ceiling" id="philhealth_income_ceiling" class="form-control" placeholder="Ex. 80,000" step="00.01">
                                        <small id="alert_philhealth_income_ceiling" class="text-danger"></small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="required" for="philhealth_ic_monthly_premium">Monthly Premium</label>
                                        <input type="number" name="philhealth_ic_monthly_premium" id="philhealth_ic_monthly_premium" class="form-control" placeholder="Ex. 3,200" step="00.01">
                                        <small id="alert_philhealth_ic_monthly_premium" class="text-danger"></small>
                                    </div>
                                </div>
                            </div>
                            <p>Refer to <a href="https://sprout.ph/blog/philhealth-new-contribution-rates/#:~:text=How%20much%20is%20the%20income%20floor%20%26%20ceiling%2C%20%26%20what%E2%80%99s%20the%20premium%20rate%3F">this about income floor and ceiling</a>.</p>
                        </div>
                    </div>
                </div>
            </div>
            <?= $this->include('payroll/settings/button-save'); ?>
        </form>
    </div>
</div>