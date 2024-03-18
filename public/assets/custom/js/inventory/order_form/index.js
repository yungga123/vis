var table,
	modal,
	form,
	elems,
	clientSelector,
	invSelector,
	itemFieldTable,
	_currentFetchItems,
	_vat_percent,
	_status;

$(document).ready(function () {
	table = "order_form_table";
	modal = "order_form_modal";
	form = "order_form_form";
	elems = ["customer_id", "customer_branch_id", "with_vat", "remarks"];
	clientSelector = "#customer_id";
	invSelector = ".inventory_id";
	itemFieldTable = $("#item_field_table tbody");
	_currentFetchItems = {};
	_grandTotals = {};
	_vat_percent = $pjOptions.vat_percent;
	_status = $pjOptions.status;

	/* Load dataTable */
	loadDataTable(table, router.order_form.list, METHOD.POST);

	/* Init filter */
	select2Init("#filter_status");
	select2Init("#filter_with_vat");

	/* Toggle modal */
	$("#btn_add_record").on("click", function () {
		$(`#${modal}`).modal("show");
		$(`#${modal}`).removeClass("edit").addClass("add");
		$(`#${modal} .modal-title`).text("Add PRF");
		$(`#${form}`)[0].reset();
		$("#id").val("");
		$("#orig_item").addClass("d-none");
		$(".job-order-details").html("");
		$(".item-row").remove();
		$(".original-item").html("");

		clearSelect2Selection(clientSelector);
		clearSelect2Selection(invSelector);
		clearAlertInForm(elems);
	});

	/* Initial init of customers (commerical) via ajax data source */
	initSelect2Customers();
	onChangeCustomerType();

	$(clientSelector).on("select2:select", function () {
		$("#client_branch_wrapper").addClass("d-none");

		const customer_type = $('input[name="customer_type"]:checked').val();

		if (customer_type === "commercial") {
			_initSelect2CustomerBranches($(this).val());

			$("#client_branch_wrapper").removeClass("d-none");
		}
	});

	/* Masterlist select2 via ajax data source */
	_initInventorySelect2();

	$("#with_vat").on("change", function () {
		_computeVat(this);
	});

	/* Form for saving record */
	formSubmit($("#" + form), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			self[0].reset();
			refreshDataTable($("#" + table));
			notifMsgSwal(res.status, message, res.status);
			_formReset();

			if ($(`#${modal}`).hasClass("edit")) {
				$(`#${modal}`).modal("hide");
			}
		}

		showAlertInForm(elems, message, res.status);
	});
});

/* For filtering and reseting */
function filterData(reset = false) {
	const status = getSelect2Selection("#filter_status");
	const with_vat = getSelect2Selection("#filter_with_vat");
	const start_date = $("#filter_start_date").val();
	const end_date = $("#filter_end_date").val();
	const params = {
		status: status,
		with_vat: with_vat,
		start_date: start_date,
		end_date: end_date,
	};
	const condition =
		!isEmpty(status) ||
		!isEmpty(with_vat) ||
		(!isEmpty(start_date) && !isEmpty(end_date));

	filterParam(
		router.order_form.list,
		table,
		params,
		condition,
		() => {
			clearSelect2Selection("#filter_status");
			clearSelect2Selection("#filter_with_vat");
			$("#filter_start_date").val("");
			$("#filter_end_date").val("");
		},
		reset
	);
}

