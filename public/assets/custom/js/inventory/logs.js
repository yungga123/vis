var tableLogs, modalLogs, formLogs, elemLogs;

$(document).ready(function () {
	const suffix = "inventory_logs";
	tableLogs = "table_" + suffix;
	modalLogs = "#modal_" + suffix;
	formLogs = "#form_" + suffix;
	elemLogs = [
		"item_size_logs",
		"item_sdp_logs",
		"item_srp_logs",
		"project_price_logs",
		"stocks_logs",
		"stock_unit_logs",
		"date_of_purchase_logs",
		"supplier_logs",
		"location_logs",
	];

	select2Init();
	$("#filter_category_logs").on("select2:select", function (e) {
		let selector = "#filter_sub_category_logs";
		dropdownInitLogs(selector, $(this).val());
	});

	/* Load dataTable */
	if ($("#" + tableLogs).length)
		loadDataTable(tableLogs, router.logs.list, METHOD.POST);

	/* Form for saving item in */
	formSubmit($(formLogs), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if ($("#inventory_table").length) tableLogs = "inventory_table";
		if (res.status !== STATUS.ERROR) {
			self[0].reset();
			refreshDataTable($("#" + tableLogs));
			notifMsgSwal(res.status, message, res.status);
			clearSelectionSelect2Logs();
			toggleModalLogs(true);
			$("#inventory_parent_id").val("");
			$("#action_logs").val("");
		}

		showAlertInForm(elems, message, res.status);
	});
});

/* For filtering and reseting */
function filterDataLogs(reset = false) {
	let logs_type = $("#filter_action :selected").val(),
		category = getSelect2Selection("#filter_category_logs");

	showLoading();
	if (!isEmpty(logs_type) || !isEmpty(category)) {
		let options = {
			params: {
				action: logs_type,
				category: category,
				sub_dropdown: getSelect2Selection("#filter_sub_category_logs"),
			},
		};

		if (reset) {
			options.params = null;
			clearSelect2Selection("#filter_category_logs");
			clearSelect2Selection("#filter_sub_category_logs");
			$("#filter_sub_category_logs").html("");
		}

		loadDataTable(tableLogs, router.logs.list, METHOD.POST, options, true);
	} else {
		closeLoading();
		if (reset) return;

		notifMsgSwal(
			TITLE.WARNING,
			"Please select a logs type or category first!",
			STATUS.INFO
		);
	}
	closeLoading();
}

/* Dropdown initialization */
function dropdownInitLogs(select, type) {
	$.post(router.dropdown.show, { dropdown_type: type })
		.then((res) => {
			if (res.status === STATUS.SUCCESS) {
				let dropdowns = "";
				if (inObject(res, "data") && !isEmpty(res.data)) {
					dropdowns = $.map(res.data, function (val, key) {
						return `<option value="${val.dropdown_id}">${val.dropdown}</option>`;
					}).join("");
				}

				if (isSelect2Initialized(select)) $(select).select2("destroy");
				$(select).html("").append(dropdowns);
				$(select).select2().val("").trigger("change");
			} else {
				console.log(res.message);
			}
		})
		.catch((err) => catchErrMsg(err));
}

/* Item In */
function itemIn(id, stock) {
	clearAlertInForm(elemLogs);
	showLoading();

	$.post(router.inventory.edit, { id: id })
		.then((res) => {
			closeLoading();

			if (res.status === STATUS.SUCCESS) {
				// $(modalLogs + " .modal-title").text(
				// 	"Item In - " + res.data.item_description
				// );
				// $(modalLogs + " .modal-dialog").addClass("modal-lg");
				// $(modalLogs + " .modal-body .row").removeClass("d-none");
				// $(modalLogs + " .modal-body .item-out-wrapper").html("");

				// $("#inventory_parent_id").val(id);
				// $("#action_logs").val("ITEM_IN");
				// $("#item_description_logs").val(res.data.item_description);
				// $("#item_brand_logs").val(res.data.item_brand_name);
				// $("#item_model_logs").val(res.data.item_model);
				// $("#item_sdp_logs").val(res.data.item_sdp);
				// $("#item_srp_logs").val(res.data.item_srp);
				// $("#project_price_logs").val(res.data.project_price);
				// // $("#stocks_logs").val(res.data.stocks);
				// $("#parent_stocks").val(res.data.stocks);
				// $("#date_of_purchase_logs").val(res.data.date_of_purchase);
				// $("#location_logs").val(res.data.location);
				// $("#supplier_logs").val(res.data.supplier);
				// $("#encoder_logs").val(res.data.encoder_name);
				// dropdownInitLogs("#item_size_logs", "SIZE", res.data.item_size, true);
				// dropdownInitLogs("#stock_unit_logs", "UNIT", res.data.stock_unit, true);

				const itemDetails = itemDetailsHtml(id, stock, res.data, "in");

				$(modalLogs + " .modal-title").text(
					"Item In - " + res.data.item_description
				);
				$(modalLogs + " .modal-dialog").removeClass("modal-lg");
				$(modalLogs + " .modal-body .row").addClass("d-none");
				$(modalLogs + " .modal-body .item-details-wrapper").html(itemDetails);
				$("#inventory_parent_id").val(id);
				$("#action_logs").val("ITEM_IN");
				$("#item_sdp_logs").val(res.data.item_sdp);
				$("#item_srp_logs").val(res.data.item_srp);
				$("#project_price_logs").val(res.data.project_price);
				$("#parent_stocks").val(res.data.stocks);
				$("#date_of_purchase_logs").val(res.data.date_of_purchase);
				$("#location_logs").val(res.data.location);
				$("#supplier_logs").val(res.data.supplier);
				toggleModalLogs();
			} else {
				toggleModalLogs(true);
				notifMsgSwal(res.status, res.message, res.status);
			}
		})
		.catch((err) => catchErrMsg(err));
}

