<!-- Generate PO Modal -->
<div class="modal fade" id="generate_PO_modal" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form id="generate_po_form" class="with-label-indicator" action="<?= url_to('generate-po.save'); ?>" method="post" autocomplete="off">
                <?= csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title">Add PO</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">        

                    
                    
                    sub_total_amount
                    mode_of_payment
                    <div class="form-group">
                        <label class="required" for="purchase_order_number">PO Number</label>
                        <input type="text" name="purchase_order_number" id="purchase_order_number" class="form-control" placeholder="Enter PO Number">
                        <small id="alert_purchase_order_number" class="text-danger"></small>
                    </div>

                    <div class="form-group">
                        <label class="required" for="purchase_order_date">PO Number</label>
                        <input type="date" name="purchase_order_date" id="purchase_order_date" class="form-control">
                        <small id="alert_purchase_order_date" class="text-danger"></small>
                    </div>

                    <div class="form-group">
                        <label class="required" for="supplier">Supplier</label>
                        <select type="text" name="supplier" id="supplier" class="form-control" placeholder="Enter PO Number">
                            <option value="">--- Please Select ---</option>
                        </select>
                        <small id="alert_supplier" class="text-danger"></small>
                    </div>

                    <div class="form-group">
                        <label class="optional" for="ship_to">Shipped To</label>
                        <input type="text" name="ship_to" id="ship_to" class="form-control" placeholder="Default value is VINCULUM TECHNOLOGIES.">
                        <small id="alert_ship_to" class="text-danger"></small>
                    </div>

                    <div class="form-group">
                        <label class="required" for="attention_to">Attention To</label>
                        <input type="text" name="attention_to" id="attention_to" class="form-control" placeholder="Enter here...">
                        <small id="alert_attention_to" class="text-danger"></small>
                    </div>

                    <div class="form-group">
                        <label class="required" for="requestor">Requestor</label>
                        <input type="text" name="requestor" id="requestor" class="form-control" placeholder="Enter here...">
                        <small id="alert_requestor" class="text-danger"></small>
                    </div>

                    <div class="form-group">
                        <label class="required" for="request_form_number">RPF No.</label>
                        <select type="text" name="request_form_number" id="request_form_number" class="form-control" placeholder="Enter PO Number">
                            <option value="">--- Please Select ---</option>
                        </select>
                        <small id="alert_request_form_number" class="text-danger"></small>
                    </div>


                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>