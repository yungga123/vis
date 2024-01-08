<?=$this->extend('templates/print');?>
<?=$this->section('content');?>
<?php
$hourly_rate    = floatval($payroll['hourly_rate'] ?? 0);
$daily_rate     = floatval($payroll['daily_rate'] ?? 0);
?>
<div class="container-fluid">
	<div class="row">		
		<div class="col-12">
            <div class="d-flex align-items-center justify-content-between">                
			    <img src="<?= $general_info['company_logo'] ?>" alt="Vinculum Logo" class="img-thumbnail mb-4" style="height: 120px; width: 380px">
                <div>
                    <div class="text-uppercase text-right">
                        <strong><?= $general_info['company_name'] ?></strong>
                    </div>
                    <div class="text-right">
                        <?= $general_info['company_address'] ?>
                    </div>
                </div>
            </div>
		</div>
        <div class="col-6">
            <table class="table table-bordered table-sm" style="font-size: 15px">
                <tbody>
                    <tr>                        
                        <td><strong>Cut-Off Period: </strong></td>
                        <td><?= $payroll['cutoff_period'] ?? '' ?></td>
                    </tr>
                    <tr>                        
                        <td><strong>Employee ID: </strong></td>
                        <td><?= $payroll['employee_id'] ?? '' ?></td>
                    </tr>
                    <tr>                        
                        <td><strong>Employee Name: </strong></td>
                        <td><?= $payroll['employee_name'] ?? '' ?></td>
                    </tr>
                    <tr>                        
                        <td><strong>Position: </strong></td>
                        <td><?= $payroll['position'] ?? '' ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-6">
            <table class="table table-bordered table-sm" style="font-size: 15px">
                <tbody>
                    <tr>                        
                        <td><strong>Status: </strong></td>
                        <td><?= $payroll['employment_status'] ?? '' ?></td>
                    </tr>
                    <tr>                        
                        <td><strong>Salary Type: </strong></td>
                        <td><?= $payroll['salary_type'] ?? '' ?></td>
                    </tr>
                    <tr>                        
                        <td><strong>Salary Rate: </strong></td>
                        <td><?= $payroll['basic_salary'] ?? 0 ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-12">
            <table class="table table-bordered table-sm" style="font-size: 15px">
                <tbody>
                    <tr>
                        <td><strong>SSS No.</strong></td>
                        <td><strong>Pag-IBIG No.</strong></td>
                        <td><strong>PhilHealth No.</strong></td>
                        <td><strong>Tin No.</strong></td>
                    </tr>
                    <tr>
                        <td><?= $payroll['sss_no'] ?? '' ?></td>
                        <td><?= $payroll['pag_ibig_no'] ?? '' ?></td>
                        <td><?= $payroll['philhealth_no'] ?? '' ?></td>
                        <td><?= $payroll['tin_no'] ?? '' ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-6">
            <table class="table table-bordered table-sm" style="font-size: 15px">
                <tbody>
                    <tr>
                        <td colspan="4" class="text-center"><strong>EARNINGS</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Items</strong></td>
                        <td><strong>Rate</strong></td>
                        <td><strong>Hrs/Days</strong></td>
                        <td><strong>Amount</strong></td>
                    </tr>
                    <tr>
                        <td>Basic Income</td>
                        <td><?= number_format($daily_rate, 2) ?></td>
                        <td><?= $payroll['working_days'] ?? 0 ?></td>
                        <td>
                            <?php $cutoff_pay = $payroll['cutoff_pay'] ?? 0 ?>
                            <?= number_format($cutoff_pay, 2) ?>
                        </td>
                    </tr>
                    <tr>
                        <?php $working_days_off = floatval($earnings['working_days_off'] ?? 0) ?>
                        <td>WDO</td>
                        <td><?= number_format(empty($working_days_off) ? 0 : $daily_rate, 2) ?></td>
                        <td><?= number_format($working_days_off, 2) ?></td>
                        <td>
                            <?php $working_days_off_amt = $earnings['working_days_off_amt'] ?? 0 ?>
                            <?= number_format($working_days_off_amt, 2) ?>
                        </td>
                    </tr>
                    <tr>
                        <?php $night_diff = floatval($earnings['night_diff'] ?? 0) ?>
                        <td>Night Diff.</td>
                        <td><?= number_format(($night_diff * ($hourly_rate * $settings['night_diff'])), 2) ?></td>
                        <td><?= number_format($night_diff, 2) ?></td>
                        <td>
                            <?php $night_diff_amt = floatval($earnings['night_diff_amt'] ?? 0) ?>
                            <?= number_format($night_diff_amt, 2) ?>
                        </td>
                    </tr>
                    <tr>
                        <?php $over_time = floatval($earnings['over_time'] ?? 0) ?>
                        <td>Overtime</td>
                        <td>
                            <?php 
                                if ($over_time == 0) echo '0.00';
                                else {
                                    echo number_format($settings['overtime'] > 1 
                                        ? ($hourly_rate * $settings['overtime'])
                                        : ($hourly_rate + ($hourly_rate * $settings['overtime'])), 2);
                                }
                            ?>
                        </td>
                        <td><?= number_format($over_time, 2) ?></td>
                        <td>
                            <?php $over_time_amt = $earnings['over_time_amt'] ?? 0 ?>
                            <?= number_format($over_time_amt, 2) ?>
                        </td>
                    </tr>
                    <tr>
                        <?php $regular_holiday = floatval($earnings['regular_holiday'] ?? 0) ?>
                        <td>Regular Holiday</td>
                        <td><?= number_format(($regular_holiday * ($daily_rate * $settings['regular_holiday'])), 2) ?></td>
                        <td><?= number_format($regular_holiday, 2) ?></td>
                        <td>
                            <?php $regular_holiday_amt = $earnings['regular_holiday_amt'] ?? 0 ?>
                            <?= number_format($regular_holiday_amt, 2) ?>
                        </td>
                    </tr>
                    <tr>
                        <?php $special_holiday = floatval($earnings['special_holiday'] ?? 0) ?>
                        <td>Special Holiday</td>
                        <td><?= number_format(($special_holiday * ($daily_rate * $settings['special_holiday'])), 2) ?></td>
                        <td><?= number_format($special_holiday, 2) ?></td>
                        <td>
                            <?php $special_holiday_amt = $earnings['special_holiday_amt'] ?? 0 ?>
                            <?= number_format($special_holiday_amt, 2) ?>
                        </td>
                    </tr>
                    <tr>
                        <?php $service_incentive_leave = floatval($earnings['service_incentive_leave'] ?? 0) ?>
                        <td>SIL Pay</td>
                        <td><?= number_format(($service_incentive_leave * $daily_rate), 2) ?></td>
                        <td><?= number_format($service_incentive_leave, 2) ?></td>
                        <td>
                            <?php $service_incentive_leave_amt = $earnings['service_incentive_leave_amt'] ?? 0 ?>
                            <?= number_format($service_incentive_leave_amt, 2) ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">Subtotal</td>
                        <td>
                            <?php $subtotal_earnings = $cutoff_pay + $working_days_off_amt + $night_diff_amt + $over_time_amt + $regular_holiday_amt + $special_holiday_amt + $service_incentive_leave_amt ?>
                            <?= number_format($subtotal_earnings, 2) ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-center"><strong>Non-Taxable</strong></td>
                    </tr>
                    <tr>
                        <td>Incentives</td>
                        <td colspan="3">
                            <?php $incentives = $earnings['incentives'] ?? 0 ?>
                            <?= number_format($incentives, 2) ?>
                        </td>
                    <tr>
                    </tr>
                        <td>Commission</td>
                        <td colspan="3">
                            <?php $commission = $earnings['commission'] ?? 0 ?>
                            <?= number_format($commission, 2) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>13th Month</td>
                        <td colspan="3">
                            <?php $thirteenth_month = $earnings['thirteenth_month'] ?? 0 ?>
                            <?= number_format($thirteenth_month, 2) ?>
                        </td>
                    <tr>
                    </tr>
                        <td>Addback</td>
                        <td colspan="3">
                            <?php $add_back = $earnings['add_back'] ?? 0 ?>
                            <?= number_format($add_back, 2) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>NT Subtotal</td>
                        <td colspan="3">
                            <?php $non_taxable = ($incentives + $commission + $thirteenth_month + $add_back) ?>
                            <?= number_format($non_taxable, 2) ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>TOTAL EARNINGS</strong></td>
                        <td colspan="3">
                            <?php $total_earnings = ($subtotal_earnings + $non_taxable) ?>
                            <?= number_format($total_earnings, 2) ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-6">
            <table class="table table-bordered table-sm" style="font-size: 15px">
                <tbody>
                    <tr>
                        <td colspan="4" class="text-center"><strong>DEDUCTIONS</strong></td>
                    </tr>
                    <tr>
                        <td><strong>Items</strong></td>
                        <td><strong>Rate</strong></td>
                        <td><strong>Hrs/Days</strong></td>
                        <td><strong>Amount</strong></td>
                    </tr>
                    <tr>
                        <?php $addt_rest_days = floatval($deductions['addt_rest_days'] ?? 0) ?>
                        <td>Rest Day/s</td>
                        <td><?= number_format(($addt_rest_days * $daily_rate), 2) ?></td>
                        <td><?= number_format($addt_rest_days, 2) ?></td>
                        <td>
                            <?php $addt_rest_days_amt = $deductions['addt_rest_days_amt'] ?? 0 ?>
                            <?= number_format($addt_rest_days_amt, 2) ?>
                        </td>
                    </tr>
                    <tr>
                        <?php $days_absent = floatval($deductions['days_absent'] ?? 0) ?>
                        <td>Absent/s</td>
                        <td><?= number_format(($days_absent * $daily_rate), 2) ?></td>
                        <td><?= number_format($days_absent, 2) ?></td>
                        <td>
                            <?php $days_absent_amt = $deductions['days_absent_amt'] ?? 0 ?>
                            <?= number_format($days_absent_amt, 2) ?>
                        </td>
                    </tr>
                    <tr>
                        <?php $hours_late = floatval($deductions['hours_late'] ?? 0) ?>
                        <td>Tardiness</td>
                        <td><?= number_format(($hours_late * $hourly_rate), 2) ?></td>
                        <td><?= number_format($hours_late, 2) ?></td>
                        <td>
                            <?php $hours_late_amt = $deductions['hours_late_amt'] ?? 0 ?>
                            <?= number_format($hours_late_amt, 2) ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">Subtotal</td>
                        <td>
                            <?php $subtotal_deductions = $addt_rest_days_amt + $days_absent_amt + $hours_late_amt ?>
                            <?= number_format($subtotal_deductions, 2) ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-center">
                            <strong>Government Contributions</strong>
                        </td>
                    </tr>
                    <tr>
                        <td>SSS</td>
                        <td colspan="3">
                            <?php $govt_sss = $deductions['govt_sss'          ] ?? 0 ?>
                            <?= number_format($govt_sss, 2) ?>
                        </td>
                    <tr>
                    </tr>
                        <td>Pag-IBIG</td>
                        <td colspan="3">
                            <?php $govt_pagibig = $deductions['govt_pagibig'] ?? 0 ?>
                            <?= number_format($govt_pagibig, 2) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>PhilHealth</td>
                        <td colspan="3">
                            <?php $govt_philhealth = $deductions['govt_philhealth'] ?? 0 ?>
                            <?= number_format($govt_philhealth, 2) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Withholding TAX</td>
                        <td colspan="3">
                            <?php $withholding_tax = $deductions['withholding_tax'] ?? 0 ?>
                            <?= number_format($withholding_tax, 2) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Subtotal</td>
                        <td colspan="3">
                            <?php $govt_deductions = ($govt_sss + $govt_pagibig + $govt_philhealth + $withholding_tax) ?>
                            <?= number_format($govt_deductions, 2) ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-center">
                            <strong>Others</strong>
                        </td>
                    </tr>
                    <tr>
                        <td>HMO</td>
                        <td colspan="3"></td>
                    <tr>
                    </tr>
                        <td>Cash Advance</td>
                        <td colspan="3">
                            <?php $cash_advance = $payroll['cash_advance'] ?? 0 ?>
                            <?= number_format($cash_advance, 2) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Others</td>
                        <td colspan="3">
                            <?php $others = $payroll['others'] ?? 0 ?>
                            <?= number_format($others, 2) ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Subtotal</td>
                        <td colspan="3">
                            <?php $other_deductions = ($cash_advance + $others) ?>
                            <?= number_format($other_deductions, 2) ?>
                        </td>
                    </tr>
                    <tr>
                        <td><strong>TOTAL DEDUCTIONS</strong></td>
                        <td colspan="3">
                            <?php $total_deductions = ($subtotal_deductions + $govt_deductions + $other_deductions) ?>
                            <?= number_format($total_deductions, 2) ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
		<div class="col-12">
			<table class="table table-bordered table-sm" style="font-size: 15px">
				<tbody>
					<tr>
						<td style="height: 50px; min-height: 50px;">
							<div class="text-bold">Notes:</div>
							<div><?= $payroll['notes'] ?? '' ?></div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
        <div class="col-6">
            <div class="d-flex justify-content-start flex-column">
                <div>
                    I hereby acknowledge to have the sum specified herein as full payment of my service rendered.
                </div>
                <div class="mt-2">
                    <div>Received By:</div>
                    <div class="text-bold ml-5">
                        <span class="ml-3">__________________________________</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="d-flex justify-content-end flex-column">
                <div class="text-right text-lg">
                    <strong>Gross Pay: </strong>
                    <span>
                        <?= ($payroll['gross_pay'] ?? 0) ?>
                    </span>
                </div>
                <div class="text-right text-lg">
                    <strong>Net Pay: </strong>
                    <span>
                        <?= ($payroll['net_pay'] ?? 0) ?>
                    </span>
                </div>
            </div>
        </div>
	</div>
</div>
<?=$this->endSection();?>
