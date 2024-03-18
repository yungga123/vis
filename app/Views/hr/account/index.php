<?=$this->extend('templates/default');?>
<?=$this->section('content');?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">            
            <div class="card">
				<div class="card-header">
                    <div class="mr-2 mb-2">
                        <strong>Filters by Role or Created Date (Start/End): </strong>
                    </div>
                    <div class="d-flex flex-md-row flex-column align-items-md-center">
                        <div class="mr-2 flex-fill mb-2 mb-md-0">
                            <select id="filter_access_level" class="custom-select select2" data-placeholder="Select a role" multiple style="width: 100%;">
                                <?= get_roles_options(); ?>
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
                    <table id="account_table" class="table table-striped table-hover nowrap">
                        <thead class="nowrap">
                            <tr>
                                <th></th>
                                <th>Actions</th>
                                <th>Employee ID</th>
                                <th>Employee Name</th>
                                <th>Username</th>
                                <th>Role</th>
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
<?= $this->include('hr/account/form'); ?>
<?= $this->include('templates/loading'); ?>
<?=$this->endSection();?>
