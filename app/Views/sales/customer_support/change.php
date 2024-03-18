<!-- Change Status Modal -->
<div class="modal fade" id="customer_support_change_modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="customer_support_change_form" class="with-label-indicator" action="<?= url_to('sales.customer_support.change'); ?>" method="post" autocomplete="off">
                <?= csrf_field(); ?>
                <input type="hidden" id="_id" name="id" readonly>
                <div class="modal-header">
                    <h5 class="modal-title">Change Status</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="required" for="status">Status</label>
                        <select class="form-control" name="status" id="status" style="width: 100%;" required>
                            <?php foreach (get_customer_support_status() as $val => $text): ?>
                                <option value="<?= $val ?>"><?= $text ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small id="alert_status" class="text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label class="required" for="_remarks">Remarks</label>
                        <textarea name="remarks" id="_remarks" class="form-control" cols="3" rows="3" placeholder="Remarks"></textarea>
                        <small id="alert__remarks" class="text-danger"></small>
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