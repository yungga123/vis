<!-- Logs Modal -->
<div class="modal fade" id="customer_support_logs_modal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="customer_support_logs_form" class="with-label-indicator" action="<?= url_to('sales.customer_support_logs.save'); ?>" method="post" autocomplete="off">
                <?= csrf_field(); ?>
                <input type="hidden" id="logs_id" name="id" readonly>
                <div class="modal-header">
                    <h5 class="modal-title">View Logs</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form">
                        <div class="form-group">
                            <label class="required" for="findings">Findings</label>
                            <textarea name="findings" id="logs_findings" class="form-control" cols="3" rows="3" placeholder="Findings"></textarea>
                            <small id="alert_logs_findings" class="text-danger"></small>
                        </div>
                        <div class="form-group">
                            <label for="action">Initial Action Taken by the Customer</label>
                            <textarea name="action" id="logs_action" class="form-control" cols="3" rows="3" placeholder="Initial Action Taken by the Customer"></textarea>
                            <small id="alert_logs_action" class="text-danger"></small>
                        </div>
                        <div class="form-group">
                            <label for="troubleshooting">Initial Troubleshooting Done?</label>
                            <textarea name="troubleshooting" id="logs_troubleshooting" class="form-control" cols="3" rows="3" placeholder="Initial Troubleshooting Done?"></textarea>
                            <small id="alert_logs_troubleshooting" class="text-danger"></small>
                        </div>
                    </div>
                    <div class="logs d-none">
                        <div class="table-responsive mb-4">
                            <h4 class="text-center">Details</h4>
                            <table class="table table-striped record">
                                <thead>
                                    <tr>
                                        <th>Client</th>
                                        <th>Client Branch</th>
                                        <th>Ticket Number</th>
                                        <th>Security and ICT System</th>
                                        <th>Priority</th>
                                        <th>Due Date</th>
                                        <th>Follow Up Date</th>
                                        <th>Problem/Issue</th>
                                        <th>Remarks</th>
                                        <th>Support Specialist/s</th>
                                        <th>Created By</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div class="table-responsive">
                            <h4 class="text-center">Logs</h4>
                            <table class="table table-striped logs">
                                <thead>
                                    <tr>
                                        <th>Findings</th>
                                        <th>Actions</th>
                                        <th>Troubleshooting</th>
                                        <th>Logged By</th>
                                        <th>Logged At</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
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