<?=$this->extend('templates/default');?>
<?=$this->section('content');?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">                    
            <div class="card">
				<div class="card-header">
                    <div class="mr-2 mb-2">
                        <strong>Filters by Status, Bill Type or Payment Method: </strong>
                    </div>
                    <div class="d-flex flex-md-row flex-column align-items-md-center">
                        <div class="mr-2 flex-fill mb-2 mb-md-0">
                            <select class="custom-select select2" id="filter_status" data-placeholder="Select a status" multiple style="width: 100%;">
                                <?php foreach (get_customer_support_status() as $val => $text): ?>
                                    <option value="<?= $val ?>"><?= $text ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mr-2 flex-fill mb-2 mb-md-0">
                            <select class="custom-select select2" id="filter_security_ict_system" data-placeholder="Select an option" multiple style="width: 100%;">
                                <?php foreach (get_security_ict_systems() as $val => $text): ?>
                                    <option value="<?= $val ?>"><?= $text ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mr-2 flex-fill mb-2 mb-md-0">
                            <select class="custom-select select2" id="filter_priority" data-placeholder="Select a priority" multiple style="width: 100%;">
                                <?php foreach (get_priorities() as $val => $text): ?>
                                    <option value="<?= $val ?>"><?= $text ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="align-items-center justify-content-center d-flex">
                            <button class="btn btn-outline-primary mr-1" title="Filter" onclick="filterData()">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="btn btn-outline-secondary" title="Reset" onclick="filterData(true)">
                                <i class="fas fa-ban"></i>
                            </button>
                        </div>
                    </div>
				</div>
                <div class="card-body">
                    <table id="customer_support_table" class="table table-hover table-striped nowrap">
                        <thead class="nowrap">
                            <tr>
                                <th></th>
                                <th>Actions</th>
                                <th>Status</th>
                                <th>ID #</th>
                                <th>Client</th>
                                <th>Client Branch</th>
                                <th>Ticket Number</th>
                                <th>Security and ICT System</th>
                                <th>Priority</th>
                                <th>Due Date</th>
                                <th>Follow Up Date</th>
                                <th>Problem/Issue</th>
                                <th>Findings</th>
                                <th>Initial Action Taken</th>
                                <th>Troubleshooting Done</th>
                                <th>Remarks</th>
                                <th>Support Specialist/s</th>
                                <th>Created By</th>
                                <th>Created At</th>
                                <th>Done By</th>
                                <th>Done At</th>
                                <th>Turn Over By</th>
                                <th>Turn Over At</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->include('sales/customer_support/form'); ?>
<?= $this->include('sales/customer_support/change'); ?>
<?= $this->include('templates/loading'); ?>
<?=$this->endSection();?>
