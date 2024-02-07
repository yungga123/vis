<div class="card">
    <div class="card-header">
        <h3 class="card-title">Company Logo for Printing</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>         
    <div class="card-body">
        <p class="text-bold">Upload Logo</p>
        <form id="form_company_logo" class="dropzone rounded border border-primary" method="post" enctype="multipart/form-data" action="<?= url_to('general_info.upload') ?>"> 
            <?= csrf_field(); ?>
            <div class="fallback">
                <input name="file" type="file" />
            </div>
        </form>
        <small class="text-center">Drop file or click to upload.</small>
        <div class="d-flex justify-content-end mt-2">
            <button type="button" class="btn btn-success" id="btn_upload_logo">Save</button>
        </div>
    </div>
</div>