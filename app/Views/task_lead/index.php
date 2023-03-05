<?= $this->extend('templates/default'); ?>
<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <input type="hidden" id="edit_url" value="<?= url_to('tasklead.edit'); ?>" disabled>
            <input type="hidden" id="remove_url" value="<?= url_to('tasklead.delete'); ?>" disabled>
            <input type="hidden" id="get_customervt_url" value="<?= url_to('tasklead.getcustomervt'); ?>" disabled>
            <input type="hidden" id="get_customervtbranch_url" value="<?= url_to('tasklead.getcustomervtbranch'); ?>" disabled>
            <input type="hidden" id="get_customerresidential" value="<?= url_to('tasklead.getcustomerresidential'); ?>" disabled>
            <input type="hidden" id="get_quotation_num" value="<?= isset($quotation_num) ? $quotation_num : "" ?>">
            <input type="hidden" id="get_employee_id" value="<?= session('employee_id') ?>">

            <table id="tasklead_table" class="table table-bordered table-striped nowrap" data-url="<?= url_to('tasklead.list'); ?>">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Tasklead ID</th>
                        <th>Employee Name</th>
                        <th>Quarter</th>
                        <th>Status</th>
                        <th>Status Percent</th>
                        <th>Customer Type</th>
                        <th>Customer Name</th>
                        <th>Branch Name</th>
                        <th>Contact Number</th>
                        <th>Project</th>
                        <th>Amount</th>
                        <th>Qtn Number</th>
                        <th>Forecast Close Date</th>
                        <th>Min. Forecast</th>
                        <th>Max Forecast</th>
                        <th>Hit?</th>
                        <th>Remark Next Step</th>
                        <th>Close Deal Date</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Duration</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="card-footer">
            <div class="float-right">
                <a class="btn btn-success" href="<?= url_to('tasklead.booked.home') ?>">View Booked Taskleads</a>
            </div>
        </div>
    </div>
</div>
<?= $this->include('task_lead/tasklead_form'); ?>
<?= $this->include('templates/loading'); ?>
<?= $this->endSection(); ?>