/* Get items */
function view(id, changeTo, status) {
	$(`#order_form_items_modal .modal-title`).html("Order Form Items");
	$("#order_form_items_modal .modal-footer .change-btn").html("");
	$("#note_item_out").html("");

	if (inArray(_status.get, changeTo)) {
		const markAs = strUpper(_status.set[changeTo]);
		const onclick = `onclick="change(${id}, '${changeTo}', '${status}', ${true})"`;

		if (changeTo === "item_out") {
			const note = `
				Please review the items first before you proceed to <strong>${markAs}</strong>! 
				Click the <button type="button" class="btn btn-sm btn-warning" onclick="edit(${id})" title="Edit"><i class="fas fa-edit"></i></button> button/icon to update details.
			`;
			$("#note_item_out").html(note);
		}

		$(`#order_form_items_modal .modal-title`).html(
			`Change Status from ${strUpper(status)} to ${strUpper(changeTo)}`
		);

		// Append button
		$("#order_form_items_modal .modal-footer .change-btn").html(`
			<button type="button" class="btn btn-success" id="btn_${changeTo}" ${onclick}>
				Mark as ${markAs}
			</button>	
		`);
	}

	showLoading();

	const data = { id: id, items: true };

	$.post(router.order_form.fetch, data)
		.then((res) => {
			closeLoading();

			if (res.status === STATUS.SUCCESS) {
				let html = "";

				if (!isEmpty(res.data)) {
					let totalItemPrice = 0;
					let totalQuantity = 0;
					let totalDiscount = 0;
					let grandTotalPrice = 0;

					$.each(res.data, (index, val) => {
						let totalPrice = parseFloat(val.total_price || 0);

						if (totalPrice == 0) {
							totalPrice = parseFloat(val.item_price * val.quantity);
							totalPrice = parseFloat(totalPrice - val.discount);
						}

						html += `
							<tr>
								<td>${val.inventory_id}</td>
								<td>${val.supplier_name}</td>
								<td>${val.item_model}</td>
								<td>${val.item_description}</td>
								<td>${val.unit || "N/A"}</td>
								<td>${val.size || "N/A"}</td>
								<td>${val.stocks}</td>
								<td>${val.item_price}</td>
								<td>${val.quantity}</td>
								<td>${numberFormat(val.discount)}</td>
								<td>${numberFormat(totalPrice)}</td>
							</tr>
						`;

						totalItemPrice += parseFloat(val.item_price);
						totalQuantity += parseFloat(val.quantity);
						totalDiscount += parseFloat(val.discount);
						grandTotalPrice += parseFloat(totalPrice);
					});

					$(`#order_form_items_table tfoot`).removeClass("d-none");
					$(`#order_form_items_table td.total_item_price`).text(
						numberFormat(totalItemPrice)
					);
					$(`#order_form_items_table td.total_quantity`).text(
						numberFormat(totalQuantity)
					);
					$(`#order_form_items_table td.total_discount`).text(
						numberFormat(totalDiscount)
					);
					$(`#order_form_items_table td.grand_total`).text(
						numberFormat(grandTotalPrice)
					);
				} else {
					html =
						'<tr><td colspan="11" align="center">No items found...</td></tr>';
					$(`#order_form_items_table tfoot`).addClass("d-none");
				}

				$(`#order_form_items_table tbody`).html(html);
				$(`#order_form_items_modal`).modal("show");
			} else {
				$(`#order_form_items_modal`).modal("hide");

				notifMsgSwal(res.status, res.message, res.status);
			}
		})
		.catch((err) => catchErrMsg(err));
}

