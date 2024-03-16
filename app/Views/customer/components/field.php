<div class="client-fields">
    <div class="form-group">
        <label class="required" for="customer_id">Client</label>
        <div class="mb-2">                                    
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="customer_type" id="commercial" value="commercial" checked>
                <label class="form-check-label" for="commercial">Commercial</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="customer_type" id="residential" value="residential">
                <label class="form-check-label" for="residential">Residentail</label>
            </div>
        </div>
        <select class="custom-select" id="customer_id" name="customer_id" style="width: 100%;"></select>
        <small id="alert_customer_id" class="text-danger"></small>
    </div>
    <div class="form-group d-none" id="client_branch_wrapper">
        <label for="customer_branch_id">Client Branch</label>
        <select class="custom-select" id="customer_branch_id" name="customer_branch_id" style="width: 100%;"></select>
        <small id="alert_customer_branch_id" class="text-danger"></small>
    </div>
</div>