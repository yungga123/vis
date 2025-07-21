var table,
	modal,
	form,
	elems,
	_interest,
	_vat_percent,
	_items,
	_select2ModalDropdownParent;

$(document).ready(function () {
	table = "billing_invoice_order_forms_table";
	modal = "billing_invoice_order_forms_modal";
	form = "billing_invoice_order_forms_form";
	elems = [
		"order_form_id",
		"status",
		"due_date",
		"bill_type",
		"billing_amount",
		"payment_method",
		"amount_paid",
		"withholding_tax_percent",
		"withholding_tax",
	];
	_interest = $pjOptions.overdue_interests;
	_vat_percent = $pjOptions.vat_percent;
	_items = [];
	_select2ModalDropdownParent = `#${modal} .modal-content`;

	select2Init("#filter_billing_status");
	select2Init("#filter_bill_type");
	select2Init("#filter_payment_method");

	/* Load dataTable */
	loadDataTable(table, router.billing_invoice_order_forms.list, METHOD.POST);

	$("#btn_add_record").on("click", function () {
		$(`#${modal}`).modal("show");
		$(`#${modal}`).removeClass("edit").addClass("add");
		$(`#${modal} .modal-title`).text("Create Billing Invoice");

		_clearForm();
	});

	/* Quotation via ajax data source */
	_initOrderForms();

	$("#billing_amount").on("keyup", function () {
		$("#with_vat").trigger("change");

		let max = parseFloat($(this).attr("max") || 0);

		clearAlertInForm(["billing_amount"]);

		if (max < parseFloat($(this).val())) {
			const error = {
				billing_amount: `Value must not be greater than ${numberFormat(
					max
				)}!`,
			};

			showAlertInForm(["billing_amount"], error, STATUS.ERROR);
		}
	});

	$("#with_vat").on("change", function () {
		let vat_amount = 0;
		let withholding_tax = parseFloat($("#withholding_tax").val() || 0);
		let billing_amount = parseFloat($("#billing_amount").val() || 0);
		let overdue_interest = parseFloat($("#overdue_interest").val() || 0);

		$("div.with_vat").addClass("d-none");

		if ($(this).is(":checked")) {
			vat_amount = parseFloat(billing_amount * _vat_percent);

			if (vat_amount) $("div.with_vat").removeClass("d-none");
		}

		let total = billing_amount + vat_amount + overdue_interest;

		total = parseFloat(total - withholding_tax).toFixed(2);

		$("#grand_total").val(total);
		$("#amount_paid").val(total);
		$("#vat_amount").val(parseFloat(vat_amount).toFixed(2));
	});

	$("#with_interest").on("change", function () {
		let interest = 0;
		let withholding_tax = parseFloat($("#withholding_tax").val() || 0);
		let billing_amount = parseFloat($("#billing_amount").val() || 0);
		let vat_amount = parseFloat($("#vat_amount").val() || 0);

		$("div.with_interest").addClass("d-none");

		if ($(this).is(":checked")) {
			const daysInterest = parseFloat($("#days_overdue").val() || 0);

			interest = billing_amount * (daysInterest * _interest.per_day);
			interest = parseFloat(interest.toFixed(2));

			if (interest) $("div.with_interest").removeClass("d-none");
		}

		let total = billing_amount + interest + vat_amount;

		total = parseFloat(total - withholding_tax).toFixed(2);

		$("#grand_total").val(total);
		$("#amount_paid").val(total);
		$("#overdue_interest").val(interest);
	});

	$("#withholding_tax_percent").on("keyup", function () {
		let billing_amount = parseFloat($("#billing_amount").val() || 0);
		let wt_percent = parseFloat($(this).val() || 0) / 100;
		let withholding_tax = billing_amount * wt_percent;

		$("#withholding_tax").val(withholding_tax);
		$("#with_vat").trigger("change");
	});

	/* Form for saving record */
	formSubmit($("#" + form), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			_clearForm();
			refreshDataTable($("#" + table));
			notifMsgSwal(res.status, res.message, res.status);

			if ($(`#${modal}`).hasClass("edit")) {
				$(`#${modal}`).modal("hide");
			}
		}

		if (res.errors) notifMsg(res.message, res.status);

		showAlertInForm(elems, message, res.status);
	});
});

/* For filtering and reseting */
function filterData(reset = false) {
	const billing_status = getSelect2Selection("#filter_billing_status");
	const bill_type = getSelect2Selection("#filter_bill_type");
	const payment_method = getSelect2Selection("#filter_payment_method");
	const params = {
		billing_status: billing_status,
		bill_type: bill_type,
		payment_method: payment_method,
	};
	const condition =
		!isEmpty(billing_status) ||
		!isEmpty(bill_type) ||
		!isEmpty(payment_method);

	filterParam(
		router.billing_invoice_order_forms.list,
		table,
		params,
		condition,
		() => {
			clearSelect2Selection("#filter_billing_status");
			clearSelect2Selection("#filter_bill_type");
			clearSelect2Selection("#filter_payment_method");
		},
		reset
	);
}

