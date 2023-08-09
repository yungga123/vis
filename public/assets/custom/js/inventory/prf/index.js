var table, modal, form, elems, joSelector, invSelector, itemFieldTable;

$(document).ready(function () {
	table = "prf_table";
	modal = "prf_modal";
	form = "prf_form";
	elems = ["job_order_id", "inventory_id", "quantity_out", "process_date"];
	joSelector = "#job_order_id";
	invSelector = ".inventory_id";
	itemFieldTable = $("#item_field_table tbody");

	/* Load dataTable */
	loadDataTable(table, router.prf.list, METHOD.POST);

	/* Toggle modal */
	$("#btn_add_record").on("click", function () {
		$(`#${modal}`).modal("show");
		$(`#${modal}`).removeClass("edit").addClass("add");
		$(`#${modal} .modal-title`).text("Add PRF");
		$(`#${form}`)[0].reset();
		$("#prf_id").val("");
		$("#orig_job_order").addClass("d-none");
		$("#orig_item").addClass("d-none");
		$(".job-order-details").html("");
		$(".item-row").remove();
		clearSelect2Selection(joSelector);
		clearSelect2Selection(invSelector);
		clearAlertInForm(elems);
	});

	/* Job Order select2 via ajax data source */
	select2AjaxInit(
		joSelector,
		"Search & select a job order",
		router.inventory.common.joborders,
		"option_text",
		_loadJobOrderDetails
	);

	/* Masterlist select2 via ajax data source */
	_initInventorySelect2();

	/* Form for saving record */
	formSubmit($("#" + form), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			self[0].reset();
			// refreshDataTable($("#" + table));
			notifMsgSwal(res.status, message, res.status);
			$("#prf_id").val("");
			$("#orig_job_order").addClass("d-none");
			$("#orig_item").addClass("d-none");
			$(".job-order-details").html("");
			$(".item-row").remove();
			clearSelect2Selection(joSelector);
			clearSelect2Selection(invSelector);

			if ($(`#${modal}`).hasClass("edit")) {
				$(`#${modal}`).modal("hide");
			}
		}

		showAlertInForm(elems, message, res.status);
	});
});

/* Get prf items */
function view(id) {
	$("#prf_items_modal #prf_id_text").text("PRF #: " + id);
	showLoading();

	const data = { id: id, prf_items: true };
	$.post(router.prf.fetch, data)
		.then((res) => {
			closeLoading();

			if (res.status === STATUS.SUCCESS) {
				let html = "";

				if (!isEmpty(res.data)) {
					$.each(res.data, (index, val) => {
						console.log(index, val);
						html += `
							<tr>
								<td>${val.inventory_id}</td>
								<td>${val.category_name}</td>
								<td>${val.item_model}</td>
								<td>${val.item_description}</td>
								<td>${val.stocks}</td>
								<td>${val.quantity_out}</td>
								<td>${val.returned_q || "N/A"}</td>
								<td>${val.consumed}</td>
								<td>${val.returned_date || "N/A"}</td>
							</tr>
						`;
					});
				} else {
					html =
						'<tr><td colspan="9" align="center">No PRF items found...</td></tr>';
				}

				$(`#prf_items_table tbody`).html(html);
				$(`#prf_items_modal`).modal("show");
			} else {
				$(`#prf_items_modal`).modal("hide");
				notifMsgSwal(res.status, res.message, res.status);
			}
		})
		.catch((err) => catchErrMsg(err));
}

/* Get record details */
function edit(id) {
	$(`#${modal}`).removeClass("add").addClass("edit");
	$(`#${modal} .modal-title`).text("Edit Item");
	$("#prf_id").val(id);

	clearAlertInForm(elems);
	showLoading();

	$.post(router.prf.fetch, { id: id })
		.then((res) => {
			closeLoading();

			if (res.status === STATUS.SUCCESS) {
				// Set selected job order in select2
				setSelect2AjaxSelection(
					joSelector,
					res.data.option_text,
					res.data.job_order_id
				);
				// Set selected item in select2
				setSelect2AjaxSelection(
					invSelector,
					res.data.text,
					res.data.inventory_id
				);

				$.each(res.data, (key, value) => $(`input[name="${key}"]`).val(value));
				$("#orig_job_order")
					.removeClass()
					.html(`Original JO: <strong>${res.data.option_text}</strong>`);
				$("#orig_item")
					.removeClass()
					.html(`Original item: <strong>${res.data.text}</strong>`);

				$(`#${modal}`).modal("show");
			} else {
				$(`#${modal}`).modal("hide");
				notifMsgSwal(res.status, res.message, res.status);
			}
		})
		.catch((err) => catchErrMsg(err));
}

