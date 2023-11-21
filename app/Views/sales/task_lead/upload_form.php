<!-- Modal -->
<div class="modal fade" id="modal-addfile" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Form</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="required" for="upload_files">Upload Files <span class="text-normal text-danger text-italic">(Required)</span></label>
                    <p class="text-muted text-italic text-bold">Files allowed are: Image (jpg, jpeg, png, webp) and Files (docx, xlsx, pdf, csv)</p>
                </div>
                <form id="upload_form" class="dropzone rounded border border-primary" method="post" enctype="multipart/form-data" action="<?= url_to('tasklead.booked.files.upload') ?>"> 
                    <?= csrf_field(); ?>
                    <input type="hidden" id="upload_tasklead_id" name="id" readonly>
                    <div class="fallback">
                        <input name="file" type="file" multiple />
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-success btn-upload">Submit</button>
            </div>
        </div>
    </div>
</div>