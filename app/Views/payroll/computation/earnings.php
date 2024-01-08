<div class="card">
    <div class="card-header">
        <h4 class="card-title text-lg">Earnings</h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label for="basic_pay">Basic Pay</label>
                    <input type="text" class="form-control" name="basic_pay" id="basic_pay" placeholder="0.00" readonly value="<?= $payroll['cutoff_pay'] ?? 0 ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="working_days">Working Days</label>
                    <input type="number" class="form-control" name="working_days" id="working_days" placeholder="Days" step="00.5" value="<?= $payroll['working_days'] ?? 0 ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="working_days_off">RD Working</label>
                    <input type="number" class="form-control" name="working_days_off" id="working_days_off" placeholder="Days" step="00.5" data-earnings value="<?= $earnings['working_days_off'] ?? '' ?>">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="over_time">Overtime</label>
                    <input type="number" class="form-control" name="over_time" id="over_time" placeholder="Hours" step="00.5" data-earnings value="<?= $earnings['over_time'] ?? '' ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="night_diff">Night Diff.</label>
                    <input type="number" class="form-control" name="night_diff" id="night_diff" placeholder="0.00" step="00.01" data-earnings value="<?= $earnings['night_diff'] ?? '' ?>">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <h5>Holidays</h5>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="regular_holiday">Regular</label>
                    <input type="number" class="form-control" name="regular_holiday" id="regular_holiday" placeholder="Days" step="00.5" data-earnings value="<?= $earnings['regular_holiday'] ?? '' ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="special_holiday">Special</label>
                    <input type="number" class="form-control" name="special_holiday" id="special_holiday" placeholder="Days" step="00.5" data-earnings value="<?= $earnings['special_holiday'] ?? '' ?>">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <h5>Leave</h5>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label for="service_incentive_leave">Service Incentive Leave (SIL)</label>
                    <input type="number" class="form-control" name="service_incentive_leave" id="service_incentive_leave" placeholder="Days" step="00.5" data-earnings value="<?= $earnings['service_incentive_leave'] ?? '' ?>">
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-12">
                <h5>Non-Taxable</h5>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                <label for="incentives">Incentives</label>
                <input type="number" class="form-control" name="incentives" id="incentives" placeholder="00.0" step="00.01" data-earnings value="<?= $earnings['incentives'] ?? '' ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                <label for="commission">Commission</label>
                <input type="number" class="form-control" name="commission" id="commission" placeholder="00.0" step="00.01" data-earnings value="<?= $earnings['commission'] ?? '' ?>">
                </div>
            </div>
            <div class="col-md-6 mt-2">
                <div class="form-group">
                <label for="thirteenth_month">13th Month</label>
                <input type="number" class="form-control" name="thirteenth_month" id="thirteenth_month" placeholder="00.0" step="00.01" data-earnings value="<?= $earnings['thirteenth_month'] ?? '' ?>">
                </div>
            </div>
            <div class="col-md-6 mt-2">
                <div class="form-group">
                    <label for="add_back">Add Back</label>
                    <input type="number" class="form-control" name="add_back" id="add_back" placeholder="00.0" step="00.01" data-earnings value="<?= $earnings['add_back'] ?? '' ?>">
                </div>
            </div>
        </div>
    </div>
</div>