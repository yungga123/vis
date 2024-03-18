<!-- Employment Status Modal -->
<div class="modal fade" id="customer_support_modal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="customer_support_form" class="with-label-indicator" action="<?= url_to('sales.customer_support.save'); ?>" method="post" autocomplete="off">
                <?= csrf_field(); ?>
                <input type="hidden" id="id" name="id" readonly>
                <div class="modal-header">
                    <h5 class="modal-title"><?= $btn_add_lbl ?? 'Add a Record' ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?= $this->include('customer/components/field'); ?>
                            <div class="form-group">
                                <label for="ticket_number">Ticket Number</label>
                                <input type="text" class="form-control" name="ticket_number" id="ticket_number" placeholder="Ticket Number" step="0.01">
                                <small id="alert_ticket_number" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label class="required" for="security_ict_system">Type of Security and ICT System</label>
                                <select class="form-control" name="security_ict_system" id="security_ict_system" style="width: 100%;" required>
                                    <?php foreach (get_security_ict_systems() as $val => $text): ?>
                                        <option value="<?= $val ?>"><?= $text ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small id="alert_security_ict_system" class="text-danger"></small>
                                <div class="security_ict_system_other mt-2 d-none">                                    
                                    <input type="text" class="form-control" name="security_ict_system_other" id="security_ict_system_other" placeholder="Please specify...">
                                    <small id="alert_security_ict_system_other" class="text-danger"></small>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="required" for="priority">Priority (Very Low to Very High)</label>
                                <select class="form-control" name="priority" id="priority" style="width: 100%;" required>
                                    <?php foreach (get_priorities() as $val => $text): ?>
                                        <option value="<?= $val ?>"><?= $text ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <small id="alert_priority" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label class="required" for="due_date">Due Date</label>
                                <input type="date" class="form-control" name="due_date" id="due_date" placeholder="Due Date" value="<?= current_date() ?>">
                                <small id="alert_due_date" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label class="required" for="follow_up_date">Date Need to Follow-Up?</label>
                                <input type="date" class="form-control" name="follow_up_date" id="follow_up_date" placeholder="Date Need to Follow-Up?" value="<?= current_date() ?>">
                                <small id="alert_follow_up_date" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label class="required" for="specialists">Support Specialist/s</label>
                                <select class="form-control" id="specialists" name="specialists[]" style="width: 100%;" data-placeholder="Select an specialists" multiple required></select>
                                <small id="alert_specialists" class="text-danger"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="required" for="issue">Problem/Issue Encountered?</label>
                                <textarea name="issue" id="issue" class="form-control" cols="3" rows="3" placeholder="Problem/Issue Encountered?"></textarea>
                                <small id="alert_issue" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label class="required" for="findings">Findings</label>
                                <textarea name="findings" id="findings" class="form-control" cols="3" rows="3" placeholder="Findings"></textarea>
                                <small id="alert_findings" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="action">Initial Action Taken by the Customer</label>
                                <textarea name="action" id="action" class="form-control" cols="3" rows="3" placeholder="Initial Action Taken by the Customer"></textarea>
                                <small id="alert_action" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label for="troubleshooting">Initial Troubleshooting Done?</label>
                                <textarea name="troubleshooting" id="troubleshooting" class="form-control" cols="3" rows="3" placeholder="Initial Troubleshooting Done?"></textarea>
                                <small id="alert_troubleshooting" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label class="required" for="remarks">Remarks</label>
                                <textarea name="remarks" id="remarks" class="form-control" cols="3" rows="3" placeholder="Remarks"></textarea>
                                <small id="alert_remarks" class="text-danger"></small>
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