<?=$this->extend('templates/default');?>
<?=$this->section('content');?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-3 col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Legend</h3>
                </div>
                <div class="card-body">
                    <?= $type_legend ?>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Note</h3>
                </div>
                <div class="card-body">
                    <p>To <strong>EDIT</strong> or <strong>DELETE</strong> a schedule, click the event first in the calendar.</p>
                    <div class="text-center">
                         <a href="<?= url_to('schedule.export'); ?>" class="btn btn-success">Export All Schedules</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-9 col-sm-12">                    
            <div class="card">
                <div class="card-body p-0">
                    <!-- THE CALENDAR -->
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->include('admin/schedule/form'); ?>
<?= $this->include('templates/loading'); ?>
<?=$this->endSection();?>
