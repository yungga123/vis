<!-- Supplier Dropdown Add Form (MODAL) -->
<div class="modal fade" id="modal_add_supplierDd" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="form_add_supplier" action="<?= url_to('suppliers.save'); ?>" method="post" autocomplete="off">
                <?= csrf_field(); ?>
                <input type="hidden" id="supplier_dropdown_id" name="id" readonly>
                <div class="modal-header">
                    <h5 class="modal-title">Add New Supplier</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <!-- Row -->
                    <div class="row">
                        <!-- Col -->
                        <div class="col-sm-12">

                            <!-- Form Text -->
                            <div class="form-group">
                                <label for="dropdown">Dropdown</label>
                                <input type="text" name="dropdown" id="dropdown" class="form-control" placeholder="Enter here...">
                                <small id="alert_dropdown" class="text-muted"></small>
                            </div>
                            <input type="text" id="dropdown_type" name="dropdown_type" value="Suppliers">
                            <input type="text" id="created_by" name="created_by" value="<?= session('employee_id')  ?>">


                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-outline-success">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>