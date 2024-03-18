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
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">General</h3>
                        </div>
                        <div class="card-body">
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
                </div>
                <div class="col-sm-12 col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Bank for Printing</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="required" for="company_bank_name">Bank Name</label>
                                <input type="text" name="company_bank_name" id="company_bank_name" class="form-control" placeholder="Bank Name">
                                <small id="alert_company_bank_name" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label class="required" for="company_bank_account_name">Account Name</label>
                                <input type="text" name="company_bank_account_name" id="company_bank_account_name" class="form-control" placeholder="Account Name">
                                <small id="alert_company_bank_account_name" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label class="required" for="company_bank_account_number">Account Number</label>
                                <input type="text" name="company_bank_account_number" id="company_bank_account_number" class="form-control" placeholder="Account Number">
                                <small id="alert_company_bank_account_number" class="text-danger"></small>
                            </div>
                            <div class="form-group">
                                <label class="required" for="company_bank_branch">Branch</label>
                                <input type="text" name="company_bank_branch" id="company_bank_branch" class="form-control" placeholder="Branch">
                                <small id="alert_company_bank_branch" class="text-danger"></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?= $this->include('settings/general_info/button-save'); ?>
        </form>
    </div>
</div>