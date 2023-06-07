<?= $this->extend('templates/default'); ?>
<?= $this->section('content'); ?>

<div class="container-fluid">

    <!-- Suppliers List Card -->
    <div class="card">
        <div class="card-body">


            <table id="supplier_table" class="table table-bordered table-striped nowrap" data-url="<?= url_to('suppliers.list'); ?>">
                <thead>
                    <tr>
                        <th>Supplier ID</th>
                        <th>Supplier Name</th>
                        <th>Supplier Type</th>
                        <th>Contact Person</th>
                        <th>Contact Number</th>
                        <th>Viber</th>
                        <th>Payment Terms</th>
                        <th>Mode of Payment</th>
                        <th>Product</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
            </table>


        </div>
    </div>
</div>


<!-- Supplier Add Form (MODAL) -->
<div class="modal fade" id="modal_add_supplier" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form id="form_add_supplier" action="<?= url_to('suppliers.save'); ?>" method="post" autocomplete="off">
                <?= csrf_field(); ?>
                <input type="hidden" id="supplier_id" name="id" readonly>
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
                        <div class="col-sm-6">

                            <!-- Form Text -->
                            <div class="form-group">
                                <label for="supplier_name">Supplier Name</label>
                                <input type="text" name="supplier_name" id="supplier_name" class="form-control" placeholder="Enter here...">
                                <small id="alert_supplier_name" class="text-muted"></small>
                            </div>

                            <!-- Form Text -->
                            <div class="form-group">
                                <label for="supplier_type">Type of Supplier</label>
                                <input type="text" name="supplier_type" id="supplier_type" class="form-control" placeholder="Enter here...">
                                <small id="alert_supplier_type" class="text-muted"></small>
                            </div>

                            <!-- Form Text -->
                            <div class="form-group">
                                <label for="contact_person">Contact Person</label>
                                <input type="text" name="contact_person" id="contact_person" class="form-control" placeholder="Enter here...">
                                <small id="alert_contact_person" class="text-muted"></small>
                            </div>

                            <!-- Form Text -->
                            <div class="form-group">
                                <label for="contact_number">Contact Number</label>
                                <input type="text" name="contact_number" id="contact_number" class="form-control" placeholder="Enter here...">
                                <small id="alert_contact_number" class="text-muted"></small>
                            </div>

                            <!-- Form Text -->
                            <div class="form-group">
                                <label for="product">Product</label>
                                <input type="text" name="product" id="product" class="form-control" placeholder="Enter here...">
                                <small id="alert_product" class="text-muted"></small>
                            </div>



                        </div>

                        <!-- Col -->
                        <div class="col-sm-6">

                            <!-- Form Text -->
                            <div class="form-group">
                                <label for="viber">Viber Account</label>
                                <input type="text" name="viber" id="viber" class="form-control" placeholder="Enter here...">
                                <small id="alert_viber" class="text-muted"></small>
                            </div>

                            <!-- Form Text -->
                            <div class="form-group">
                                <label for="payment_terms">Payment Terms</label>
                                <input type="text" name="payment_terms" id="payment_terms" class="form-control" placeholder="Enter here...">
                                <small id="alert_payment_terms" class="text-muted"></small>
                            </div>

                            <!-- Form Text -->
                            <div class="form-group">
                                <label for="payment_mode">Mode of Payment</label>
                                <input type="text" name="payment_mode" id="payment_mode" class="form-control" placeholder="Enter here...">
                                <small id="alert_payment_mode" class="text-muted"></small>
                            </div>

                            <!-- Form Text -->
                            <div class="form-group">
                                <label for="remarks">Additional Remarks</label>
                                <input type="text" name="remarks" id="remarks" class="form-control" placeholder="Enter here...">
                                <small id="alert_remarks" class="text-muted"></small>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->include('templates/loading'); ?>
<?= $this->endSection(); ?>