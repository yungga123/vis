var table,
	modal,
	form,
	elems,
	joSelector,
	clientRoute,
	clientBranchRoute,
	unitCondItems,
	$items = {};

$(document).ready(function () {
	table = "service_report_table";
	modal = "service_report_modal";
	form = "service_report_form";
	elems = [
		"job_order_id",
		"serial_number",
		"server_type",
		"service_type",
		"area",
		"arrival_date",
		"arrival_time",
		"error_description",
		"corrective_action",
		"remarks",
		"labor",
		"travel_time",
		"time_out",
	];
	joSelector = "#job_order_id";
	clientRoute = router.clients.common.customers;
	clientBranchRoute = router.clients.common.customer_branches;
	unitCondItems = $pjOptions.unitc_items;

	/* Filters */
	select2Init("#filter_status");
	select2Init("#filter_service_type");

	/* Load dataTable */
	loadDataTable(table, router.service_report.list, METHOD.POST);

	/* Toggle modal */
	$("#btn_add_record").on("click", function () {
		$(`#${modal}`).modal("show");
		$(`#${modal}`).removeClass("edit").addClass("add");
		$(`#${modal} .modal-title`).text("Add Service Report");

		_clearForm();
	});

	/* Initial init of customers (commerical) via ajax data source */
	initSelect2Customers(clientRoute);
	onChangeCustomerType();
	onSelectCustomer();
	onClearCustomer();
	initSelect2CustomerBranches(clientBranchRoute);

	/* Job Orders select2 via ajax data source */
	initSelect2JobOrders(router.admin.common.job_orders);

	/* Form for saving job order */
	formSubmit($("#" + form), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			refreshDataTable($("#" + table));
			notifMsgSwal(res.status, message, res.status);
			_clearForm();

			if ($(`#${modal}`).hasClass("edit")) {
				$(`#${modal}`).modal("hide");
			}
		}

		showAlertInForm(elems, message, res.status);
	});

	/* For saving job order status form */
	formSubmit($("#status_form"), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			self[0].reset();
			refreshDataTable($("#" + table));
			notifMsgSwal(res.status, message, res.status);

			$("#status_modal").modal("hide");
			$("#status").val("");
		}

		showAlertInForm(elems, message, res.status);
	});
});

/* For filtering and reseting */
function filterData(reset = false) {
	const status = getSelect2Selection("#filter_status");
	const area = getSelect2Selection("#filter_area");
	const service_type = getSelect2Selection("#filter_service_type");
	const params = {
		status: status,
		area: area,
		service_type: service_type,
	};
	const condition =
		!isEmpty(status) || !isEmpty(area) || !isEmpty(service_type);

	filterParam(
		router.service_report.list,
		table,
		params,
		condition,
		() => {
			clearSelect2Selection("#filter_status");
			clearSelect2Selection("#filter_area");
			clearSelect2Selection("#filter_service_type");
		},
		reset
	);
}

/* Get record details */
function edit(id) {
	$(`#${modal}`).removeClass("add").addClass("edit");
	$(`#${modal} .modal-title`).text("Edit Service Report");

	_clearForm();
	fetchRecord(router.service_report.fetch, { id: id }, modal, (res) => {
		if (res.status === STATUS.SUCCESS) {
			initSelect2JobOrders(router.admin.common.job_orders);
			setSelect2AjaxSelection(
				joSelector,
				res.data.job_order_id + " | " + res.data.client_name,
				res.data.job_order_id
			);

			$("#id").val(id);
			$("#serial_number").val(res.data.serial_number);
			$("#server_type").val(res.data.server_type);
			$("#service_type").val(res.data.service_type);
			$("#area").val(res.data.area);
			$("#arrival_date").val(res.data.arrival_date);
			$("#arrival_time").val(res.data.arrival_time);
			$("#error_description").val(res.data.error_description);
			$("#corrective_action").val(res.data.corrective_action);
			$("#remarks").val(res.data.remarks);
			$("#labor").val(res.data.labor);
			$("#travel_time").val(res.data.travel_time);
			$("#time_out").val(res.data.time_out);

			// Populate units & items
			_populateUnitsItems(res.data.units, "unit");
			_populateUnitsItems(res.data.items, "item");

			$(`#${modal}`).modal("show");
		} else {
			$(`#${modal}`).modal("hide");

			notifMsgSwal(res.status, res.message, res.status);
		}
	});
}

/* Delete record */
function remove(id) {
	deleteRecord(router.service_report.delete, { id: id }, table);
}

