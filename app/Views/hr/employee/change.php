<!-- Employment Status Modal -->
<div class="modal fade" id="employment_status_modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="employment_status_form" class="with-label-indicator" action="<?= url_to('employee.change'); ?>" method="post" autocomplete="off">
                <?= csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Change Employment Status</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="callout callout-info">
                        <strong>Note:</strong> Once an employee is marked as resigned, their corresponding accounts will also be deleted!
                    </div>
                    <div class="form-group">
                        <label class="required" for="_employee_id">Employee ID</label>
                        <input type="text" name="employee_id" id="_employee_id" class="form-control" readonly>
                        <input type="hidden" id="_id" name="id" readonly>
                    </div>
                    <div class="form-group">
                        <label class="required" for="_employment_status">Employment Status</label>
                        <select type="text" class="form-control" name="employment_status" id="_employment_status">
                            <?php foreach (get_employment_status() as $val => $text): ?>
                                <option value="<?= $val ?>"><?= $text ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small id="alert__employment_status" class="form-text text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label id="label_date_resigned" for="date_resigned">Date Resigned</label>
                        <input type="date" class="form-control" name="date_resigned" id="_date_resigned">
                        <small id="alert__date_resigned" class="form-text text-danger"></small>
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