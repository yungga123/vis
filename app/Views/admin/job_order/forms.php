<!-- Job Order Modal -->
<div class="modal fade" id="job_order_modal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="job_order_form" class="with-label-indicator" action="<?= url_to('job_order.save'); ?>" method="post" autocomplete="off">
                <?= csrf_field(); ?>
                <input type="hidden" id="job_order_id" name="id" readonly>
                <div class="modal-header">
                    <h5 class="modal-title">Add Job Order</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">                    
                    <div class="alert alert-info" role="alert">
                        <strong>Note for Quotation Number:</strong> 
                        If not empty, initial dropdowns are by 10. Type the number to search if not in the options and then, click to select.
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="id" id="job_order_id" readonly>
                        <input type="hidden" name="tasklead_id" id="tasklead_id" readonly>
                        <input type="hidden" name="quotation" id="quotation" readonly>
                        <input type="hidden" name="employee_id" id="employee_id" readonly>
                        <label class="required" for="select2Quotation">Quotation Number</label>
                        <select class="custom-select select2" id="select2Quotation" style="width: 100%;">
                        </select>
                        <div class="d-none" id="orig_qn"></div>
                        <small id="alert_quotation" class="text-danger"></small>
                    </div>
                    <div class="form-group q-details"></div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="required" for="date_requested">Date Requested</label>
                                <input type="date" name="date_requested" id="date_requested" class="form-control">
                                <small id="alert_date_requested" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="date_committed">Date Committed</label>
                                <input type="date" name="date_committed" id="date_committed" class="form-control">
                                <small id="alert_date_committed" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label class="required" for="date_reported">Date Reported</label>
                                <input type="date" name="date_reported" id="date_reported" class="form-control">
                                <small id="alert_date_reported" class="text-danger"></small>
                            </div>                          
                            <div class="form-group">
                                <label class="required" for="work_type">Work Type</label>
                                <select class="form-control" name="work_type" id="work_type" style="width: 100%;" required>
                                    <?php foreach (get_work_type() as $val => $text): ?>
                                        <option value="<?= $val ?>"><?= $text ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small id="alert_work_type" class="text-danger"></small>
                            </div>
                        </div>
                        <div class="col-6">  
                            <div class="form-group">
                                <label class="required" for="comments">Comments</label>
                                <textarea name="comments" id="comments" class="form-control" cols="3" rows="3" placeholder="Enter comments"></textarea>
                                <small id="alert_comments" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label class="required" for="warranty">Warranty?</label>
                                <select class="form-control" name="warranty" id="warranty" style="width: 100%;" required>
                                    <option value="No">No</option>
                                    <option value="Yes">Yes</option>
                                </select>
                                <small id="alert_warranty" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="created_by">Requested By</label>
                                <input type="text" name="created_by" id="created_by" class="form-control" placeholder="Requested By" value="<?= session('name') ?>" readonly>
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

<!-- Status Modal -->
<div class="modal fade" id="status_modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="status_form" class="with-label-indicator" action="<?= url_to('job_order.status'); ?>" method="post" autocomplete="off">
                <?= csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="required" for="job_order_id_status">Job Order ID</label>
                        <input type="text" name="id" id="job_order_id_status" class="form-control" readonly>
                        <input type="hidden" name="status" id="status" class="form-control" readonly>
                        <input type="hidden" name="is_form" value="true" class="form-control" readonly>
                    </div>
                    <div class="d-none" id="fields_accept">                        
                        <div class="form-group">
                            <label class="required" for="date_committed_status">Date Committed</label>
                            <input type="date" name="date_committed" id="date_committed_status" class="form-control" required>
                            <small id="alert_date_committed_status" class="text-danger"></small>
                        </div>
                        <div class="form-group">
                            <label class="required" for="employee_id">Manager / Incharge</label>
                            <select class="custom-select select2" name="employee_id" id="employee_id_status" placeholder="Please a manager" style="width: 100%;">
                                <?php if (! empty(get_employees())): ?>
                                <?php foreach (get_employees() as $val): ?>
                                    <option value="<?= $val['employee_id'] ?>"><?= $val['employee_name'] ?></option>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="type">Type</label>
                            <input type="text" name="type" id="type" class="form-control" readonly>
                        </div>
                    </div>
                    <div class="d-none" id="fields_file">
                        <div class="form-group">
                            <label class="required" for="remarks">Remarks</label>
                            <textarea name="remarks" id="remarks" class="form-control" cols="3" rows="3" placeholder="Enter remarks"></textarea>
                            <small id="alert_remarks" class="text-danger"></small>
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