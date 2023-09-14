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
		dropdownInitLogs2(selector, $(this).val());
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
			toggleModalLogs(true);

			$("#inventory_parent_id").val("");
			$("#action_logs").val("");
			$("#parent_stocks_logs").val("");
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

/* Dropdown initialization */
function dropdownInitLogs2(select, type) {
	$.post(router.dropdown.show, { dropdown_type: type })
		.then((res) => {
			if (res.status === STATUS.SUCCESS) {
				const options = formatOptionsForSelect2(
					res.data,
					"dropdown_id",
					"dropdown"
				);
				select2Reinit(select, "", options);
				setSelect2Selection(select, val);
			} else {
				console.log(res.message);
			}
		})
		.catch((err) => catchErrMsg(err));
}

/* Item In */
function itemIn(id, stock) {
	fetchItemDetails(id, stock, "in");
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

	fetchItemDetails(id, stock, "out");
}

/* Get inventory item details */
function fetchItemDetails(id, stock, action) {
	clearAlertInForm(elemLogs);
	showLoading();

	$.post(router.inventory.edit, { id: id })
		.then((res) => {
			closeLoading();

			if (res.status === STATUS.SUCCESS) {
				const itemDetails = itemDetailsHtml(id, stock, res.data, action);
				$(modalLogs + " .modal-title").text(
					`Item ${strCapitalize(action)} - ` + res.data.item_description
				);
				$(modalLogs + " .modal-dialog").removeClass("modal-lg");
				$(modalLogs + " .modal-body .row").addClass("d-none");
				$(modalLogs + " .modal-body .item-details-wrapper").html(itemDetails);
				$("#inventory_parent_id").val(id);
				$("#parent_stocks_logs").val(stock);
				$("#action_logs").val("ITEM_" + strUpper(action));
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

	const max = action === "in" ? "" : stock;
	const itemStatusFields =
		action === "out"
			? ""
			: `	
				<tr>
					<th>Status:</th>
					<td>
						<select name="status_logs" id="status_logs" class="form-control" required>
							<option value="purchase">Purchase</option>
							<option value="return">Return</option>
						</select>
					</td>
				</tr>
				<tr>
					<th>Status Date:</th>
					<td>
						<input type="date" class="form-control" name="status_date_logs" id="status_date_logs" required/>
					</td>
				</tr>
			`;
	const details = `
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
					<td>${data.brand}</td>
				</tr>
				<tr>
					<th>Item Model:</th>
					<td>${data.item_model}</td>
				</tr>
				<tr>
					<th>Current Stocks:</th>
					<td>${data.stocks}</td>
				</tr>
				<tr>
					<th>Quantity (${strUpper(action)}):</th>
					<td>
						<input type="number" class="form-control" name="quantity" id="quantity_logs" placeholder="Quantity here" min="1" max="${max}" onkeyup="checkInputQuantity(this.value, ${stock}, '${action}')" required/>
						<small id="alert_quantity_logs" class="text-danger"></small>
					</td>
				</tr>
				${itemStatusFields}
			</tbody>
		</table>
	`;

	return details;
}

/* Validation */
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
