<div class="card">
    <div class="card-header">
        <div class="mr-2 mb-2">
            <strong>Filters by Transaction Type, Coming From or Expenses: </strong>
        </div>
        <div class="d-flex flex-md-row flex-column align-items-md-center">
            <div class="mr-2 flex-fill mb-2 mb-md-0">
                <select class="custom-select select2" id="filter_transaction_type" data-placeholder="Select a transaction type" style="width: 100%;">
                    <option value=""></option>
                    <option value="incoming">Incoming</option>
                    <option value="outgoing">Outgoing</option>
                </select>
            </div>
            <div class="mr-2 flex-fill mb-2 mb-md-0">
                <select class="custom-select select2" id="filter_coming_from" data-placeholder="Coming from?" style="width: 100%;">
                    <option value=""></option>
                    <option value="Billing Invoice">Billing Invoice</option>
                    <option value="Expenses">Expenses</option>
                </select>   
            </div>
            <div class="mr-2 flex-fill mb-2 mb-md-0">
                <select class="custom-select select2" id="filter_expenses" data-placeholder="Select an expense" multiple style="width: 100%;">
                    <?php foreach (get_expenses() as $val => $text): ?>
                        <option value="<?= $val ?>"><?= $text ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="align-items-center justify-content-center d-flex">
                <button class="btn btn-outline-primary mr-1" title="Filter" onclick="filterData()">
                    <i class="fas fa-search"></i>
                </button>
                <button class="btn btn-outline-secondary" title="Reset" onclick="filterData(true)">
                    <i class="fas fa-ban"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <table id="funds_table" class="table table-hover table-striped nowrap">
            <thead class="nowrap">
                <tr>
                    <th>Transaction Type</th>
                    <th>ID</th>
                    <th>Transaction Amount</th>
                    <th>Previous Funds</th>
                    <th>Current Funds</th>
                    <th>Coming From</th>
                    <th>Expenses</th>
                    <th>Remarks</th>
                    <th>Created By</th>
                    <th>Created At</th>
                </tr>
            </thead>
        </table>
    </div>
</div>