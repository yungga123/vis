<!-- Modal -->
<div class="modal fade" id="modal_tasklead" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="form_tasklead" action="<?= url_to('tasklead.save'); ?>" method="post" autocomplete="off">
                <?= csrf_field(); ?>
                <input type="hidden" id="tasklead_id" name="id" readonly>
                <div class="modal-header">
                    <h5 class="modal-title">Add New Item</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <span class="tasklead"></span>
                            <div class="form-group employee_id" hidden>
                                <label for="employee_id">Employee ID</label>
                                <input type="text" name="employee_id" id="employee_id" class="form-control" placeholder="Enter..." value="<?= session('employee_id') ?>" readonly>
                                <small id="alert_employee_id" class="text-danger"></small>
                            </div>
                            <div class="form-group quarter" hidden>
                                <label for="quarter">Quarter</label>
                                <select type="text" name="quarter" id="quarter" class="form-control">
                                    <option value="1" <?= ($quarter==1) ? "selected" : "" ?>>1st Quarter</option>
                                    <option value="2" <?= ($quarter==2) ? "selected" : "" ?>>2nd Quarter</option>
                                    <option value="3" <?= ($quarter==3) ? "selected" : "" ?>>3rd Quarter</option>
                                    <option value="4" <?= ($quarter==4) ? "selected" : "" ?>>4th Quarter</option>
                                </select>
                                <small id="alert_quarter" class="text-danger"></small>
                            </div>
                            <div class="form-group status" hidden>
                                <label for="status">Status</label>
                                <input name="status" id="status" class="form-control" placeholder="Enter..." hidden>
                                <input class="form-control status_text" type="text" readonly>
                                <small id="alert_status" class="text-danger"></small>
                            </div>
                            <div class="form-group customer_type" hidden>
                                <label for="customer_type">Customer Type</label>
                                <select name="customer_type" id="customer_type" class="form-control" placeholder="Enter...">
                                    <option value="">---Please Select---</option>
                                    <option value="Residential">Residential</option>
                                    <option value="Commercial">Commercial</option>
                                </select>
                                <small id="alert_customer_type" class="text-danger"></small>
                            </div>
                            <div class="form-group existing_customer" hidden>
                                <label for="existing_customer">Existing Customer?</label>
                                <select name="existing_customer" id="existing_customer" class="form-control" placeholder="Enter...">
                                    <option value="">---Please Select---</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                                <small id="alert_existing_customer" class="text-danger"></small>
                            </div>
                            <div class="form-group customer_id" hidden>
                                <label for="customer_id">Customer</label>
                                <select name="customer_id" id="customer_id" class="form-control" placeholder="Enter..." disabled style="width: 100%;">
                                    
                                </select>
                                <small id="alert_customer_id" class="text-danger"></small>
                            </div>
                            <div class="form-group branch_id" hidden>
                                <label for="branch_id">Branch</label>
                                <select name="branch_id" id="branch_id" class="form-control" placeholder="Enter..." disabled></select>
                                <small id="alert_branch_id" class="text-danger"></small>
                            </div>
                            <div class="form-group project" hidden>
                                <label for="project">Project</label>
                                <input type="text" name="project" id="project" class="form-control" placeholder="Enter...">
                                <small id="alert_project" class="text-danger"></small>
                            </div>
                            <div class="form-group project_amount" hidden>
                                <label for="project_amount">Project Amount</label>
                                <input type="text" name="project_amount" id="project_amount" class="form-control" placeholder="Enter...">
                                <small id="alert_project_amount" class="text-danger"></small>
                            </div>
                            <div class="form-group quotation_num" hidden>
                                
                                <label for="quotation_num">Quotation Number</label>
                                <select class="form-control" id="quotation_type">
                                    <option value="">--Select Quotation Type---</option>
                                    <?php foreach (get_quotation_type() as $val => $text): ?>
                                        <option value="<?= $val ?>"><?= $text ?></option>
                                    <?php endforeach; ?>
                                </select>

                                <select class="form-control mt-2" id="quotation_color">
                                    <option value="">--Select Quotation Color---</option>
                                    <?php foreach (get_quotation_color() as $val => $text): ?>
                                        <option value="<?= $val ?>"><?= $text ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="text" name="quotation_num" id="quotation_num" class="form-control mt-2" placeholder="Enter..." readonly>
                                <input type="hidden" name="tasklead_type" id="tasklead_type" class="form-control" placeholder="Enter..." readonly>
                                <small id="alert_quotation_num" class="text-danger"></small>
                            </div>
                            <div class="form-group forecast_close_date" hidden>
                                <label for="forecast_close_date">Forecast Close Date</label>
                                <input type="date" name="forecast_close_date" id="forecast_close_date" class="form-control" placeholder="Enter...">
                                <small id="alert_forecast_close_date" class="text-danger"></small>
                            </div>
                            <div class="form-group remark_next_step" hidden>
                                <label for="remark_next_step">Remark Next Step</label>
                                <textarea name="remark_next_step" id="remark_next_step" class="form-control" placeholder="Enter..."></textarea>
                                <small id="alert_remark_next_step" class="text-danger"></small>
                            </div>
                            <div class="form-group close_deal_date" hidden>
                                <label for="close_deal_date">Close Deal Date</label>
                                <input type="date" name="close_deal_date" id="close_deal_date" class="form-control" placeholder="Enter..." value="">
                                <small id="alert_close_deal_date" class="text-danger"></small>
                            </div>
                            <div class="form-group project_start_date" hidden>
                                <label for="project_start_date">Project Start Date</label>
                                <input type="date" name="project_start_date" id="project_start_date" class="form-control" placeholder="Enter..." value="">
                                <small id="alert_project_start_date" class="text-danger"></small>
                            </div>
                            <div class="form-group project_finish_date" hidden>
                                <label for="project_finish_date">Project Finish Date</label>
                                <input type="date" name="project_finish_date" id="project_finish_date" class="form-control" placeholder="Enter..." value="">
                                <small id="alert_project_finish_date" class="text-danger"></small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <select id="change_tasklead" class="form-control">
                        <option value="">---Change Lead---</option>
                        <option value="10.00">IDENTIFIED (10%)</option>
                        <option value="30.00">QUALIFIED (30%)</option>
                        <option value="50.00">DEVELOPED SOLUTION (50%)</option>
                        <option value="70.00">EVALUATION (70%)</option>
                        <option value="90.00">NEGOTIATION (90%)</option>
                    </select>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>