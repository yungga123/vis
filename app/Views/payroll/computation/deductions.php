<div class="card">
    <div class="card-header">
        <h4 class="card-title text-lg">Deductions</h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="absent">Absent</label>
                    <input type="number" class="form-control" name="absent" id="absent" placeholder="Days" step="00.5" data-deductions value="<?= $deductions['days_absent'] ?? '' ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="tardiness">Tardiness</label>
                    <input type="number" class="form-control" name="tardiness" id="tardiness" placeholder="Hours" step="00.5" data-deductions value="<?= $deductions['hours_late'] ?? '' ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="additional_rest_day">Additional Rest Day</label>
                    <input type="number" class="form-control" name="additional_rest_day" id="additional_rest_day" placeholder="Days" step="00.5" data-deductions value="<?= $deductions['addt_rest_days'] ?? '' ?>">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <h5>Government</h5>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="sss">SSS</label>
                    <input type="number" class="form-control" name="sss" id="sss" placeholder="00.0" step="00.01" data-deductions value="<?= $deductions['govt_sss'] ?? '' ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="pagibig">Pag-ibig</label>
                    <input type="number" class="form-control" name="pagibig" id="pagibig" placeholder="00.0" step="00.01" data-deductions value="<?= $deductions['govt_pagibig'] ?? '' ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="philhealth">Philhealth</label>
                    <input type="number" class="form-control" name="philhealth" id="philhealth" placeholder="00.0" step="00.01" data-deductions value="<?= $deductions['govt_philhealth'] ?? '' ?>">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="withholding_tax">Withholding Tax</label>
                    <input type="number" class="form-control" name="withholding_tax" id="withholding_tax" placeholder="00.0" step="00.01" data-deductions value="<?= $deductions['withholding_tax'] ?? '' ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="cash_advance">Cash Advance</label>
                    <input type="number" class="form-control" name="cash_advance" id="cash_advance" placeholder="00.0" step="00.01" data-deductions value="<?= $deductions['cash_advance'] ?? '' ?>">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="other_deductions">Others</label>
                    <input type="number" class="form-control" name="other_deductions" id="other_deductions" placeholder="00.0" step="00.01" data-deductions value="<?= $deductions['others'] ?? '' ?>">
                </div>
            </div>
        </div>
    </div>
</div>