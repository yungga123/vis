<?= $this->extend('templates/default'); ?>

<?= $this->section('content'); ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-body">

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="item_name">Item name</label>
                        <input type="text" name="item_name" id="item_name" class="form-control" placeholder="Enter...">
                        <small id="small_item_name" class="text-muted"></small>
                    </div>

                    <div class="form-group">
                        <label for="item_brand">Brand</label>
                        <input type="text" name="item_brand" id="item_brand" class="form-control" placeholder="Enter...">
                        <small id="small_item_brand" class="text-muted"></small>
                    </div>

                    <div class="form-group">
                        <label for="item_type">Item Type</label>
                        <input type="text" name="item_type" id="item_type" class="form-control" placeholder="Enter...">
                        <small id="small_item_type" class="text-muted"></small>
                    </div>

                    <div class="form-group">
                        <label for="item_sdp">Dealer's Price</label>
                        <input type="number" name="item_sdp" id="item_sdp" class="form-control" placeholder="Enter...">
                        <small id="small_item_sdp" class="text-muted"></small>
                    </div>

                    <div class="form-group">
                        <label for="item_srp">Retail Price</label>
                        <input type="number" name="item_srp" id="item_srp" class="form-control" placeholder="Enter...">
                        <small id="small_item_srp" class="text-muted"></small>
                    </div>

                    <div class="form-group">
                        <label for="project_price">Project Price</label>
                        <input type="number" name="project_price" id="project_price" class="form-control" placeholder="Enter...">
                        <small id="small_project_price" class="text-muted"></small>
                    </div>
                </div>


                <div class="col-sm-6">

                    <div class="form-group">
                        <label for="number">Quantity</label>
                        <input type="text" name="stocks" id="stocks" class="form-control" placeholder="Enter...">
                        <small id="small_stocks" class="text-muted"></small>
                    </div>

                    <div class="form-group">
                        <label for="stock_unit">Unit</label>
                        <input type="text" name="stock_unit" id="stock_unit" class="form-control" placeholder="Enter...">
                        <small id="small_stock_unit" class="text-muted"></small>
                    </div>

                    <div class="form-group">
                        <label for="date_of_purchase">Date of Purchase</label>
                        <input type="date" name="date_of_purchase" id="date_of_purchase" class="form-control" placeholder="Enter...">
                        <small id="small_date_of_purchase" class="text-muted"></small>
                    </div>

                    <div class="form-group">
                        <label for="location">Item Location</label>
                        <input type="text" name="location" id="location" class="form-control" placeholder="Enter...">
                        <small id="small_location" class="text-muted"></small>
                    </div>

                    <div class="form-group">
                        <label for="supplier">Supplier</label>
                        <input type="text" name="supplier" id="supplier" class="form-control" placeholder="Enter...">
                        <small id="small_supplier" class="text-muted"></small>
                    </div>

                    <div class="form-group">
                        <label for="encoder">Encoder</label>
                        <input type="text" name="encoder" id="encoder" class="form-control" placeholder="Enter..." readonly value="<?= session('name') ?>">
                        <small id="small_encoder" class="text-muted"></small>
                    </div>

                </div>

            </div>


        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-success float-right">CONFIRM</button>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>