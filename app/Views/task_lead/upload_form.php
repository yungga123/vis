<!-- Modal -->
<div class="modal fade" id="modal-addfile" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <!-- <form id="form_tasklead" action="</?= url_to('tasklead.upload'); ?>" method="post" autocomplete="off"> -->
            <?= form_open_multipart(url_to('tasklead.booked.upload'),["id" => "form_upload"]) ?>
                <?= csrf_field(); ?>
                
                <div class="modal-header">
                    <h5 class="modal-title">Upload Form</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                    <input type="hidden" id="tasklead_id" name="upload_id" readonly>
                        <div class="col-sm-12">
                            <?= form_upload('project_file', '', ["id" => "project_file"]); ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Save Changes</button>
                </div>
            <?= form_close() ?>
        </div>
    </div>
</div>