/* Get record details */
function edit(id, billing_status) {
	let title = "Edit Billing Invoice";
	let route = router.billing_invoice_order_forms.fetch;

	_clearForm();
	clearSelect2Selection("#order_form_id");

	$(`#${modal}`).removeClass("add").addClass("edit");
	$(`#${modal} .modal-title`).text("Edit Billing Invoice");

	if (billing_status && billing_status != "paid") {
		title = "Mark Billing Invoice as PAID";

		$("#billing_status").val("paid");
		$(".form-group.amount_paid label:first-child").addClass("required");
	}

	$(`#${modal} .modal-title`).text(title);

	fetchRecord(route, { id: id }, modal, (res) => {
		if (res.status === STATUS.SUCCESS) {
			if (inObject(res, "data") && !isEmpty(res.data)) {
				const text = `${res.data.order_form_id} | ${
					res.data.client_name
				} | ${res.data.client_branch_name || "N/A"}`;

				$("#id").val(id);
				$("#due_date").val(res.data.due_date);
				$("#billing_amount").val(res.data.billing_amount);
				$("#receipt_number").val(res.data.receipt_number);
				$("#amount_paid").val(
					billing_status
						? res.data.billing_amount
						: res.data.amount_paid
				);
				$("#days_overdue").val(res.data.days_overdue || "");
				$("#overdue_interest").val(res.data.overdue_interest || "");
				$("#vat_amount").val(res.data.vat_amount || "");
				$("#grand_total").val(res.data.grand_total || "");
				$("#withholding_tax_percent").val(
					res.data.withholding_tax_percent || ""
				);
				$("#withholding_tax").val(res.data.withholding_tax || "");
				$("#additional_description").val(
					res.data.additional_description || ""
				);
				$("#with_vat").prop("checked", res.data.with_vat != 0);
				$("#with_vat").trigger("change");
				$("#with_interest").prop(
					"checked",
					res.data.overdue_interest > 0
				);
				$("#with_interest").trigger("change");
				$("#orig_order_form").html(
					`Original Order Form: <strong>${text}</strong>`
				);

				setSelect2AjaxSelection(
					"#order_form_id",
					text,
					res.data.order_form_id
				);
				setTimeout(() => _displayOrderForm(res.data), 200);

				setOptionValue("#bill_type", res.data.bill_type);
				setOptionValue("#payment_method", res.data.payment_method);

				if (billing_status && res.data.billing_status !== "paid") {
					$(".wrapper_paid").removeClass("d-none");
				}

				if (res.data.billing_status === "paid") {
					$(".form-group.amount_paid label:first-child").addClass(
						"required"
					);
					$("#billing_status").val(res.data.billing_status);
					$("#amount_paid").val(res.data.billing_amount);
				}

				if (res.data.billing_status === "overdue") {
					$(".with_interest-checkbox").removeClass("d-none");
				}

				$(`#${modal}`).modal("show");
			}
		}
	});
}

/* Delete record */
function remove(id) {
	deleteRecord(router.billing_invoice_order_forms.delete, { id: id }, table);
}

/* Change status record */
function change(id, changeTo, status) {
	const data = { id: id, status: changeTo };
	const title = `${strUpper(status)} to ${strUpper(changeTo)}!`;

	changeRecord(router.billing_invoice_order_forms.change, data, title, table);
}

/* Quotation via ajax data source */
function _initOrderForms() {
	const options = {
		dropdownParent: _select2ModalDropdownParent,
	};

	select2AjaxInit(
		"#order_form_id",
		"Search & select an order form",
		router.inventory.common.order_forms,
		"text",
		_loadOrderFormDetails,
		options
	);
}

/* Load selected order_form/quotation details */
function _loadOrderFormDetails(data) {
	$(".order_form .initial").html("");

	if (data && !isEmpty(data.id)) {
		_fetchOrderFormItems(data.id);
		_displayOrderForm(data);
	}
}

