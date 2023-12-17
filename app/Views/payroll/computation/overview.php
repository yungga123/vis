<div class="card">
    <div class="card-header">
        <h4 class="card-title text-lg">Overview</h4>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <td colspan="2" width="50%"><strong>Earnings</strong></td>
                        <td colspan="2" width="50%"><strong>Deductions</strong></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Basic Income</td>
                        <td class="basic_pay">00.00</td>
                        <!-- Deduction -->
                        <td>Absent</td>
                        <td class="absent_amt">00.00</td>
                    </tr>
                    <tr>
                        <td>Working Day-Off</td>
                        <td class="working_days_off_amt">00.00</td>
                        <!-- Deduction -->
                        <td>Tardiness</td>
                        <td class="tardiness_amt">00.00</td>
                    </tr>
                    <tr>
                        <td>Overtime</td>
                        <td class="over_time_amt">00.00</td>
                        <!-- Deduction -->
                        <td>Additional Rest Day</td>
                        <td class="additional_rest_day_amt">00.00</td>
                    </tr>
                    <tr>
                        <td>Night Diff.</td>
                        <td class="night_diff_amt">00.00</td>
                        <!-- Deduction -->
                        <td>SSS</td>
                        <td class="sss">00.00</td>
                    </tr>
                    <tr>
                        <td>Regular Holiday</td>
                        <td class="regular_holiday_amt">00.00</td>
                        <!-- Deduction -->
                        <td>Pag-ibig</td>
                        <td class="pagibig">00.00</td>
                    </tr>
                    <tr>
                        <td>Special Holiday</td>
                        <td class="special_holiday_amt">00.00</td>
                        <!-- Deduction -->
                        <td>Philhealth</td>
                        <td class="philhealth">00.00</td>
                    </tr>
                    <tr>
                        <td>Vacation Leave</td>
                        <td class="vacation_leave_amt">00.00</td>
                        <!-- Deduction -->
                        <td>Withholding Tax</td>
                        <td class="withholding_tax">00.00</td>
                    </tr>
                    <tr>
                        <td>Sick Leave</td>
                        <td class="sick_leave_amt">00.00</td>
                        <!-- Deduction -->
                        <td>Cash Advance</td>
                        <td class="cash_advance">00.00</td>
                    </tr>
                    <tr>
                        <td>Incentives</td>
                        <td class="incentives">00.00</td>
                        <!-- Deduction -->
                        <td>Others</td>
                        <td class="other_deductions">00.00</td>
                    </tr>
                    <tr>
                        <td>Commission</td>
                        <td class="commission">00.00</td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>13th Month</td>
                        <td class="thirteenth_month">00.00</td>
                        <!-- Total -->
                        <td><strong>Gross Pay</strong></td>
                        <td class="gross_pay"></td>
                    </tr>
                    <tr>
                        <td>Add Back</td>
                        <td class="add_back">00.00</td>
                        <!-- Total -->
                        <td><strong>Net Pay</strong></td>
                        <td class="net_pay"></td>
                    </tr>
                </tbody>
            </table>
        </div>        
        <div class="p-3">            
            <div class="form-group">
                <label class="required" for="notes">Notes</label>
                <textarea name="notes" id="notes" class="form-control" cols="2" rows="2" placeholder="Enter notes"></textarea>
                <small id="alert_notes" class="text-danger"></small>
            </div>
            <div class="form-group">
                <input type="hidden" id="id" name="id" readonly>
                <?php if ($can_submit): ?>
                    <button type="button" class="btn btn-success w-100 btn-submit" disabled>Submit</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>