/* Get record details */
function edit(id) {
	_currentFetchItems = {};

	$("#order_form_items_modal").modal("hide");
	$(`#${modal}`).removeClass("add").addClass("edit");
	$(`#${modal} .modal-title`).text("Edit Order Form");

	_formReset();
	fetchRecord(router.order_form.fetch, { id: id }, modal, (res) => {
		if (res.status === STATUS.SUCCESS) {
			const customer_type = strLower(res.data.customer_type);

			// Set selected client in select2
			$("#" + customer_type).prop("checked", true);

			initSelect2Customers(customer_type);
			setSelect2AjaxSelection(
				clientSelector,
				res.data.customer_name,
				res.data.customer_id
			);

			if (
				!isEmpty(res.data.customer_branch_id) &&
				customer_type === "commercial"
			) {
				_initSelect2CustomerBranches(
					res.data.customer_id,
					res.data.customer_branch_id
				);
				$("#client_branch_wrapper").removeClass("d-none");
			}

			$("#id").val(id);
			$("#purchase_date").val(res.data.purchase_date);
			$("#purchase_time").val(res.data.purchase_time);
			$("#remarks").val(res.data.remarks);
			$("#grand_total").val(res.data.grand_total);
			$("#vat_amount").val(res.data.vat_amount);
			$("#with_vat").prop("checked", res.data.with_vat != 0 ? true : false);

			if (!isEmpty(res.data.items)) {
				const items = res.data.items;

				// Add item fields
				for (let i = 1; i < items.length; i++) toggleItemField();

				// Populate data
				const itemFields = $(".inventory_id");

				for (let x = 0; x < itemFields.length; x++) {
					const itemElem = itemFields[x];
					const item = items[x];
					const text = `${item.inventory_id} | ${item.item_model} | ${
						item.item_description
					} | ${item.size || "N/A"}`;
					const trId = "row_" + x;
					let totalPrice = parseFloat(item.total_price || 0);

					if (totalPrice == 0) {
						totalPrice = parseFloat(item.item_price * item.quantity);
						totalPrice = parseFloat(totalPrice - item.discount);
					}

					// Store items in a variable with inventory_id as key
					_currentFetchItems[trId] = item;
					_currentFetchItems[trId].total_price = totalPrice;

					// Set selected item in each select2
					setSelect2AjaxSelection(itemElem, text, item.inventory_id);

					// Display the selected item in a div under each select2
					$(itemElem)
						.parent()
						.children(".original-item")
						.html(`Original Item: <strong>${text}</strong>`);

					// Set value and text
					$(`#${modal} tr#${trId} td.quantity input.quantity`).val(
						parseFloat(item.quantity)
					);
					$(`#${modal} tr#${trId} td.discount input.discount`).val(
						parseFloat(item.discount)
					);
					$(`#${modal} tr#${trId} td.item_stocks`).text(item.stocks);
					$(`#${modal} tr#${trId} td.item_price input`).text(
						numberFormat(item.item_price)
					);
					$(`#${modal} tr#${trId} td.item_price span`).text(
						numberFormat(item.item_price)
					);
					$(`#${modal} tr#${trId} td.total_price input`).val(totalPrice);
					$(`#${modal} tr#${trId} td.total_price span`).text(
						numberFormat(totalPrice)
					);

					calculateGrandTotals();
				}
			}

			$("#total_amount").val(res.data.total_amount);
			$("#total_discount").val(res.data.total_discount);
			$("#total_amount_no_discount").val(
				parseFloat(res.data.total_amount + res.data.total_discount)
			);
			$(`#${modal}`).modal("show");
		} else {
			$(`#${modal}`).modal("hide");

			notifMsgSwal(res.status, res.message, res.status);
		}
	});
}

/* Delete record */
function remove(id) {
	deleteRecord(router.order_form.delete, { id: id }, table);
}