/* View units & items */
function view(id) {
	const _modal = "service_report_details_modal";
	const _data = { id: id, items: true };

	fetchRecord(router.service_report.fetch, _data, _modal, (res) => {
		if (res.status === STATUS.SUCCESS) {
			// Populate units & items
			_populateUnits(res.data.units, "unit");
			_populateItems(res.data.items, "item");

			$(`#${_modal}`).modal("show");
		} else {
			$(`#${_modal}`).modal("hide");

			notifMsgSwal(res.status, res.message, res.status);
		}
	});
}

/* Change status record */
function change(id, changeTo, status) {
	const data = { id: id, status: changeTo };

	changeRecord(router.service_report.change, data, null, table);
}

/* Toggle unit condition field */
function toggleItemField(row, param) {
	param = param || "unit";

	let selector = param === "unit" ? "table.unit-condition" : "table.items";
	let table = $(`#${modal} ${selector} tbody`);
	let count = table.find("tr").length;

	if (count > 1) count + 1;

	if (row) {
		table.children("tr#row_" + row).remove();

		return;
	}

	const trId = `row_${count}`;

	if (param === "unit") {
		_unitsFieldRow(table, trId, count);
	} else {
		_itemsFieldRow(table, trId, count);
	}
}

/* Unit condition items options */
function calculate(value, trId, param) {
	value = parseFloat(value);

	const qtyOrPrice = parseFloat(
		$(`#${trId} td.${param} input[name="${param}[]"]`).val() || 0
	);
	const totalPrice = qtyOrPrice * value;

	$(`#${trId} td.item_total_price input.item_total_price`).val(
		decimalFormat(totalPrice)
	);

	calculateGrandTotals();
}

/* Calculate grand totals and display */
function calculateGrandTotals() {
	let totalQty = 0,
		totalPrice = 0,
		grandTotal = 0;

	const tbody = $(`#${modal} table.items tbody`);
	const count = tbody.find("tr").length;

	for (let i = 0; i < count; i++) {
		const trElem = $(tbody.find("tr")[i]);

		const qtyVal = trElem.find("td.item_qty > input.item_qty").val();
		const priceVal = trElem
			.find("td.item_unit_price > input.item_unit_price")
			.val();
		const totalVal = trElem
			.find("td.item_total_price > input.item_total_price")
			.val();

		totalQty += parseFloat(qtyVal || 0);
		totalPrice += parseFloat(priceVal || 0);
		grandTotal += parseFloat(totalVal || 0);
	}

	const tfoot = `#${modal} table.items tfoot`;

	$(`${tfoot} td.total_qty`).text(numberFormat(totalQty));
	$(`${tfoot} td.total_price`).text(numberFormat(totalPrice));
	$(`${tfoot} td.grand_total`).text(numberFormat(grandTotal));
}

/* Clear form */
function _clearForm() {
	$("#" + form)[0].reset();
	$("#id").val("");
	$(".item-row").remove();

	clearSelect2Selection(joSelector);
	clearAlertInForm(elems);

	const tfoot = `#${modal} table.items tfoot`;

	$(`${tfoot} td.total_qty`).text("");
	$(`${tfoot} td.total_price`).text("");
	$(`${tfoot} td.grand_total`).text("");
}

/* Unit condition item field row */
function _unitsFieldRow(table, trId, count) {
	const html = `
		<tr class="item-row" id="${trId}">
			<td class="item">
				<select class="custom-select item" name="item[]" style="width: 100%;">
					${_unitConditionItems()}
				</select>
			</td>
			<td class="item_status">
				<select class="form-control" name="item_status[]" style="width: 100%;" required>
					<option value="GOOD">GOOD</option>
					<option value="DEFECTIVE">DEFECTIVE</option>
				</select>
			</td>
			<td class="defective_description">
				<input type="text" name="defective_description[]" class="form-control defective_description" placeholder="Specify">
			</td>
			<td>
				<button type="button" class="btn btn-sm btn-danger" onclick="toggleItemField(${count}, 'unit')" title="Remove item field">
					<i class="fas fa-minus"></i>
				</button>
			</td>
		</tr>
	`;

	table.append(html);
}

/* Unit condition item field row */
function _itemsFieldRow(table, trId, count) {
	const html = `
		<tr class="item-row" id="${trId}">
			<td class="item_no">
				<input type="text" name="item_no[]" class="form-control item_no" placeholder="Item No.">
			</td>
			<td class="item_description">
				<input type="text" name="item_description[]" class="form-control item_description" placeholder="Description">
			</td>
			<td class="item_qty">
				<input type="text" name="item_qty[]" class="form-control item_qty" placeholder="Qty" onkeyup="calculate(this.value, '${trId}', 'item_unit_price')">
			</td>
			<td class="item_unit_price">
				<input type="number" name="item_unit_price[]" class="form-control item_unit_price" placeholder="Unit price" step="0.01" onkeyup="calculate(this.value, '${trId}', 'item_qty')">
			</td>
			<td class="item_total_price">
				<input type="number" name="item_total_price[]" class="form-control item_total_price" placeholder="Total price" step="0.01" readonly>
			</td>
			<td>
				<button type="button" class="btn btn-sm btn-danger" onclick="toggleItemField(${count}, 'items')" title="Remove item field">
					<i class="fas fa-minus"></i>
				</button>
			</td>
		</tr>
	`;

	table.append(html);
}

