<!-- Employment Status Modal -->
<div class="modal fade" id="manage_leave_modal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="manage_leave_form" class="with-label-indicator" action="<?= url_to('manage_leave.save'); ?>" method="post" autocomplete="off">
                <?= csrf_field(); ?>
                <input type="hidden" id="id" name="id" readonly>
                <div class="modal-header">
                    <h5 class="modal-title">Add New Leave</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="required" for="leave_type">Leave Type</label>
                        <select type="text" class="form-control" name="leave_type" id="leave_type">
                            <option value="">Select a leave type</option>
                            <?php foreach (get_leave_type() as $val => $text): ?>
                                <option value="<?= $val ?>"><?= $text ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small id="alert_leave_type" class="form-text text-danger"></small>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required" for="start_date">Start Date</label>
                                <input type="date" class="form-control" name="start_date" id="start_date" placeholder="Start Date">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required" for="end_date">End Date</label>
                                <input type="date" class="form-control" name="end_date" id="end_date" placeholder="End Date">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="required" for="leave_reason">Leave Reason</label>
                        <textarea name="leave_reason" id="leave_reason" class="form-control" cols="3" rows="3" placeholder="Enter a valid leave reason"></textarea>
                        <small id="alert_leave_reason" class="text-danger"></small>
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