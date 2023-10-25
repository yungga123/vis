<div class="modal fade" id="customer_branch_table_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Client Branch</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-primary" id="btn_add_branch">Add New Branch</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="customer_branch_table" class="table table-hover table-striped nowrap">
                            <thead>
                                <tr>
                                    <th>Actions</th>
                                    <th>Branch Name</th>
                                    <th>Contact Person</th>
                                    <th>Contact Number</th>
                                    <th>Address</th>
                                    <th>Email Address</th>
                                    <th>Notes</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>