<?=$this->extend('templates/default');?>
<?=$this->section('content');?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">                    
            <div class="card">
                <div class="card-header">
                    <div class="mr-2 mb-2">
                        <strong>Filters by Status, Area or Service Type: </strong>
                    </div>
                    <div class="d-flex flex-md-row flex-column align-items-md-center">
                        <div class="mr-2 flex-fill mb-2 mb-md-0">
                            <select class="custom-select select2" id="filter_status" data-placeholder="Select a status" multiple style="width: 100%;">
                                <?php foreach (get_service_report_status('', true) as $val => $text): ?>
                                    <option value="<?= $val ?>"><?= ucfirst($text) ?></option>
                                <?php endforeach; ?>
                            </select>   
                        </div>
                        <div class="mr-2 flex-fill mb-2 mb-md-0">
                            <select class="custom-select select2" id="filter_area" data-placeholder="Select an area" style="width: 100%;">
                                <option value="">Select an area</option>
                                <option value="Customer Site">Customer Site</option>
                                <option value="In-house">In-house</option>
                            </select>
                        </div>
                        <div class="mr-2 flex-fill mb-2 mb-md-0">
                            <select class="custom-select select2" id="filter_service_type" data-placeholder="Select a service type" multiple style="width: 100%;">
                                <?php foreach (get_service_types() as $val => $text): ?>
                                    <option value="<?= $val ?>"><?= ucfirst($text) ?></option>
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
                    <table id="service_report_table" class="table table-hover table-striped nowrap">
                        <thead class="nowrap">
                            <tr>
                                <th></th>
                                <th>Action</th>
                                <th>Units & Items</th>
                                <th>Status</th>
                                <th>SR #</th>
                                <th>JO #</th>
                                <th>Client</th>
                                <th>Client Branch</th>
                                <th>Area</th>
                                <th>Serial Number</th>
                                <th>Server Type</th>
                                <th>Service Type</th>
                                <th>Arrival At</th>
                                <th>Error Description</th>
                                <th>Corrective Action</th>
                                <th>Remarks</th>
                                <th>Labor (Hrs)</th>
                                <th>Travel Time (Hrs)</th>
                                <th>Time Out</th>
                                <th>Created By</th>
                                <th>Created At</th>
                                <th>Accepted By</th>
                                <th>Accepted At</th>
                                <th>Filed By</th>
                                <th>Filed At</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->include('admin/service_report/form'); ?>
<?= $this->include('admin/service_report/details'); ?>
<?= $this->include('templates/loading'); ?>
<?=$this->endSection();?>