<!-- Details Modal -->
<div class="modal fade" id="service_report_details_modal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Units & Items</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div>
                    <h4 class="text-center">Units</h4>
                    <div class="table-responsive">
                        <table class="table table-striped unit-condition">
                            <thead>
                                <tr class="text-bold">
                                    <td>ITEMS</td>
                                    <td>STATUS</td>
                                    <td>If defective please specify:</td>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <div>
                    <h4 class="text-center">Items</h4>
                    <div class="table-responsive">
                        <table class="table table-striped items">
                            <thead>
                                <tr class="text-bold">
                                    <td>ITEM NO.</td>
                                    <td>DESCRIPTION</td>
                                    <td>QTY</td>
                                    <td>UNIT PRICE</td>
                                    <td>TOTAL PRICE</td>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr class="text-bold">
                                    <td colspan="2" class="text-right">Grand Totals</td>
                                    <td class="total_qty text-danger"></td>
                                    <td class="total_price text-danger"></td>
                                    <td class="grand_total text-danger"></td>
                                    <td></td>
                                </tr>
                            </tfoot>
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