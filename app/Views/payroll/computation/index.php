<?=$this->extend('templates/default');?>
<?=$this->section('content');?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">                    
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 <?= isset($payroll) ? '' : 'd-none' ?>" id="employee">
                            <div class="form-group">
                                <label class="required" for="employee_id">Employee Name</label>
                                <select class="custom-select <?= isset($payroll) ? 'd-none' : '' ?>" name="employee_id" id="employee_id" style="width: 100%;">
                                </select>
                                <input type="<?= isset($payroll) ? 'text' : 'hidden' ?>" name="employee_name" id="employee_name" readonly class="form-control" value="<?= $payroll['employee_name'] ?? '' ?>"/>
                            </div>
                            <div class="row employee_info d-none">
                                <div class="col-md-6">
                                    <div class="info-text employee_id">
                                        <strong>Employee ID: </strong>
                                        <span><?= $payroll['employee_id'] ?? '' ?></span>
                                    </div>
                                    <div class="info-text employment_status">
                                        <strong>Employment Status: </strong>
                                        <span><?= $payroll['employment_status'] ?? '' ?></span>
                                    </div>
                                    <div class="info-text position">
                                        <strong>Position: </strong>
                                        <span><?= $payroll['position'] ?? '' ?></span>
                                    </div>
                                    <div class="info-text rate_type">
                                        <strong>Salary Type: </strong>
                                        <span><?= $payroll['salary_type'] ?? '' ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-text salary_rate">
                                        <strong>Basic Salary: </strong>
                                        <span><?= number_format($payroll['basic_salary'] ?? 0, 2) ?></span>
                                    </div>
                                    <div class="info-text cut_off_pay">
                                        <strong>Cut-Off Pay: </strong>
                                        <span><?= number_format($payroll['cutoff_pay'] ?? 0, 2) ?></span>
                                    </div>
                                    <div class="info-text daily_rate">
                                        <strong>Daily Rate: </strong>
                                        <span><?= number_format($payroll['daily_rate'] ?? 0, 2) ?></span>
                                    </div>
                                    <div class="info-text hourly_rate">
                                        <strong>Hourly Rate: </strong>
                                        <span><?= number_format($payroll['hourly_rate'] ?? 0, 2) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="start_date">Start Cut-Off Date</label>
                                        <input type="date" class="form-control" name="start_date" id="start_date" placeholder="Start Date" value="<?= $payroll['cutoff_start'] ?? '' ?>" <?= isset($payroll) ? 'readonly' : '' ?>>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="end_date">End Cut-Off Date</label>
                                        <input type="date" class="form-control" name="end_date" id="end_date" placeholder="End Date" value="<?= $payroll['cutoff_end'] ?? '' ?>" <?= isset($payroll) ? 'readonly' : '' ?>>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group text-center">
                                        <button class="btn btn-primary btn-cut-off" <?= isset($payroll) ? 'disabled' : '' ?>>Proceed</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-7">
            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <?= $this->include('payroll/computation/earnings'); ?>
                </div>
                <div class="col-sm-12 col-md-6">
                    <?= $this->include('payroll/computation/deductions'); ?>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-5">
            <?= $this->include('payroll/computation/overview'); ?>
        </div>
    </div>
</div>
<?= $this->include('templates/loading'); ?>
<?=$this->endSection();?>
