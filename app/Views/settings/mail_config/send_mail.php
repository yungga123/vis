<?= $this->extend('templates/default'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
    <form id="form_mail_config" class="with-label-indicator" action="<?= url_to('mail.save'); ?>" method="POST" autocomplete="off">
        <?= csrf_field(); ?>
        <input type="hidden" name="mail_config_id" value="<?= $mail['mail_config_id'] ?? ''; ?>">
        <div class="row">
            <div class="col-sm-6">
                <div class="card card-primary collapsed-card">
                    <div class="card-header">
                        <h3 class="card-title">Gmail Credentials</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>         
                    <div class="card-body">
                        <?php if (is_admin()): ?>
                        <div class="form-group">
                            <label class="required" for="email_name">Email Name</label>
                            <input type="text" name="email_name" id="email_name" class="form-control" placeholder="Enter Email Name..." value="<?= $mail['email_name'] ?? ''; ?>">
                            <small id="alert_name" class="text-danger"></small>
                        </div>
                        <div class="form-group">
                            <label class="required" for="email">Email Address</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="Enter Email Address..." value="<?= $mail['email'] ?? ''; ?>">
                            <small id="alert_email" class="text-danger"></small>
                        </div>
                        <div class="form-group input-group">
                            <label class="required" for="password">App Password</label>
                            <div class="input-group">
                                <input type="password" name="password" id="password" class="form-control" placeholder="Enter App Password..." value="<?= $mail['password'] ?? ''; ?>">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" id="show_password" type="button" title="Click here to show password!"><i class="fas fa-eye"></i></button>
                                </div>
                            </div>
                            <small id="alert_password" class="text-danger"></small>
                        </div>
                        <?php else: ?>
                            <p class="text-center">You have no permission to view the details in this section.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Other Details</h3>
                    </div>         
                    <div class="card-body">
                        <?php if (is_admin()): ?>
                        <div class="form-group">
                            <label class="required" for="hostname">Host Name</label>
                            <input type="text" class="form-control" name="hostname" id="hostname" value="<?= $mail['hostname'] ?? ''; ?>" placeholder="Enter Host Name">
                            <small id="alert_hostname" class="text-danger"></small>
                        </div>
                        <?php endif; ?>
                        <div class="form-group">
                            <label for="recepients">CC Recepients</label>
                            <textarea class="form-control" name="recepients" id="recepients" rows="2" placeholder="Enter CC Recepients"><?= $mail['recepients'] ?? ''; ?></textarea>
                            <small class="text-muted">
                                If multiple emails, separate it by comma (e.g. mail@email.com, mail2@email.com). <br>
                                <strong>Someone that can receive mail copy if user will change their password.</strong>
                            </small><br>
                            <small id="alert_recepients" class="text-danger"></small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card card-secondary collapsed-card">
                    <div class="card-header">
                        <h3 class="card-title">OAuth2 Google Client</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>       
                    <div class="card-body">    
                        <?php if (is_admin()): ?>                    
                        <div class="form-group">
                            <label for="oauth_client_id">Client ID</label>
                            <textarea class="form-control" name="oauth_client_id" id="oauth_client_id" rows="2" placeholder="Enter Client ID"><?= $mail['oauth_client_id'] ?? ''; ?></textarea>
                            <small id="alert_oauth_client_id" class="text-danger"></small>
                        </div>
                        <div class="form-group">
                            <label for="oauth_client_secret">Client Secret</label>
                            <textarea class="form-control" name="oauth_client_secret" id="oauth_client_secret" rows="2" placeholder="Enter Client Secret"><?= $mail['oauth_client_secret'] ?? ''; ?></textarea>
                            <small id="alert_oauth_client_secret" class="text-danger"></small>
                        </div>
                        <div class="form-group">
                            <label for="oauth_scope">Scope</label>
                            <input type="text" class="form-control" name="oauth_scope" id="oauth_scope" value="<?= $mail['oauth_scope'] ?? ''; ?>" placeholder="Enter Scope">
                            <small id="alert_oauth_scope" class="text-danger"></small>
                        </div>
                        <div class="form-group">
                            <label for="redirect_uri">Redirect URI</label>
                            <input type="text" class="form-control" name="redirect_uri" id="redirect_uri" value="<?= $mail['redirect_uri'] ?? ''; ?>" placeholder="Enter Redirect URI">
                            <small id="alert_redirect_uri" class="text-danger"></small>
                        </div>
                        <div class="form-group">
                            <label for="access_type">Access Type</label>
                            <input type="text" class="form-control" name="access_type" id="access_type" value="<?= $mail['access_type'] ?? ''; ?>" placeholder="Enter Access Type">
                            <small id="alert_access_type" class="text-danger"></small>
                        </div>
                        <div class="form-group">
                            <a href="<?= url_to('mail.config'); ?>" 
                                class="btn btn-secondary mt-2" id="btn_getAccessToken"
                                ><i class="fas fa-yin-yang"></i> Get Access Token for OAuth2</a>
                        </div>
                        <?php else: ?>
                            <p class="text-center">You have no permission to view the details in this section.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php if ($can_save): ?>  
        <div class="row mb-4">
            <button type="submit" class="btn btn-success col-sm-12 col-md-10 offset-md-1"><i class="fas fa-check"></i> <?= is_admin() ? 'Save All Changes' : 'Save' ?></button>
        </div>
        <?php endif; ?>
    </form>
</div>
<?= $this->include('templates/loading'); ?>
<?= $this->endSection(); ?>