<!-- Modal -->
<div class="modal fade" id="modal_inventory_logs" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form_inventory_logs" class="with-label-indicator" action="<?= url_to('inventory.logs.save'); ?>" method="post" autocomplete="off">
                <?= csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title">New Item In</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="inventory_id" id="inventory_parent_id" readonly>
                    <input type="hidden" id="action_logs" name="action_logs" readonly>
                    <input type="hidden" id="parent_stocks_logs" name="parent_stocks" readonly>
                    <div class="item-details-wrapper"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>