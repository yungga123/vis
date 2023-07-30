<!-- Dispatch Modal -->
<div class="modal fade" id="dispatch_modal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="dispatch_form" class="with-label-indicator" action="<?= url_to('dispatch.save'); ?>" method="post" autocomplete="off">
                <?= csrf_field(); ?>
                <input type="hidden" id="dispatch_id" name="id" readonly>
                <div class="modal-header">
                    <h5 class="modal-title">Add Dispatch</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">                    
                    <div class="alert alert-info" role="alert">
                        <strong>Note:</strong> 
                        If not empty, initial dropdowns of <strong>Schedule & Client</strong> are by 10. Type the title (Schedule) & Client Name to search if not in the options and then, click to select.
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="id" id="dispatch_id" readonly>
                        <input type="hidden" name="schedule_id" id="schedule_id" readonly>
                        <label class="required" for="schedule">Schedule</label>
                        <select class="custom-select" id="schedules" style="width: 100%;">
                        </select>
                        <div class="d-none" id="orig_schedule"></div>
                        <small id="alert_schedule_id" class="text-danger"></small>
                    </div>
                    <div class="form-group schedule-details"></div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label class="required" for="customer_id">Client</label>
                                <div class="mb-2">                                    
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="customer_type" id="commercial" value="commercial" checked>
                                        <label class="form-check-label" for="commercial">Commercial</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="customer_type" id="residential" value="residential">
                                        <label class="form-check-label" for="residential">Residentail</label>
                                    </div>
                                </div>
                                <select class="form-control" id="customer_id" name="customer_id" style="width: 100%;" required></select>
                                <small id="alert_customer_id" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label class="required" for="dispatch_date">Dispatch Date</label>
                                <input type="date" name="dispatch_date" id="dispatch_date" class="form-control">
                                <small id="alert_dispatch_date" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="dispatch_out">Dispatch Out</label>
                                <input type="time" name="dispatch_out" id="dispatch_out" class="form-control">
                                <small id="alert_dispatch_out" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="time_in">Time In</label>
                                <input type="time" name="time_in" id="time_in" class="form-control">
                                <small id="alert_time_in" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="time_out">Time Out</label>
                                <input type="time" name="time_out" id="time_out" class="form-control">
                                <small id="alert_time_out" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="remarks">Remarks</label>
                                <textarea name="remarks" id="remarks" class="form-control" cols="3" rows="3" placeholder="Enter remarks"></textarea>
                                <small id="alert_remarks" class="text-danger"></small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group">
                                <label for="sr_number">SR Number</label>
                                <input type="text" name="sr_number" id="sr_number" class="form-control" placeholder="Enter sr number">
                                <small id="alert_sr_number" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label class="required" for="service_type">Service Type</label>
                                <select class="form-control" name="service_type" id="service_type" style="width: 100%;" required>
                                    <?php foreach (get_dispatch_services() as $val => $text): ?>
                                        <option value="<?= $val ?>"><?= $text ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small id="alert_service_type" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label class="required" for="comments">Comments</label>
                                <textarea name="comments" id="comments" class="form-control" cols="3" rows="3" placeholder="Enter comments"></textarea>
                                <small id="alert_comments" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label class="required" for="with_permit">With Permit?</label>
                                <select class="form-control" name="with_permit" id="with_permit" style="width: 100%;" required>
                                    <option value="No">No</option>
                                    <option value="Yes">Yes</option>
                                </select>
                                <small id="alert_with_permit" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label class="required" for="technicians">Assign Technicians</label>
                                <select class="form-control" id="technicians" multiple name="technicians[]" style="width: 100%;" required></select>
                                <small id="alert_technicians" class="text-danger"></small>
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