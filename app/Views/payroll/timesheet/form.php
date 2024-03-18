<!-- Employment Status Modal -->
<div class="modal fade" id="timesheet_modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="timesheet_form" class="with-label-indicator" action="<?= url_to('payroll.timesheet.save'); ?>" method="post" autocomplete="off">
                <?= csrf_field(); ?>
                <input type="hidden" id="id" name="id" readonly>
                <div class="modal-header">
                    <h5 class="modal-title">Add Timesheet</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="required" for="clock_date">Clock Date</label>
                        <input type="date" class="form-control" name="clock_date" id="clock_date" placeholder="Clock Date" value="<?= current_date() ?>">
                        <small id="alert_clock_date" class="text-danger"></small>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required" for="clock_in">Clock In</label>
                                <input type="time" class="form-control" name="clock_in" id="clock_in" placeholder="Clock In">
                                <small id="alert_clock_in" class="text-danger"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required" for="clock_out">Clock Out</label>
                                <input type="time" class="form-control" name="clock_out" id="clock_out" placeholder="Clock Out">
                                <small id="alert_clock_out" class="text-danger"></small>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="required" for="remark">Remarks</label>
                        <textarea name="remark" id="remark" class="form-control" cols="3" rows="3" placeholder="Enter a remarks"></textarea>
                        <small id="alert_remark" class="text-danger"></small>
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