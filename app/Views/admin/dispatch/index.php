<?=$this->extend('templates/default');?>
<?=$this->section('content');?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">                    
            <div class="card">
				<div class="card-header">
                    <div class="mr-2 mb-2">
                        <strong>Filters by Service Type or Dispatch Date (Start/End): </strong>
                    </div>
                    <div class="d-flex flex-md-row flex-column align-items-md-center">
                        <div class="mr-2 flex-fill mb-2 mb-md-0">
                            <select id="filter_service_type" class="custom-select select2" data-placeholder="Select a service type" multiple style="width: 100%;">
                                <?php foreach (get_dispatch_services() as $val => $text): ?>
                                    <option value="<?= $val ?>"><?= $text ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mr-2 flex-fill mb-2 mb-md-0">
                            <input type="date" class="form-control" name="filter_start_date" id="filter_start_date" placeholder="Start Date">
                        </div>
                        <div class="mr-2 flex-fill mb-2 mb-md-0">
                            <input type="date" class="form-control" name="filter_end_date" id="filter_end_date" placeholder="End Date">
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
                    <table id="dispatch_table" class="table table-hover table-striped nowrap">
                        <thead class="nowrap">
                            <tr>
                                <th></th>
                                <th>Actions</th>
                                <th>Dispatch ID</th>
                                <th>Schedule ID</th>
                                <th>Schedule Title</th>
                                <th>Schedule Description</th>
                                <th>Dispatch Date</th>
                                <th>Dispatch Out</th>
                                <th>Time In</th>
                                <th>Time Out</th>
                                <th>SR Number</th>
                                <th>Technicians</th>
                                <th>Service Type</th>
                                <th>With Permit</th>
                                <th>Comments</th>
                                <th>Remarks</th>
                                <th>Checked By</th>
                                <th>Dispatched By</th>
                                <th>Dispatched At</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->include('admin/dispatch/form'); ?>
<?= $this->include('templates/loading'); ?>
<?=$this->endSection();?>
