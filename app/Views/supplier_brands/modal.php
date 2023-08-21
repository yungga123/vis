<!-- Supplier Brands Add Form (MODAL) -->
<div class="modal fade" id="modal_add_supplier_brand" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="form_add_supplier_brand" action="<?= url_to('suppliers_brand.save'); ?>" method="post" autocomplete="off">
                <?= csrf_field(); ?>
                <input type="hidden" id="brand_id" name="id" readonly>
                <input type="hidden" id="brand_supplier_id" name="supplier_id" readonly>
                <div class="modal-header">
                    <h5 class="modal-title">Add New Supplier brand</h5>
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
                                <label for="brand_name">Brand</label>
                                <input type="text" name="brand_name" id="brand_name" class="form-control" placeholder="Enter here...">
                                <small id="alert_brand_name" class="text-muted"></small>
                            </div>
                            <!-- Form Text -->
                            <div class="form-group">
                                <label for="brand_product">Product</label>
                                <input type="text" name="product" id="brand_product" class="form-control" placeholder="Enter here...">
                                <small id="alert_brand_product" class="text-muted"></small>
                            </div>
                            <!-- Form Text -->
                            <div class="form-group">
                                <label for="brand_warranty">Warranty</label>
                                <select name="warranty" id="brand_warranty" class="form-control" placeholder="Enter here...">
                                <small id="alert_brand_warranty" class="text-muted"></small>
                            </div>
                            <!-- Form Text -->
                            <div class="form-group">
                                <label for="brand_sales_person">Supplier Name</label>
                                <input type="text" name="sales_person" id="brand_sales_person" class="form-control" placeholder="Enter here...">
                                <small id="alert_brand_sales_person" class="text-muted"></small>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <!-- Form Text -->
                            <div class="form-group">
                                <label for="brand_sales_contact_number">Supplier Name</label>
                                <input type="text" name="sales_contact_number" id="brand_sales_contact_number" class="form-control" placeholder="Enter here...">
                                <small id="alert_brand_sales_contact_number" class="text-muted"></small>
                            </div>
                            <!-- Form Text -->
                            <div class="form-group">
                                <label for="brand_technical_support">Supplier Name</label>
                                <input type="text" name="technical_support" id="brand_technical_support" class="form-control" placeholder="Enter here...">
                                <small id="alert_brand_technical_support" class="text-muted"></small>
                            </div>
                            <!-- Form Text -->
                            <div class="form-group">
                                <label for="brand_technical_contact_number">Supplier Name</label>
                                <input type="text" name="technical_contact_number" id="brand_technical_contact_number" class="form-control" placeholder="Enter here...">
                                <small id="alert_brand_technical_contact_number" class="text-muted"></small>
                            </div>
                            <!-- Form Text -->
                            <div class="form-group">
                                <label for="brand_remarks">Supplier Name</label>
                                <input type="text" name="remarks" id="brand_remarks" class="form-control" placeholder="Enter here...">
                                <small id="alert_brand_remarks" class="text-muted"></small>
                            </div>
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