/* Display order_form details */
function _displayOrderForm(data) {
	let amount = 0;

	if (data.total_amount) {
		amount = parseFloat(data.total_amount || 0);

		let vatText = isEmpty(data.vat_amount) ? "Vat Ex." : "Vat Inc.";
		let vat = parseFloat(data.vat_amount || data.of_vat_amount || 0);
		let total = parseFloat(data.grand_total || data.of_grand_total || 0);
		let html = `
				<div>
					<table class="table table-bordered">
						<thead>
							<tr class="text-bold">
								<td>Purchased At</td>
								<td>Total Discount</td>
								<td>Total Amount</td>
								<td>Vat Amount</td>
								<td>Grand Total (${vatText})</td>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td class="text-bold text-danger">${data.purchase_at}</td>
								<td class="text-bold text-danger">${numberFormat(data.total_discount || 0)}</td>
								<td class="text-bold text-danger">${numberFormat(data.total_amount || 0)}</td>
								<td class="text-bold text-danger">${numberFormat(vat)}</td>
								<td class="text-bold text-danger">${numberFormat(total)}</td>
							</tr>
						</tbody>
					</table>
				</div>
			`;

		$(".order_form .initial").html(html);
	}

	$("#billing_amount").attr("max", amount);
}

/* Display order form items */
function _displayOrderFormItems(id, data) {
	let html = "";

	if (!isEmpty(data)) {
		let rows = "";
		let totalItemPrice = 0;
		let totalSellingPrice = 0;
		let totalQuantity = 0;
		let totalDiscount = 0;
		let grandTotalPrice = 0;

		$.each(data, (index, val) => {
			let itemPrice = parseFloat(val.item_price || 0);
			let sellingPrice = parseFloat(val.selling_price || 0);
			let finalItemPrice = sellingPrice == 0 ? itemPrice : sellingPrice;
			let totalPrice = parseFloat(val.total_price || 0);

			if (totalPrice == 0) {
				totalPrice = parseFloat(val.item_price * val.quantity);
				totalPrice = parseFloat(totalPrice - val.discount);
			}

			rows += `
				<tr>
					<td>${val.inventory_id}</td>
					<td>${val.supplier_name}</td>
					<td>${val.item_model}</td>
					<td>${val.item_description}</td>
					<td>${val.unit || "N/A"}</td>
					<td>${val.size || "N/A"}</td>
					<td>${val.stocks}</td>
					<td>${numberFormat(itemPrice)}</td>
					<td>${numberFormat(finalItemPrice)}</td>
					<td>${val.quantity}</td>
					<td>${numberFormat(val.discount)}</td>
					<td>${numberFormat(totalPrice)}</td>
				</tr>
			`;

			totalItemPrice += itemPrice;
			totalSellingPrice += finalItemPrice;
			totalQuantity += parseFloat(val.quantity);
			totalDiscount += parseFloat(val.discount);
			grandTotalPrice += parseFloat(totalPrice);
		});

		html = `
			<h5 class="text-center">Order Form <strong>#${id}</strong> Items</h5>
			<table class="table table-bordered">
				<thead>
					<tr class="text-bold">
						<td>Item #</td>
						<td>Supplier</td>
						<td>Item Model</td>
						<td>Item Description</td>
						<td>Item Unit</td>
						<td>Item Size</td>
						<td>Current Stocks</td>
						<td>Item Price</td>
						<td>Selling Price</td>
						<td>Quantity</td>
						<td>Discount</td>
						<td>Total Price</td>
					</tr>
				</thead>
				<tbody>${rows}</tbody>
				<tfoot>
					<tr>
						<td colspan="7" class="text-right text-bold">Grand Totals</td>
						<td class="text-bold text-danger">${numberFormat(totalItemPrice)}</td>
						<td class="text-bold text-danger">${numberFormat(totalSellingPrice)}</td>
						<td class="text-bold text-danger">${numberFormat(totalQuantity)}</td>
						<td class="text-bold text-danger">${numberFormat(totalDiscount)}</td>
						<td class="text-bold text-danger">${numberFormat(grandTotalPrice)}</td>
					</tr>
				</tfoot>
			</table>
		`;
	}

	$(".order_form .items").html(html);
}

/* Fetch order form items by id */
function _fetchOrderFormItems(id) {
	if (_items[id]) {
		_displayOrderFormItems(id, _items[id]);
	}

	const data = {
		id: id,
		items: true,
	};

	fetchRecord(router.inventory.order_form.fetch, data, modal, (res) => {
		_items[id] = res.data;

		_displayOrderFormItems(id, res.data);
	});
}

/* Quotation via ajax data source */
function _clearForm() {
	$(`#${form}`)[0].reset();
	$("#id").val("");
	$("#billing_status").val("");
	$("#days_overdue").val("");
	$("#overdue_interest").val("");
	$("#orig_order_form").html("");
	$(".order_form .initial").html("");
	$(".order_form .items").html("");
	$(".wrapper_paid").addClass("d-none");
	$("div.with_vat").addClass("d-none");
	$("div.with_interest").addClass("d-none");
	$(".with_interest-checkbox").addClass("d-none");

	clearSelect2Selection("#order_form_id");
	clearAlertInForm(elems);
}
