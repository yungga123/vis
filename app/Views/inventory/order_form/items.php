<!-- PRF Items Modal -->
<div class="modal fade" id="order_form_items_modal" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form id="order_form_file_form" class="with-label-indicator" action="<?= url_to('inventory.order_form.change'); ?>" method="post" autocomplete="off">
            <?= csrf_field(); ?>
            <input type="hidden" id="_id" name="id" readonly>
            <input type="hidden" id="_status" name="status" readonly>
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Order Form Items</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5 class="text-center text-bold" id="order_form_id_text"></h5>
                    <div class="table-responsive">
                        <table class="table table-hover" id="order_form_items_table">
                            <thead>
                                <tr>
                                    <th>Item #</th>
                                    <th>Supplier</th>
                                    <th>Item Model</th>
                                    <th>Item Description</th>
                                    <th>Item Unit</th>
                                    <th>Item Size</th>
                                    <th>Current Stocks</th>
                                    <th>Item Price</th>
                                    <th>Quantity</th>
                                    <th>Discount</th>
                                    <th>Total Price</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="7" class="text-right text-bold">Grand Totals</td>
                                    <td class="total_item_price text-bold text-danger"></td>
                                    <td class="total_quantity text-bold text-danger"></td>
                                    <td class="total_discount text-bold text-danger"></td>
                                    <td class="grand_total text-bold text-danger"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <p class="mt-4 text-center" id="note_item_out"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <div class="change-btn"></div>        
                </div>
            </div>
        </form>
    </div>
</div>