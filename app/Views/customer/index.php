<?= $this->extend('templates/default'); ?>
<?= $this->section('content'); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">                
                <div class="card">
                    <div class="card-header">
                        <label for="filter-group">Filters by (New Client?, Client Type and Source):</label>
                        <div class="input-group" id="filter-group" style="flex-wrap: nowrap; width: 100%;">
                            <select class="custom-select mr-1" name="new_client" id="filter_new_client">
                                <option value="">All</option>
                                <option value="1">Yes</option>
                                <option value="zero">No</option>
                            </select>
                            <select class="custom-select mr-1" name="filter_type" id="filter_type">
                                <option value="">All</option>
                                <option value="COMMERCIAL">Commercial</option>
                                <option value="RESIDENTIAL">Residential</option>
                            </select>
                            <select class="custom-select select2" id="filter_source" multiple>
                                <?php foreach (get_client_sources() as $key => $val ): ?>
                                    <option value="<?= $key ?>"><?= $val ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="input-group-append">
                                <button class="btn btn-outline-primary px-3" onclick="filterData()" type="button" title="Search filter">Filter</button>
                                <button class="btn btn-outline-secondary px-3 rounded-right" onclick="filterData(true)" type="button" title="Reset filter">Reset</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="customer_table" class="table table-hover table-striped nowrap">
                            <thead class="nowrap">
                                <tr>
                                    <th>Actions</th>
                                    <th>Client ID</th>
                                    <th>New Client?</th>
                                    <th>Client Name</th>
                                    <th>Client Type</th>
                                    <th>Contact Person</th>
                                    <th>Contact Number</th>
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
                    <div class="card-footer">
                        <a type="button" href="<?= url_to('customer.export') ?>" class="btn btn-success">Export All Clients</a>
                        <a type="button" href="<?= url_to('customer.branch.export') ?>" class="btn btn-success">Export All Client Branches</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= $this->include('customer/form') ;?>
<?= $this->include('customer/branch/index') ;?>
<?= $this->include('customer/branch/form') ;?>
<?= $this->include('templates/loading'); ?>
<?= $this->endSection(); ?>