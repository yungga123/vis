<!-- Schedule Modal -->
<div class="modal fade" id="schedule_modal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="schedule_form" class="with-label-indicator" action="<?= url_to('schedule.save'); ?>" method="post" autocomplete="off">
                <?= csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">                 
                    <div class="callout callout-info">
                        <strong>Note:</strong> 
                        If not empty, initial dropdowns of <strong>Job Order</strong> are by 10. Type the <strong>ID or Client Name</strong> to search if not in the options and then, click to select.
                    </div>
                    <!-- Hidden start and end date inputs -->
                    <input type="hidden" name="id" id="schedule_id" class="form-control" readonly>
                    <input type="hidden" name="start" id="start" class="form-control" readonly>
                    <input type="hidden" name="end" id="end" class="form-control" readonly>
                    <div class="form-group">
                        <label for="job_order_id">Job Order</label>
                        <div>Format: ID | Client Name</div>
                        <select class="custom-select" name="job_order_id" id="job_order_id" style="width: 100%;">
                        </select>
                        <div class="d-none" id="orig_schedule"></div>
                        <small id="alert_job_order_id" class="text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label class="required" for="date_range">Date & Time Range</label>
                        <input type="text" name="date_range" id="date_range" class="form-control" placeholder="Select date & time range">
                        <small id="alert_date_range" class="text-danger"></small>                        
                    </div> 
                    <div class="form-group">
                        <label class="required" for="title">Title</label>
                        <input type="text" name="title" id="title" class="form-control" placeholder="Enter title">
                        <small id="alert_title" class="text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label class="required" for="description">Description</label>
                        <textarea name="description" id="description" class="form-control" cols="3" rows="4" placeholder="Enter description"></textarea>
                        <small id="alert_description" class="text-danger"></small>
                    </div>
                    <div class="form-group">
                        <label class="required" for="type">Schedule Type</label>
                        <select class="form-control" name="type" id="type" style="width: 100%;" required>
                            <?php foreach (get_schedule_type() as $key => $val): ?>
                                <option value="<?= $key ?>"><?= $val['text'] ?></option>
                            <?php endforeach; ?>
                        </select>
                        <small id="alert_type" class="text-danger"></small>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <div>
                        <button type="button" class="btn btn-danger d-none" id="btn_delete" onclick="remove()">Delete</button>
                    </div>
                    <div>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>                        
                        <button type="submit" class="btn btn-success">Save Changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>