<?=$this->extend('templates/default');?>
<?=$this->section('content');?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex flex-md-row flex-column align-items-md-center">
                        <div class="mr-2 mb-2 mb-md-0">
                            <strong>Filter by Types: </strong>
                        </div>
                        <div class="mr-2 flex-fill mb-2 mb-md-0">
                            <select name="filter_dtypes[]" id="filter_dtypes" class="form-control select2" multiple="multiple" data-placeholder="Select filter by types (Multiple)" style="width: 100%;"></select>
                        </div>
                        <div class="align-items-center justify-content-center d-flex">
                            <button class="btn btn-outline-secondary" title="Reset" onclick="reset()">
                                <i class="fas fa-ban"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table id="table_dropdown" class="table table-hover table-striped nowrap" width="100%">
                        <thead>
                            <tr>
                                <th width="15%">ID</th>
                                <th width="40%">Dropdowns</th>
                                <th width="20%">Type</th>
                                <th width="15%">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="card-footer">
                    <a href="<?= url_to('inventory.home'); ?>" class="btn btn-success">Inventory Masterlist</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->include('inventory/dropdown/form'); ?>
<?= $this->include('templates/loading'); ?>
<?=$this->endSection();?>