/* Unit condition items options */
function _unitConditionItems() {
	let options = `<option selected value="">Please choose an option</option>`;

	$.each(unitCondItems, (key, val) => {
		options += `<option value="${key}">${val}</option>`;
	});

	return options;
}

/* Populate units/items */
function _populateUnitsItems(vals, param) {
	if (vals && !isEmpty(vals)) {
		// Add unit fields
		for (let i = 1; i < vals.length; i++) toggleItemField(null, param);

		if (param === "unit") {
			_populateUnits(vals);
		} else {
			_populateItems(vals);
		}
	}
}

/* Populate units */
function _populateUnits(units, isView) {
	if (isView) {
		let _rows = "";
		let _modal = "#service_report_details_modal";

		if (!isEmpty(units)) {
			$.each(units, (key, val) => {
				_rows += `
					<tr>
						<td>${val.item}</td>
						<td>${val.status}</td>
						<td>${val.defective_description || "N/A"}</td>
					</tr>
				`;
			});
		} else {
			_rows = `
				<tr>
					<td colspan="3" class="text-center">No units found...</td>
				</tr>
			`;
		}

		$(_modal + " table.unit-condition tbody").html(_rows);

		return;
	}

	const tbodyElem = `#${modal} table.unit-condition tbody`;
	const itemElems = $(`${tbodyElem} tr td > select.item`);

	// Populate
	for (let i = 0; i < itemElems.length; i++) {
		const elem = itemElems[i];
		const unit = units[i];
		const trId = "row_" + i;

		$(elem).val(unit.item);
		$(`${tbodyElem} tr#${trId} td > input.item_status`).val(unit.status);
		$(`${tbodyElem} tr#${trId} td > input.defective_description`).val(
			unit.defective_description
		);
	}
}

/* Populate items */
function _populateItems(items, isView) {
	let totalQty = 0;
	let totalPrice = 0;
	let grandTotal = 0;

	const tfootElem = `#${modal} table.items tfoot`;

	if (isView) {
		let _rows = "";
		let _modal = "#service_report_details_modal";

		if (!isEmpty(items)) {
			$.each(items, (key, val) => {
				_rows += `
					<tr>
						<td>${val.item_no || "N/A"}</td>
						<td>${val.item_description || "N/A"}</td>
						<td>${numberFormat(val.item_qty || 0)}</td>
						<td>${numberFormat(val.item_unit_price || 0)}</td>
						<td>${numberFormat(val.item_total_price || 0)}</td>
					</tr>
				`;
				totalQty += parseFloat(val.item_qty || 0);
				totalPrice += parseFloat(val.item_unit_price || 0);
				grandTotal += parseFloat(val.item_total_price || 0);
			});
		} else {
			_rows = `
				<tr>
					<td colspan="5" class="text-center">No items found...</td>
				</tr>
			`;
		}

		$(_modal + " table.items tbody").html(_rows);
		$(`${tfootElem} td.total_qty`).text(numberFormat(totalQty));
		$(`${tfootElem} td.total_price`).text(numberFormat(totalPrice));
		$(`${tfootElem} td.grand_total`).text(numberFormat(grandTotal));

		return;
	}

	const tbodyElem = `#${modal} table.items tbody`;
	const itemNoElems = $(`${tbodyElem} tr td > input.item_no`);

	// Populate
	for (let i = 0; i < itemNoElems.length; i++) {
		const elem = itemNoElems[i];
		const item = items[i];
		const trId = "row_" + i;

		$(elem).val(item.item_no);
		$(`${tbodyElem} tr#${trId} td input.item_description`).val(
			item.item_description
		);
		$(`${tbodyElem} tr#${trId} td input.item_qty`).val(item.item_qty);
		$(`${tbodyElem} tr#${trId} td input.item_unit_price`).val(
			item.item_unit_price
		);
		$(`${tbodyElem} tr#${trId} td input.item_total_price`).val(
			item.item_total_price
		);

		totalQty += parseFloat(item.item_qty || 0);
		totalPrice += parseFloat(item.item_unit_price || 0);
		grandTotal += parseFloat(item.item_total_price || 0);
	}

	$(`${tfootElem} td.total_qty`).text(numberFormat(totalQty));
	$(`${tfootElem} td.total_price`).text(numberFormat(totalPrice));
	$(`${tfootElem} td.grand_total`).text(numberFormat(grandTotal));
}
