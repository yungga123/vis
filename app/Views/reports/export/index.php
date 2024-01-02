<?=$this->extend('templates/default');?>
<?=$this->section('content');?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Filters</h3>
                </div>         
                <div class="card-body">
                    <form id="form_export" class="with-label-indicator" action="<?= url_to('export.data') ?>" method="post">
                        <?= csrf_field(); ?>
                        <div class="row">
                            <div class="col-12">
                                <p><strong>Note:</strong> <i>To avoid server error due huge data extraction, <strong>dates filter</strong>(start/end date - which is the time the records were created or inserted in the system) is/are limited to <strong>up to 6 months only</strong>!</i> If has no error encounter, check your <strong>Downloads</strong> folder or browser.</p>
                            </div>
                            <!-- Alert -->
                            <div class="col-12 mb-3">
                                <?php if (has_flashdata('success')): ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <strong>Success!</strong> <?= get_flashdata('success') ?>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                <?php endif; ?>

                                <?php if (has_flashdata('error')): ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <strong>Oops!</strong> <?= get_flashdata('error') ?>
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                <?php endif; ?>

                                <?php if (has_validation_errors()): ?>
                                    <h6 class="text-bold">Required field(s):</h6>
                                    <div class="text-danger">
                                        <?= validation_list_errors() ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-group">
                                    <label class="required" for="module">Module</label>
                                    <select class="form-control" name="module" id="module" style="width: 100%;" required>
                                        <option value="">Select a module</option>
                                        <?php if (! empty($modules)): ?>
                                            <?php foreach ($modules as $val => $text): ?>
                                                <option value="<?= $val ?>" <?= old('module') === $val ? 'selected' : '' ?>><?= $text ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <small id="alert_module" class="text-danger"></small>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-group" id="wrapper_status">
                                    <label for="status">Status</label>
                                    <select class="form-control" name="status[]" id="status" style="width: 100%;" disabled multiple>
                                    </select>
                                    <small id="alert_status" class="text-danger"></small>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-group">
                                    <label class="required" for="start_date">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" value="<?= old('start_date') ?>">
                                    <small id="alert_start_date" class="text-danger"></small>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-group">
                                    <label class="required" for="end_date">End Date</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control" value="<?= old('start_date') ?>">
                                    <small id="alert_end_date" class="text-danger"></small>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="d-flex justify-content-lg-end justify-content-sm-center">
                                    <?php if ($can_generate): ?>
                                        <button type="submit" class="btn btn-success px-5">Generate</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->include('templates/loading'); ?>
<?=$this->endSection();?>
