<!-- Employment Status Modal -->
<div class="modal fade" id="salary_rate_modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="salary_rate_form" class="with-label-indicator" action="<?= url_to('salary_rate.save'); ?>" method="post" autocomplete="off">
                <?= csrf_field(); ?>
                <input type="hidden" id="id" name="id" readonly>
                <div class="modal-header">
                    <h5 class="modal-title">Add Salary Rate</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="callout callout-info">
                        <strong>Note:</strong>
                        <p>If <strong>Employee Name</strong> is not empty, initial dropdowns are by 10. Type the <strong>Employee ID or Name</strong> to search if not in the options and then, click to select.</p>
                        <p class="mt-2">You can also select and insert multiple <strong>Employees</strong> with the same <strong>Rate Type and Salary Rate</strong>.</p>
                    </div>
                    <div class="form-group">
                        <label class="required" for="employee_id">Employee Name</label>
                        <select class="custom-select" name="employee_id" id="employee_id" style="width: 100%;" multiple>
                        </select>
                        <small id="alert_employee_id" class="form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label class="required" for="rate_type">Rate Type</label>
                        <select type="text" class="form-control" name="rate_type" id="rate_type">
                            <option value="">Select a rate type</option>
                            <?php foreach (get_salary_rate_type() as $val => $text): ?>
                                <option value="<?= $val ?>"><?= $text ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small id="alert_rate_type" class="form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label class="required" for="salary_rate">Salary Rate</label>
                        <input type="number" class="form-control" name="salary_rate" id="salary_rate" step="0.01" placeholder="Enter salary rate">
                        <small id="alert_salary_rate" class="form-text text-danger"></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>