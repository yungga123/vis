<!-- Form -->
<form id="funds_form" class="with-label-indicator d-none" action="<?= url_to('finance.funds.save'); ?>" method="post" autocomplete="off">
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">Release Fund</h3>
        </div>
        <div class="card-body">
            <?= csrf_field(); ?>
            <input type="hidden" id="current_funds" name="current_funds" value="<?= $funds ?>" readonly>
            <div class="form-group">
                <label class="required" for="expenses">Purpose</label>
                <select class="form-control" name="expenses" id="expenses" style="width: 100%;" required>
                    <?php foreach (get_expenses() as $val => $text): ?>
                        <option value="<?= $val ?>"><?= $text ?></option>
                    <?php endforeach; ?>
                </select>
                <small id="alert_expenses" class="text-danger"></small>
            </div>
            <div class="form-group">
                <label class="required" for="transaction_amount">Amount</label>
                <input type="number" class="form-control" name="transaction_amount" id="transaction_amount" placeholder="Amount" step="0.01">
                <small id="alert_transaction_amount" class="text-danger"></small>
            </div>
            <div class="form-group">
                <label for="remarks">Remarks</label>
                <textarea name="remarks" id="remarks" class="form-control" cols="3" rows="3" placeholder="Enter remarks"></textarea>
                <small id="alert_remarks" class="text-danger"></small>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-secondary mr-1" onclick="_toggleForm()">Close</button>
                <button type="submit" class="btn btn-success">Submit</button>
            </div>
        </div>
    </div>
</form>