<?=$this->extend('templates/default');?>
<?=$this->section('content');?>
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12 col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Working Days and Time In/Out</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>         
                <div class="card-body">
                    <form id="form_working_days_time" class="with-label-indicator" action="<?= url_to('payroll.settings.save') ?>">
                        <?= csrf_field(); ?>
                        <input type="hidden" name="rules" value="working_days" class="form-control" readonly>
                        <div class="form-group">
                            <label class="required" for="working_days">Days</label>
                            <select type="text" class="form-control" name="working_days[]" id="working_days" data-placeholder="Select working days" multiple>
                                <?php foreach (get_days() as $val => $text): ?>
                                    <option value="<?= $val ?>"><?= $text ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small id="alert_working_days" class="form-text text-danger"></small>
                        </div>
                        <div class="form-group">
                            <label class="required" for="working_time_in">Time In</label>
                            <input type="time" name="working_time_in" id="working_time_in" class="form-control">
                            <small id="alert_working_time_in" class="text-danger"></small>
                        </div>
                        <div class="form-group">
                            <label class="required" for="working_time_out">Time Out</label>
                            <input type="time" name="working_time_out" id="working_time_out" class="form-control">
                            <small id="alert_working_time_out" class="text-danger"></small>
                        </div>
                        <hr/>
                        <div class="form-group">
                            <h5>Default Leave Allotment</h5>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-6">
                                <div class="form-group">
                                    <label class="required" for="default_vacation_leave">Vacation Leave</label>
                                    <input type="number" name="default_vacation_leave" id="default_vacation_leave" class="form-control" placeholder="Ex. 6">
                                    <small id="alert_default_vacation_leave" class="text-danger"></small>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6">
                                <div class="form-group">
                                    <label class="required" for="default_sick_leave">Sick Leave</label>
                                    <input type="number" name="default_sick_leave" id="default_sick_leave" class="form-control" placeholder="Ex. 6">
                                    <small id="alert_default_sick_leave" class="text-danger"></small>
                                </div>
                            </div>
                        </div>
                        <?= $this->include('payroll/settings/button-save'); ?>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-6">
            <?= $this->include('payroll/settings/overtime'); ?>
        </div>
        <div class="col-sm-12 col-md-12">
            <?= $this->include('payroll/settings/government'); ?>
        </div>
    </div>
</div>
<?= $this->include('templates/loading'); ?>
<?=$this->endSection();?>
