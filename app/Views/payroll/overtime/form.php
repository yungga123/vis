<!-- Overtime Modal -->
<div class="modal fade" id="overtime_modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="overtime_form" class="with-label-indicator" action="<?= url_to('payroll.overtime.save'); ?>" method="post" autocomplete="off">
                <?= csrf_field(); ?>
                <input type="hidden" id="id" name="id" readonly>
                <div class="modal-header">
                    <h5 class="modal-title">File Overtime</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="required" for="date">Date</label>
                        <input type="date" class="form-control" name="date" id="date" placeholder="Date" value="<?= current_date() ?>">
                        <small id="alert_date" class="text-danger"></small>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required" for="time_start">Time Start</label>
                                <input type="time" class="form-control" name="time_start" id="time_start" placeholder="Time Start">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required" for="time_end">Time End</label>
                                <input type="time" class="form-control" name="time_end" id="time_end" placeholder="ETime End">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="required" for="reason">Reason</label>
                        <textarea name="reason" id="reason" class="form-control" cols="3" rows="3" placeholder="Enter a valid reason"></textarea>
                        <small id="alert_reason" class="text-danger"></small>
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