/* Change status record */
function change(id, changeTo, status, proceed) {
	const allowedInView = _status.get;

	if (inArray(allowedInView, changeTo) && !proceed) {
		$("#_id").val(id);
		$("#_status").val(changeTo);

		// Display the items details
		view(id, changeTo, status);
		return;
	}

	const title = `${strUpper(status)} to ${strUpper(changeTo)}!`;
	const swalMsg = `
		<div>PRF #: <strong>${id}</strong></div>
		<div>Are you sure you want to <strong>${strUpper(
			changeTo
		)}</strong> this Order Form?</div>
	`;
	const data = { id: id, status: changeTo };

	swalNotifConfirm(
		() => {
			$.post(router.order_form.change, data)
				.then((res) => {
					const message = res.errors ?? res.message;

					notifMsgSwal(res.status, message, res.status);

					if (res.status !== STATUS.ERROR) {
						refreshDataTable($("#" + table));

						$("#order_form_items_modal").modal("hide");
					}
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
	let itemFieldCount = itemFieldTable.find("tr").length;

	if (itemFieldCount > 1) itemFieldCount + 1;

	if (row) {
		itemFieldTable.children("tr#row_" + row).remove();

		delete _currentFetchItems["row_" + row];

		calculateGrandTotals();

		return;
	}

	const trId = `row_${itemFieldCount}`;
	const html = `
		<tr class="item-row" id="${trId}">
			<td>
				<select class="custom-select inventory_id" name="inventory_id[]" style="width: 100%;"></select>
				<div class="original-item"></div>
			</td>
			<td class="text-center item_stocks"></td>
			<td class="item_price">
				<span></span>
				<input type="hidden" name="item_price[]" readonly>
			</td>
			<td class="quantity">
				<input type="number" name="quantity[]" class="form-control quantity" placeholder="Quantity" min="1" step="0.5" onkeyup="calculate(this.value, '${trId}', 'quantity')" required>
			</td>
			<td class="discount">
				<input type="number" name="discount[]" class="form-control discount" placeholder="Discount" step="0.01" onkeyup="calculate(this.value, '${trId}', 'discount')">
			</td>
			<td class="total_price">
				<span></span>
				<input type="hidden" name="total_price[]" readonly>
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

/* Toggle item field */
function calculate(val, trId, field) {
	if (!isEmpty(_currentFetchItems)) {
		const isQty = field === "quantity";
		const item_price = parseFloat(_currentFetchItems[trId].item_price || 0);
		const quantity = parseFloat(
			(isQty
				? val
				: $(`#${modal} tr#${trId} td.quantity input.quantity`).val()) || 0
		);
		const discount = parseFloat(
			(!isQty
				? val
				: $(`#${modal} tr#${trId} td.discount input.discount`).val()) || 0
		);
		let total_price = parseFloat(item_price * quantity);
		total_price = parseFloat(total_price - discount);

		_currentFetchItems[trId].quantity = quantity;
		_currentFetchItems[trId].discount = discount;
		_currentFetchItems[trId].total_price = total_price;

		$(`#${modal} tr#${trId} td.total_price input`).val(total_price);
		$(`#${modal} tr#${trId} td.total_price span`).text(
			numberFormat(total_price)
		);
		$(`#${modal} tr#${trId} td.item_price span`).text(numberFormat(item_price));

		calculateGrandTotals();
	}
}

/* Calculate grand totals and display */
function calculateGrandTotals() {
	let totalItemPrice = 0,
		totalQuantity = 0,
		totalDiscount = 0,
		totalPriceAmount = 0;

	$.each(_currentFetchItems, function (key, val) {
		totalItemPrice += parseFloat(val.item_price || 0);
		totalQuantity += parseFloat(val.quantity || 0);
		totalDiscount += parseFloat(val.discount || 0);
		totalPriceAmount += parseFloat(val.total_price || 0);
	});

	_grandTotals.totalItemPrice = totalItemPrice;
	_grandTotals.totalQuantity = totalQuantity;
	_grandTotals.totalDiscount = totalDiscount;
	_grandTotals.totalPriceAmount = totalPriceAmount;
	_grandTotals.totalAmount = parseFloat(totalPriceAmount + totalDiscount); // Without discount

	$(`#${modal} td.total_item_price`).text(numberFormat(totalItemPrice));
	$(`#${modal} td.total_quantity`).text(numberFormat(totalQuantity));
	$(`#${modal} td.total_discount`).text(numberFormat(totalDiscount));
	$(`#${modal} td.grand_total`).text(numberFormat(totalPriceAmount));

	$("#total_amount").val(totalPriceAmount || "");
	$("#total_discount").val(totalDiscount || "");
	$("#grand_total").val(_grandTotals.totalAmount || "");
	$("#total_amount_no_discount").val(_grandTotals.totalAmount || "");

	_computeVat();
}

/* Initialize select2 customer branches */
function _initSelect2CustomerBranches(customer_id, branch_id) {
	const options = {
		options: {
			not_select2_ajax: true,
			customer_id: customer_id,
		},
	};

	/* Get customer branches via ajax post */
	$.post(router.admin.common.customer_branches, options)
		.then((res) => {
			select2Reinit("#customer_branch_id", "Please select a branch", res.data);

			if (branch_id) {
				setSelect2Selection("#customer_branch_id", branch_id);
			}
		})
		.catch((err) => catchErrMsg(err));
}

/* Masterlist select2 via ajax data source */
function _initInventorySelect2() {
	select2AjaxInit(
		invSelector,
		"Search & select an item",
		router.inventory.common.masterlist,
		"text",
		_loadItemDetails
	);

	// If select2 clear, set the item_available input next to it to empty
	$(invSelector).on("select2:clear", function (e) {
		const trElem = e.target.parentElement.parentElement;
		const trId = $(trElem).attr("id");

		$(`#${modal} tr#${trId} td.item_stocks`).text("");
		$(`#${modal} tr#${trId} td.item_price span`).text("");
		$(`#${modal} tr#${trId} td.quantity input.quantity`).val("");
		$(`#${modal} tr#${trId} td.discount input.discount`).val("");
		$(`#${modal} tr#${trId} td.total_price span`).text("");

		if (_currentFetchItems[trId]) {
			_currentFetchItems[trId].item_stocks = 0;
			_currentFetchItems[trId].item_price = 0;
			_currentFetchItems[trId].quantity = 0;
			_currentFetchItems[trId].discount = 0;
			_currentFetchItems[trId].total_price = 0;
		}

		calculateGrandTotals();
	});
}

/* Load selected item details */
function _loadItemDetails(data) {
	let total_price = 0;
	let quantity = 0;
	let discount = 0;

	if (data.item_price) {
		let item_stocks = data.stocks;
		let item_price = parseFloat(data.item_price);

		const trParentElem = data.element.parentElement.parentElement.parentElement;
		const trId = $(trParentElem).attr("id");
		const quantityElem = $(`tr#${trId} td.quantity input.quantity`);
		const discountElem = $(`tr#${trId} td.discount input.discount`);

		quantity = parseFloat(quantityElem.val() || 0);
		discount = parseFloat(discountElem.val() || 0);
		total_price = item_price * quantity - discount;

		quantityElem.attr("max", item_stocks);
		quantityElem.attr("data-tr_id", trId);
		discountElem.attr("data-tr_id", trId);

		$(`#${modal} tr#${trId} td.item_stocks`).text(item_stocks);
		$(`#${modal} tr#${trId} td.item_price span`).text(numberFormat(item_price));
		$(`#${modal} tr#${trId} td.total_price span`).text(
			numberFormat(total_price)
		);
		$(`#${modal} tr#${trId} td.total_price input`).val(total_price);

		_currentFetchItems[trId] = data;
		_currentFetchItems[trId].item_price = item_price;
		_currentFetchItems[trId].total_price = total_price;
	}
}

/* Compute vat */
function _computeVat(elem) {
	let vat_amount = 0;
	let total_amount =
		_grandTotals.totalAmount ||
		parseFloat($("#total_amount_no_discount").val() || 0);

	$("div.row.with_vat").addClass("d-none");

	if ($(elem || "#with_vat").is(":checked")) {
		vat_amount = parseFloat(total_amount * _vat_percent);

		$("div.row.with_vat").removeClass("d-none");
	}

	const grand_total = parseFloat(total_amount + vat_amount).toFixed(2);

	$("#grand_total").val(grand_total);
	$("#vat_amount").val(parseFloat(vat_amount).toFixed(2));
}

/* Reset form */
function _formReset() {
	$("#id").val("");
	$("#total_amount").val("");
	$("#total_discount").val("");
	$("#total_amount_no_discount").val("");
	$(".item-row").remove();
	$(".original-item").html("");
	$("#client_branch_wrapper").addClass("d-none");

	$(`#${modal} td.item_stocks`).text("");
	$(`#${modal} td.item_price span`).text("");
	$(`#${modal} td.quantity input.quantity`).val("");
	$(`#${modal} td.discount input.discount`).val("");
	$(`#${modal} td.total_price span`).text("");
	$(`#${modal} td.total_item_price`).text("");
	$(`#${modal} td.total_quantity`).text("");
	$(`#${modal} td.total_discount`).text("");
	$(`#${modal} td.grand_total`).text("");

	clearSelect2Selection(clientSelector);
	clearSelect2Selection(invSelector);
	clearSelect2Selection("#customer_branch_id");
	clearAlertInForm(elems);
}