/* Item Out */
function itemOut(id, stock) {
	if (parseFloat(stock) <= 0) {
		notifMsgSwal(
			TITLE.WARNING,
			"This item has zero stock! Need to add items first.",
			STATUS.INFO
		);

		return;
	}

	clearAlertInForm(elemLogs);
	showLoading();

	$.post(router.inventory.edit, { id: id })
		.then((res) => {
			closeLoading();

			if (res.status === STATUS.SUCCESS) {
				const itemDetails = itemDetailsHtml(id, stock, res.data, "out");
				$(modalLogs + " .modal-title").text(
					"Item Out - " + res.data.item_description
				);
				$(modalLogs + " .modal-dialog").removeClass("modal-lg");
				$(modalLogs + " .modal-body .row").addClass("d-none");
				$(modalLogs + " .modal-body .item-details-wrapper").html(itemDetails);
				$("#inventory_parent_id").val(id);
				$("#action_logs").val("ITEM_OUT");
				$("#item_sdp_logs").val(res.data.item_sdp);
				$("#item_srp_logs").val(res.data.item_srp);
				$("#project_price_logs").val(res.data.project_price);
				$("#parent_stocks").val(res.data.stocks);
				$("#date_of_purchase_logs").val(res.data.date_of_purchase);
				$("#location_logs").val(res.data.location);
				$("#supplier_logs").val(res.data.supplier);
				toggleModalLogs();
			} else {
				toggleModalLogs(true);
				notifMsgSwal(res.status, res.message, res.status);
			}
		})
		.catch((err) => catchErrMsg(err));
}

/* Item details */
function itemDetailsHtml(id, stock, data, action) {
	action = action || "in";

	let max = action === "in" ? "" : stock;
	let details = `
		<h4 class="text-center">Item Details</h4>
		<table class="table">
			<tbody>
				<tr>
					<th>Item Number:</th>
					<td>${id}</td>
				</tr>
				<tr>
					<th>Item Description:</th>
					<td>${data.item_description}</td>
				</tr>
				<tr>
					<th>Item Brand:</th>
					<td>${data.item_brand_name}</td>
				</tr>
				<tr>
					<th>Item Model:</th>
					<td>${data.item_model}</td>
				</tr>
				<tr>
					<th>Dealer's Price:</th>
					<td>${data.item_sdp}</td>
				</tr>
				<tr>
					<th>Current Stocks:</th>
					<td>${data.stocks}</td>
				</tr>
				<tr>
					<th>Quantity (${action.toUpperCase()}):</th>
					<td>
						<input type="number" class="form-control" name="quantity" id="quantity_logs" placeholder="Quantity here" min="1" max="${max}" onkeyup="checkInputQuantity(this.value, ${stock}, '${action}')" required/>
						<small id="alert_quantity_logs" class="text-danger"></small>
					</td>
				</tr>
			</tbody>
		</table>
		<input type="hidden" name="item_size_logs_${stock}" value="${
		data.item_size
	}" readonly>
		<input type="hidden" name="stock_unit_logs_${stock}" value="${
		data.stock_unit
	}" readonly>
	`;

	return details;
}

function checkInputQuantity(val, stock, action) {
	$("#quantity_logs").addClass("is-valid").removeClass("is-invalid");
	$("#alert_quantity_logs").text("");

	if (
		(action === "out" && parseFloat(val) > stock) ||
		(action === "in" && (isEmpty(val) || parseFloat(val) == 0))
	) {
		let message =
			action === "in"
				? "should be at least 1!"
				: "is greater than the current stocks!";

		$("#quantity_logs").removeClass("is-valid").addClass("is-invalid");
		$("#alert_quantity_logs").text("Entered quantity " + message);
	}
}

/* Toggling the modal */
function toggleModalLogs(close = false) {
	if (close) $(modalLogs).modal("hide");
	$(modalLogs).modal("show");
}

function clearSelectionSelect2Logs() {
	$("#item_size_logs").val("").trigger("change");
	$("#stock_unit_logs").val("").trigger("change");
}
