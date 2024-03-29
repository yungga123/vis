<div class="modal fade" id="modal-customer-branch" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Customer Branch</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-body">
                        <table id="customerbranch_table" class="table table-hover table-striped nowrap" data-url="<?= url_to('customers.branchlist'); ?>">
                            <thead class="nowrap">
                                <tr>
                                    <th>Actions</th>
                                    <th>Branch Name</th>
                                    <th>Contact Person</th>
                                    <th>Contact Number</th>
                                    <th>Address</th>
                                    <th>Email Address</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>

            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-success" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>