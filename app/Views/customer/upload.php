<!-- Modal -->
<div class="modal fade" id="upload_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Client Files</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label class="required" for="upload_files">Upload Files <span class="text-normal text-danger text-italic">(Required)</span></label>
                    <p class="text-muted text-italic text-bold">Files allowed are: Image (jpg, jpeg, png, webp) and Files (docx, xlsx, pdf, csv)</p>
                </div>
                <form id="upload_form" class="dropzone rounded border border-primary" method="post" enctype="multipart/form-data"> 
                    <?= csrf_field(); ?>
                    <input type="hidden" id="upload_customer_id" name="id" readonly>
                    <div class="fallback">
                        <input name="file" type="file" multiple />
                    </div>
                </form>
                <small id="alert_file" class="form-text text-danger"></small>
                <p class="text-italic text-bold mt-3">Click the file to download.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success btn-upload">Upload</button>
            </div>
        </div>
    </div>
</div>