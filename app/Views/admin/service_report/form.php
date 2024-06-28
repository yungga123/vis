<!-- Job Order Modal -->
<div class="modal fade" id="service_report_modal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="service_report_form" class="with-label-indicator" action="<?= url_to('admin.service_report.save'); ?>" method="post" autocomplete="off">
                <?= csrf_field(); ?>
                <input type="hidden" name="id" id="id" readonly>

                <div class="modal-header">
                    <h5 class="modal-title">Add Job Order</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">                    
                    <div class="callout callout-info">
                        <strong>Note:</strong> 
                        If not empty, initial dropdowns of <strong>Job Order</strong> is by 10. Type the <strong>CLIENT NAME or ID</strong> to search if not in the options and then, click to select.
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <?= $this->include('admin/job_order/components/field'); ?>
                            <div class="form-group">
                                <label class="required" for="area">Site Area</label>
                                <select class="form-control" name="area" id="area" style="width: 100%;" required>
                                    <option value="Customer Site">Customer Site</option>
                                    <option value="In-house">In-house</option>
                                </select>
                                <small id="alert_area" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label class="required" for="service_type">Service Type</label>
                                <select class="form-control" name="service_type" id="service_type" style="width: 100%;" required>
                                    <?php foreach (get_service_types() as $val => $text): ?>
                                        <option value="<?= $val ?>"><?= $text ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small id="alert_service_type" class="text-danger"></small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="row">
                                <div class="col-6 form-group">
                                    <label class="required" for="arrival_date">Arrival Date</label>
                                    <input type="date" name="arrival_date" id="arrival_date" class="form-control" value="<?= current_date() ?>">
                                </div>
                                <div class="col-6 form-group">
                                    <label class="required" for="arrival_time">Arrival Time</label>
                                    <input type="time" name="arrival_time" id="arrival_time" class="form-control">
                                </div>
                                <div class="col-12">
                                    <small id="alert_date_arrival_date" class="text-danger"></small>
                                    <small id="alert_date_arrival_time" class="text-danger"></small>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="serial_number">Serial Number</label>
                                <input type="text" name="serial_number" id="serial_number" class="form-control" placeholder="Serial Number">
                                <small id="alert_serial_number" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label class="required" for="server_type">Server Type</label>
                                <input type="text" name="server_type" id="server_type" class="form-control" placeholder="Server Type">
                                <small id="alert_server_type" class="text-danger"></small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6"> 
                            <div class="form-group">
                                <label class="required" for="error_description">Error Description</label>
                                <textarea name="error_description" id="error_description" class="form-control" cols="3" rows="3" placeholder="Enter error description"></textarea>
                                <small id="alert_error_description" class="text-danger"></small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label class="required" for="corrective_action">Corrective Action</label>
                                <textarea name="corrective_action" id="corrective_action" class="form-control" cols="3" rows="3" placeholder="Enter corrective action"></textarea>
                                <small id="alert_corrective_action" class="text-danger"></small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label for="remarks">Remarks</label>
                                <textarea name="remarks" id="remarks" class="form-control" cols="3" rows="3" placeholder="Enter remarks"></textarea>
                                <small id="alert_remarks" class="text-danger"></small>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="required text-center">Unit Condition</label>
                            </div>
                            <div class="table-responsive">
                                <table class="table unit-condition">
                                    <thead>
                                        <tr class="text-bold">
                                            <td>ITEMS</td>
                                            <td>STATUS</td>
                                            <td>If defective please specify:</td>
                                            <td>Button</td>
                                        </tr>
                                    </thead>
                                    <tbody>  
                                        <tr id="row_0">
                                            <td class="item">
                                                <select class="custom-select item" name="item[]" style="width: 100%;">
                                                    <option selected value="">Please choose an option</option>
                                                    <?php foreach (get_unit_condition_items() as $val => $text): ?>
                                                        <option value="<?= $val ?>"><?= $text ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td class="item_status">
                                                <select class="form-control" name="item_status[]" style="width: 100%;" required>
                                                    <option value="GOOD">GOOD</option>
                                                    <option value="DEFECTIVE">DEFECTIVE</option>
                                                </select>
                                            </td>
                                            <td class="defective_description">
                                                <input type="text" name="defective_description[]" class="form-control defective_description" placeholder="Specify">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-success" onclick="toggleItemField()" title="Add new item field">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <hr>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                <label>Items</label>
                            </div>
                            <div class="table-responsive">
                                <table class="table items">
                                    <thead>
                                        <tr class="text-bold">
                                            <td>ITEM NO.</td>
                                            <td>DESCRIPTION</td>
                                            <td>QTY</td>
                                            <td>UNIT PRICE</td>
                                            <td>TOTAL PRICE</td>
                                            <td>Button</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr id="row_0">
                                            <td class="item_no">
                                                <input type="text" name="item_no[]" class="form-control item_no" placeholder="Item No.">
                                            </td>
                                            <td class="item_description">
                                                <input type="text" name="item_description[]" class="form-control item_description" placeholder="Description">
                                            </td>
                                            <td class="item_qty">
                                                <input type="text" name="item_qty[]" class="form-control item_qty" placeholder="Qty" onkeyup="calculate(this.value, 'row_0', 'item_unit_price')">
                                            </td>
                                            <td class="item_unit_price">
                                                <input type="number" name="item_unit_price[]" class="form-control item_unit_price" placeholder="Unit price" step="0.01" onkeyup="calculate(this.value, 'row_0', 'item_qty')">
                                            </td>
                                            <td class="item_total_price">
                                                <input type="number" name="item_total_price[]" class="form-control item_total_price" placeholder="Total price" step="0.01" readonly>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-success" onclick="toggleItemField(0, 'items')" title="Add new item field">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr class="text-bold">
                                            <td colspan="2" class="text-right">Grand Totals</td>
                                            <td class="total_qty text-danger"></td>
                                            <td class="total_price text-danger"></td>
                                            <td class="grand_total text-danger"></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <hr>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-4">
                            <div class="form-group">
                                <label class="required" for="labor">Labor (Hours)</label>
                                <input type="number" class="form-control" name="labor" id="labor" placeholder="0.00" step="0.01">
                                <small id="alert_labor" class="text-danger"></small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="required" for="travel_time">Travel Time (Hours)</label>
                                <input type="number" class="form-control" name="travel_time" id="travel_time" placeholder="0.00" step="0.01">
                                <small id="alert_travel_time" class="text-danger"></small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label class="required" for="time_out">Time Out</label>
                                <input type="time" class="form-control" name="time_out" id="time_out">
                                <small id="alert_time_out" class="text-danger"></small>
                            </div>
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