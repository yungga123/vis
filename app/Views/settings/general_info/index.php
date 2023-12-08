<?=$this->extend('templates/default');?>
<?=$this->section('content');?>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12 col-md-7">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">System Info</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>         
                <div class="card-body">
                    <form id="form_system_info" action="<?= url_to('general_info.save') ?>">
                        <?= csrf_field(); ?>
                        <div class="form-group">
                            <label class="required" for="system_name">System Name</label>
                            <input type="text" name="system_name" id="system_name" class="form-control" placeholder="System Name">
                            <small id="alert_system_name" class="text-danger"></small>
                        </div>
                        <?= $this->include('settings/general_info/button-save'); ?>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-5">
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
        </div>
        <div class="col-sm-12 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Company Info</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>         
                <div class="card-body">
                    <form id="form_company_info" action="<?= url_to('general_info.save') ?>">
                        <?= csrf_field(); ?>
                        <div class="row">
                            <div class="col-sm-12 col-md-6">
                                <div class="form-group">
                                    <label class="required" for="company_name">Name</label>
                                    <input type="text" name="company_name" id="company_name" class="form-control" placeholder="Name">
                                    <small id="alert_company_name" class="text-danger"></small>
                                </div>
                                <div class="form-group">
                                    <label class="required" for="company_address">Address</label>
                                    <input type="text" name="company_address" id="company_address" class="form-control" placeholder="Addess">
                                    <small id="alert_company_address" class="text-danger"></small>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-group">
                                    <label class="required" for="company_contact_number">Contact Number</label>
                                    <input type="text" name="company_contact_number" id="company_contact_number" class="form-control" placeholder="Contact Number">
                                    <small id="alert_company_contact_number" class="text-danger"></small>
                                </div>
                                <div class="form-group">
                                    <label class="required" for="company_email_address">Email Addess</label>
                                    <input type="text" name="company_email_address" id="company_email_address" class="form-control" placeholder="Email Addess">
                                    <small id="alert_company_email_address" class="text-danger"></small>
                                </div>
                            </div>
                        </div>
                        <?= $this->include('settings/general_info/button-save'); ?>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->include('templates/loading'); ?>
<?=$this->endSection();?>
