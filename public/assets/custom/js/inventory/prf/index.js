var table, modal, form, elems, $technicians;

$(document).ready(function () {
	table = "prf_table";
	modal = "prf_modal";
	form = "prf_form";
	elems = ["inventory_id", "quantity_out", "process_date"];

	/* Load dataTable */
	loadDataTable(table, router.prf.list, METHOD.POST);

	/* Toggle modal */
	$("#btn_add_record").on("click", function () {
		$(`#${modal}`).modal("show");
		$(`#${modal}`).removeClass("edit").addClass("add");
		$(`#${modal} .modal-title`).text("Add PRF");
		$(`#${form}`)[0].reset();
		$("#prf_id").val("");
		$("#orig_item").addClass("d-none");
		$(".item-details").html("");
		clearSelect2Selection("#inventory_id");

		clearAlertInForm(elems);
	});

	/* Masterlist select2 via ajax data source */
	select2AjaxInit(
		"#inventory_id",
		"Search & select an item",
		router.inventory.common.masterlist,
		"text",
		_loadItemDetails
	);

	/* Form for saving record */
	formSubmit($("#" + form), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			self[0].reset();
			refreshDataTable($("#" + table));
			notifMsgSwal(res.status, message, res.status);
			$("#prf_id").val("");
			$("#orig_item").addClass("d-none");
			$(".item-details").html("");
			clearSelect2Selection("#inventory_id");

			if ($(`#${modal}`).hasClass("edit")) {
				$(`#${modal}`).modal("hide");
			}
		}

		showAlertInForm(elems, message, res.status);
	});
});

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
				// Set selected item in select2
				setSelect2AjaxSelection(
					"#inventory_id",
					res.data.text,
					res.data.inventory_id
				);

				$.each(res.data, (key, value) => $(`input[name="${key}"]`).val(value));
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

/* Load selected item details */
function _loadItemDetails(data) {
	let html = "";

	if (!data.selected && data.id) {
		html = `
			<h5 class="text-center">Details</h5>
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