/* Delete record */
function remove(id) {
	const swalMsg = "delete";
	swalNotifConfirm(
		function () {
			$.post(router.prf.delete, { id: id })
				.then((res) => {
					const message = res.errors ?? res.message;

					refreshDataTable($("#" + table));
					notifMsgSwal(res.status, message, res.status);
				})
				.catch((err) => catchErrMsg(err));
		},
		TITLE.WARNING,
		swalMsg,
		STATUS.WARNING
	);
}

/* Change status record */
function change(id, changeTo, status, currStocks, quantityOut) {
	const swalMsg = `
		<div>PRF #: <strong>${id}</strong></div>
		<div>Are you sure you want to <strong>${strUpper(
			changeTo
		)}</strong> this PRF?</div>
	`;
	const title = `${strUpper(status)} to ${strUpper(changeTo)}!`;
	const data = { id: id, status: changeTo };

	if (!(isEmpty(currStocks) && isEmpty(quantityOut))) {
		// Check if current stocks is less than the quantity out
		// If yes, will not proceed to change status
		if (Math.floor(currStocks < quantityOut)) {
			notifMsgSwal(
				STATUS.INFO,
				"The Item's <strong>CURRENT STOCK/S</strong> is less than the <strong>QUANTITY OUT</strong>! Please replenish the stocks first.",
				STATUS.INFO
			);

			return;
		}
	}

	swalNotifConfirm(
		function () {
			$.post(router.prf.change, data)
				.then((res) => {
					const message = res.errors ?? res.message;

					refreshDataTable($("#" + table));
					notifMsgSwal(res.status, message, res.status);
				})
				.catch((err) => catchErrMsg(err));
		},
		title,
		swalMsg,
		STATUS.WARNING
	);
}

/* Toggle item field */
function toggleItemField(row) {
	const itemFieldCount = itemFieldTable.find("tr").length + 1;

	if (row) {
		itemFieldTable.children("tr#row_" + row).remove();
		return;
	}

	const html = `
		<tr class="item-row" id="row_${itemFieldCount}">
			<td>
				<select class="custom-select inventory_id" name="inventory_id[]" style="width: 100%;"></select>
			</td>
			<td>
				<input type="number" name="quantity_out[]" class="form-control quantity_out" placeholder="Enter quantity out">
			</td>
			<td>
				<button type="button" class="btn btn-sm btn-danger" onclick="toggleItemField(${itemFieldCount})" title="Add new item field">
					<i class="fas fa-minus"></i>
				</button>
			</td>
		</tr>
	`;

	itemFieldTable.append(html);
	_initInventorySelect2();
}

/* Masterlist select2 via ajax data source */
function _initInventorySelect2() {
	select2AjaxInit(
		invSelector,
		"Search & select an item",
		router.inventory.common.masterlist,
		"text"
	);
}

/* Load selected job order details */
function _loadJobOrderDetails(data) {
	let html = "";

	if (!data.selected && data.id) {
		html = `
			<h5 class="text-center">JO Details</h5>
			<table class="table table-bordered table-sm table-condensed">
				<tbody>
                    <tr>
                        <th class="text-right" width="50%">Task Lead #</th>
						<td class="text-left" width="50%">${data.tasklead_id}</td>
                    </tr>
                    <tr>
                        <th class="text-right" width="50%">Client</th>
						<td class="text-left" width="50%">${data.customer_name}</td>
                    </tr>
                    <tr>
                        <th class="text-right" width="50%">Manager</th>
						<td class="text-left" width="50%">${data.manager}</td>
                    </tr>
                    <tr>
                        <th class="text-right" width="50%">Work Type</th>
						<td class="text-left" width="50%">${data.work_type}</td>
                    </tr>
                    <tr>
                        <th class="text-right" width="50%">Status</th>
						<td class="text-left" width="50%">${strUpper(data.jo_status)}</td>
                    </tr>
				</tbody>
			</table>
		`;
	}

	$(".job-order-details").html(html);
}

/* Load selected item details */
function _loadItemDetails(data) {
	let html = "";

	if (!data.selected && data.id) {
		html = `
			<h5 class="text-center">Item Details</h5>
			<table class="table table-bordered table-sm table-condensed">
				<tbody>
                    <tr>
                        <th class="text-right" width="50%">Category</th>
						<td class="text-left" width="50%">${data.category_name}</td>
                    </tr>
                    <tr>
                        <th class="text-right" width="50%">Sub-Category</th>
						<td class="text-left" width="50%">${data.subcategory_name}</td>
                    </tr>
                    <tr>
                        <th class="text-right" width="50%">Brand</th>
						<td class="text-left" width="50%">${data.brand}</td>
                    </tr>
                    <tr>
                        <th class="text-right" width="50%">Current Stocks</th>
						<td class="text-left" width="50%">${data.stocks}</td>
                    </tr>
				</tbody>
			</table>
		`;
	}

	$(".item-details").html(html);
}
