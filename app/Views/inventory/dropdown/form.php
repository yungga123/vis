<!-- Modal -->
<div class="modal fade" id="modal_dropdown" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">            
            <div class="modal-header">
                <h5 class="modal-title">Create Dropdown</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body"> 
                <form id="form_dropdown" action="<?= url_to('inventory.dropdown.save'); ?>" method="post" autocomplete="off" class="with-label-indicator">
                    <?= csrf_field(); ?>
                    <input type="hidden" id="dropdown_id" name="dropdown_id" readonly>    
                    <input type="hidden" name="is_category" value="" readonly>
                    <div class="form-group">
                        <label class="required" for="dropdown_type">Category</label>
                        <select name="dropdown_type" id="dropdown_type" class="form-control select2" data-placeholder="Select Category" style="width: 100%;">
                            <option value="">Select Category</option>
                        </select>
                        <input type="hidden" id="dropdown_type_text" name="dropdown_type_text" readonly> 
                        <small id="alert_dropdown_type" class="text-danger"></small>
                        <div class="mt-1">
                            Not in the list? <a href="javascript:void(0)" id="create_new_category" onclick="toggleCategory()">Create new</a>!
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="required" for="dropdown">Description (Dropdown)</label>
                        <textarea rows="3" name="dropdown" id="dropdown" class="form-control" placeholder="Ex. Network Access"></textarea>
                        <small id="alert_dropdown" class="text-danger"></small>
                        <p class="text-muted"><strong>Comma (,)</strong> separated for multiple insert for the same category. Ex. "PABX, PVC, CCTV"</p>
                    </div>
                    <div class="d-flex justify-content-end mt-2">
                        <?php if ($can_edit): ?>
                            <a type="button" class="btn btn-warning btn-edit-category mr-1 d-none" onclick="editCategory()">Edit Selected Category</a>
                        <?php endif; ?>                            
                        <button type="submit" class="btn btn-success">Save Dropdown</button>
                    </div>
                </form>
                <form id="form_dropdown_category" action="<?= url_to('inventory.dropdown.save'); ?>" method="post" autocomplete="off" class="with-label-indicator d-none">
                    <?= csrf_field(); ?>
                    <input type="hidden" id="dropdown_id_category" name="dropdown_id" readonly>     
                    <input type="hidden" name="is_category" value="true" readonly>
                    <div class="d-flex justify-content-start">
                        <button type="button" class="text-secondary border-0 p-0 bg-transparent btn-back-to-dropdown" title="Back" onclick="toggleCategory(true)"><i class="fas fa-arrow-left"></i></button>
                    </div>
                    <div class="ml-2 mr-2 mt-2">                            
                        <div class="form-group">
                            <label class="required" for="dropdown_category">Description (Category)</label>
                            <input type="text" name="dropdown" id="dropdown_category" class="form-control text-uppercase" placeholder="Ex. DIRECT">
                            <small id="alert_dropdown_category" class="text-danger"></small>
                        </div>
                        <div class="d-flex justify-content-end mt-2">
                            <?php if ($can_delete): ?>
                                <a type="button" class="btn btn-danger btn-delete-category mr-1 d-none" onclick="deleteCategory()">Delete Category</a>
                            <?php endif; ?>   
                            <button type="submit" class="btn btn-success">Save Category</button>
                        </div>
                    </div>
                </form>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>