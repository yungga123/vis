<!-- Leave Status Modal -->
<div class="modal fade" id="status_modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="status_form" class="with-label-indicator" action="<?= url_to('payroll.overtime.change'); ?>" method="post" autocomplete="off">
                <?= csrf_field(); ?>
                <input type="hidden" id="_id" name="id" readonly>
                <input type="hidden" id="status" name="status" readonly>
                <div class="modal-header">
                    <h5 class="modal-title">Change Overtime Status</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="remark">Remark</label>
                        <textarea name="remark" id="remark" class="form-control" cols="3" rows="3" placeholder="Enter remark"></textarea>
                        <small id="alert_remark" class="text-danger"></small>
                    </div>
                    <div class="form-group with_pay">
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" name="with_pay" id="with_pay" value="1">
                            <label for="with_pay" class="custom-control-label">Is Overtime With Pay?</label>
                        </div>
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