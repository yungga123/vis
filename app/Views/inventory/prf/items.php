<!-- PRF Items Modal -->
<div class="modal fade" id="prf_items_modal" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">PRF Items Detials</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5 class="text-center text-bold" id="prf_id_text"></h5>
                <table class="table table-hover" id="prf_items_table">
                    <thead>
                        <tr>
                            <th>Item #</th>
                            <th>Category</th>
                            <th>Item Model</th>
                            <th>Item Description</th>
                            <th>Current Stocks</th>
                            <th>Quantity Out</th>
                            <th>Returned</th>
                            <th>Consumed</th>
                            <th>Returned Date</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <p class="mt-5 text-center" id="note_item_out"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                
            </div>
        </div>
    </div>
</div>