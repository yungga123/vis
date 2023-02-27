<!-- Modal -->
<div class="modal fade" id="modal_tasklead" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
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
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="employee_id">Employee ID</label>
                                <input type="text" name="employee_id" id="employee_id" class="form-control" placeholder="Enter..." value="<?= session('employee_id') ?>" readonly>
                                <small id="alert_employee_id" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="quarter">Quarter</label>
                                <input type="text" name="quarter" id="quarter" class="form-control" placeholder="Enter..." value="<?= $quarter ?>" readonly>
                                <small id="alert_quarter" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="status">Status</label>
                                <input type="text" name="status" id="status" class="form-control" placeholder="Enter..." value="10.00" readonly>
                                <small id="alert_status" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="customer_type">Customer Type</label>
                                <select name="customer_type" id="customer_type" class="form-control" placeholder="Enter...">
                                    <option value="">---Please Select---</option>
                                    <option value="Residential">Residential</option>
                                    <option value="Commercial">Commercial</option>
                                </select>
                                <small id="alert_customer_type" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="existing_customer">Existing Customer?</label>
                                <select name="existing_customer" id="existing_customer" class="form-control" placeholder="Enter...">
                                    <option value="">---Please Select---</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                                <small id="alert_existing_customer" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="customer_id">Customer</label>
                                <select name="customer_id" id="customer_id" class="form-control" placeholder="Enter..." disabled>
                                    
                                </select>
                                <small id="alert_customer_id" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="branch_id">Branch</label>
                                <select name="branch_id" id="branch_id" class="form-control" placeholder="Enter..." disabled></select>
                                <small id="alert_branch_id" class="text-danger"></small>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="project">Project</label>
                                <input type="text" name="project" id="project" class="form-control" placeholder="Enter...">
                                <small id="alert_project" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="project_amount">Project Amount</label>
                                <input type="text" name="project_amount" id="project_amount" class="form-control" placeholder="Enter...">
                                <small id="alert_project_amount" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="quotation_num">Quotation Number</label>
                                <input type="date" name="quotation_num" id="quotation_num" class="form-control" placeholder="Enter...">
                                <small id="alert_quotation_num" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="forecast_close_date">Forecast Close Date</label>
                                <input type="text" name="forecast_close_date" id="forecast_close_date" class="form-control" placeholder="Enter...">
                                <small id="alert_forecast_close_date" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="min_forecast_date">Min. Forecast Date</label>
                                <input type="text" name="min_forecast_date" id="min_forecast_date" class="form-control" placeholder="Enter...">
                                <small id="alert_min_forecast_date" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="max_forecast_date">Max Forecast Date</label>
                                <input type="text" name="max_forecast_date" id="max_forecast_date" class="form-control" placeholder="Enter..." value="">
                                <small id="alert_max_forecast_date" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="remark_next_step">Remark Next Step</label>
                                <input type="text" name="remark_next_step" id="remark_next_step" class="form-control" placeholder="Enter..." value="">
                                <small id="alert_remark_next_step" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="close_deal_date">Close Deal Date</label>
                                <input type="text" name="close_deal_date" id="close_deal_date" class="form-control" placeholder="Enter..." value="">
                                <small id="alert_close_deal_date" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="project_start_date">Project Start Date</label>
                                <input type="text" name="project_start_date" id="project_start_date" class="form-control" placeholder="Enter..." value="">
                                <small id="alert_project_start_date" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="project_finish_date">Project Finish Date</label>
                                <input type="text" name="project_finish_date" id="project_finish_date" class="form-control" placeholder="Enter..." value="">
                                <small id="alert_project_finish_date" class="text-danger"></small>
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