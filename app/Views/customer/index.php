<?= $this->extend('templates/default'); ?>
<?= $this->section('content'); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">                
                <div class="card">
                    <div class="card-header">
                        <div class="mr-2 mb-2">
                            <strong>Filters by (New Client?, Client Type and Source):</strong>
                        </div>
                        <div class="d-flex flex-md-row flex-column align-items-md-center">
                            <div class="mr-2 flex-fill mb-2 mb-md-0">
                                <select class="custom-select mr-1" name="new_client" id="filter_new_client" data-placeholder="Is new client?" style="width: 100%;">
                                    <option value="">All</option>
                                    <option value="1">Yes</option>
                                    <option value="zero">No</option>
                                </select>
                            </div>
                            <div class="mr-2 flex-fill mb-2 mb-md-0">
                                <select class="custom-select mr-1" name="filter_type" id="filter_type" data-placeholder="Select a client type" style="width: 100%;">
                                    <option value="">All</option>
                                    <option value="COMMERCIAL">Commercial</option>
                                    <option value="RESIDENTIAL">Residential</option>
                                </select>
                            </div>
                            <div class="mr-2 flex-fill mb-2 mb-md-0">
                                <select class="custom-select select2" id="filter_source" data-placeholder="Select a source" multiple style="width: 100%;">
                                    <?php foreach (get_client_sources() as $key => $val ): ?>
                                        <option value="<?= $key ?>"><?= $val ?></option>
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
                        <table id="customer_table" class="table table-hover table-striped nowrap">
                            <thead class="nowrap">
                                <tr>
                                    <th></th>
                                    <th>Actions</th>
                                    <th>Client ID</th>
                                    <th>New Client?</th>
                                    <th>Client Name</th>
                                    <th>Client Type</th>
                                    <th>Contact Person</th>
                                    <th>Contact Number</th>
                                    <th>Telephone Number</th>
                                    <th>Email Address</th>
                                    <th>Address</th>
                                    <th>Source</th>
                                    <th>Notes</th>
                                    <th>Referred By</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->include('customer/form') ;?>
<?= $this->include('customer/upload') ;?>
<?= $this->include('customer/branch/index') ;?>
<?= $this->include('customer/branch/form') ;?>
<?= $this->include('templates/loading'); ?>
<?= $this->endSection(); ?>