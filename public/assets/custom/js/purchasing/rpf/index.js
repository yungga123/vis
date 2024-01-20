var table, modal, form, elems, invSelector, supSelector, itemFieldTable;

$(document).ready(function () {
	table = "rpf_table";
	modal = "rpf_modal";
	form = "rpf_form";
	elems = [
		"inventory_id",
		"supplier_id",
		"quantity_in",
		"received_q",
		"received_date",
		"date_needed",
	];
	invSelector = ".inventory_id";
	supSelector = ".supplier_id";
	itemFieldTable = $("#item_field_table tbody");

	/* Load dataTable */
	loadDataTable(table, router.rpf.list, METHOD.POST);

	/* Init filter */
	select2Init("#filter_status");

	/* Toggle modal */
	$("#btn_add_record").on("click", function () {
		$(`#${modal}`).modal("show");
		$(`#${modal}`).removeClass("edit").addClass("add");
		$(`#${modal} .modal-title`).text("Add RPF");
		$(`#${form}`)[0].reset();
		$("#rpf_id").val("");
		$(".item-row").remove();
		clearSelect2Selection(invSelector);
		clearAlertInForm(elems);
	});

	/* Masterlist select2 via ajax data source */
	_initInventorySelect2();

	/* Suppliers select2 via ajax data source */
	select2AjaxInit(
		".supplier_id",
		"Search & select a supplier",
		router.purchasing.common.suppliers,
		"text"
	);

	// If select2 clear, set the item_available input next to it to empty
	$(invSelector).on("select2:clear", function (e) {
		const parentSiblingElem = e.target.parentElement.nextElementSibling;
		_populateAvailableItemStocks(parentSiblingElem, "");
	});

	/* Form for saving record */
	formSubmit($("#" + form), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			self[0].reset();
			refreshDataTable($("#" + table));
			notifMsgSwal(res.status, message, res.status);
			$("#rpf_id").val("");
			$(".item-row").remove();
			clearSelect2Selection(invSelector);
			// clearSelect2Selection(supSelector);

			if ($(`#${modal}`).hasClass("edit")) {
				$(`#${modal}`).modal("hide");
			}
		}

		showAlertInForm(elems, message, res.status);
	});

	/* Form for status filed */
	formSubmit($("#rpf_items_form"), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			self[0].reset();
			refreshDataTable($("#" + table));
			notifMsgSwal(res.status, message, res.status);
			$("#rpf_id").val("");
			$("#inventory_id_file").val("");
			$("#status_receive").val("");
			$(`#rpf_items_modal`).modal("hide");
		}

		showAlertInForm(["remarks"], message, res.status);
	});

	showItemsIfRedirectedFromMail();
});

/* For filtering and reseting */
function filterData(reset = false) {
	let status = getSelect2Selection("#filter_status");

	filterParam(
		router.rpf.list,
		table,
		{ status: status },
		!isEmpty(status),
		() => clearSelect2Selection("#filter_status"),
		reset
	);
}

/* Get rpf items */
function view(id, status) {
	$(`#rpf_items_modal .modal-title`).html("RPF Item Details");
	$("#received_remarks").addClass("d-none");
	if (!status) {
		$("#item_note").html("");
		$("#rpf_items_modal .modal-footer #btn_review").remove();
		$("#rpf_items_modal .modal-footer #btn_receive").remove();
	}
	if (status === "receive") {
		$(`#rpf_items_modal .modal-title`).html(
			`Change Status from REVIEWED to RECEIVE`
		);

		// Remove and append button
		$("#rpf_items_modal .modal-footer #btn_review").remove();
		$("#rpf_items_modal .modal-footer #btn_receive").remove();
		$("#rpf_items_modal .modal-footer").append(`
			<button type="submit" class="btn btn-success" id="btn_receive">Mark as Received</button>	
		`);
	}

	$("#rpf_items_modal #rpf_id_text").text("RPF #: " + id);
	showLoading();

	const data = { id: id, rpf_items: true };
	$.post(router.rpf.fetch, data)
		.then((res) => {
			closeLoading();

			if (res.status === STATUS.SUCCESS) {
				let html = "";

				if (!isEmpty(res.data)) {
					const received_date = `
						<input type="date" name="received_date[]" id="received_date" class="form-control" placeholder="Quantity" value="${currentDate()}" max="${currentDate()}">
					`;
					let totalAmount = 0,
						totalAmountReceived = 0;

					$.each(res.data, (index, val) => {
						const inventory_id = `
							<input type="hidden" name="inventory_id[]" value="${val.inventory_id}" class="form-control" readonly>
						`;
						const stocks = `
							<input type="hidden" name="stocks[]" value="${val.stocks}" class="form-control" readonly>
						`;
						const quantity_in = `
							<input type="hidden" name="quantity_in[]" value="${val.quantity_in}" class="form-control" readonly>
						`;
						const onkeyEvent =
							'onkeyup="validate(' + parseFloat(val.quantity_in) + ', event)"';
						const received_q = `
							<input type="number" name="received_q[]" id="received_q_${index}" class="form-control" placeholder="Qty" ${onkeyEvent} value="${val.quantity_in}" max="${val.quantity_in}" data-item_cost="${val.item_sdp}">
						`;
						const totalCost = Math.floor(val.quantity_in * val.item_sdp);
						const totalCostReceived = Math.floor(val.received_q * val.item_sdp);

						totalAmount = Math.floor(totalAmount + totalCost);
						totalAmountReceived = Math.floor(
							totalAmountReceived + totalCostReceived
						);
						html += `
							<tr>
								<td>
									${val.inventory_id}
									${inventory_id}
								</td>
								<td>${val.category_name}</td>
								<td>${val.item_model}</td>
								<td>${val.item_description}</td>
								<td>${val.supplier_name || "N/A"}</td>
								<td>${val.unit || "N/A"}</td>
								<td>${val.size || "N/A"}</td>
								<td>
									${val.stocks}
									${stocks}
								</td>
								<td>
									${val.quantity_in}
									${quantity_in}
								</td>
								<td>${numberFormat(val.item_sdp)}</td>
								<td>${numberFormat(totalCost)}</td>
								<td>${status === "receive" ? received_q : val.received_q || "0.00"}</td>
								<td>${
									status === "receive"
										? received_date
										: val.received_date_formatted || "N/A"
								}</td>
								<td>${val.purpose || "N/A"}</td>
							</tr>
						`;
					});
					$(`#total_amount`).text(numberFormat(totalAmount));
					$(`#total_amount_received`).text(numberFormat(totalAmountReceived));
				} else {
					html =
						'<tr><td colspan="11" align="center">No rpf items found...</td></tr>';
				}

				$(`#rpf_items_table tbody`).html(html);
				$(`#rpf_items_modal`).modal("show");
			} else {
				$(`#rpf_items_modal`).modal("hide");
				notifMsgSwal(res.status, res.message, res.status);
			}
		})
		.catch((err) => catchErrMsg(err));
}

/* Get record details */
function edit(id) {
	$("#rpf_items_modal").modal("hide");
	$(`#${modal}`).removeClass("add").addClass("edit");
	$(`#${modal} .modal-title`).text("Edit RPF");
	$("#rpf_id").val(id);
	$(".quantity_in").val("");
	$(".item-row").remove();

	// clearSelect2Selection(supSelector);
	clearSelect2Selection(invSelector);
	clearAlertInForm(elems);
	showLoading();

	$.post(router.rpf.fetch, { id: id })
		.then((res) => {
			closeLoading();

			if (res.status === STATUS.SUCCESS) {
				if (!isEmpty(res.data.items)) {
					const items = res.data.items;
					// Add item fields
					for (let i = 1; i < items.length; i++) toggleItemField();

					// Populate data
					const itemFields = $(".inventory_id");
					const quantity_in = $(".quantity_in");
					for (let x = 0; x < itemFields.length; x++) {
						const elem = itemFields[x];
						const qelem = quantity_in[x];
						const item = items[x];
						const text = `${item.inventory_id} | ${item.item_model} | ${item.item_description}`;
						// Set selected item in each select2
						setSelect2AjaxSelection(elem, text, item.inventory_id);
						// Set quantity_in in each input
						$(qelem).val(parseInt(item.quantity_in));
						// Get the parent next sibling td (which where the item_available input) each
						const parentSiblingElem = $(elem).parent().next();
						// Set available stocks each
						_populateAvailableItemStocks(parentSiblingElem[0], item.stocks);
					}
				}

				$.each(res.data, (key, value) => $(`input[name="${key}"]`).val(value));
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
			$.post(router.rpf.delete, { id: id })
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
function change(id, changeTo, status, proceed) {
	if (status === "accepted" && !proceed) {
		// For item out, dispaly the rpf_items_modal
		// to review the item first
		view(id, changeTo);
		// Add note
		const note = `
			Please review the items first! If good to go, click the <strong>Mark as Reviewed</strong> button to make this as <strong>REVIEWED</strong> and ready for purchase. Once marked as reviewed, record cannot be edited anymore.
		`;
		$("#item_note").html(note);

		// Remove and append the button
		const changeClick =
			'onclick="change(' +
			id +
			",'" +
			changeTo +
			"','" +
			status +
			"'," +
			true +
			')"';
		$("#rpf_items_modal .modal-footer #btn_review").remove();
		$("#rpf_items_modal .modal-footer").append(`
			<button type="button" class="btn btn-success" id="btn_review" ${changeClick}">Mark as Reviewed</button>	
		`);
		return;
	}

	if (status === "reviewed") {
		$("#rpf_id_received").val(id);
		$("#status_received").val(changeTo);
		$("#item_note").html("");

		// Display the items details
		view(id, changeTo);
		return;
	}

	const title = `${strUpper(status)} to ${strUpper(changeTo)}!`;
	const swalMsg = `
		<div>RPF #: <strong>${id}</strong></div>
		<div>Are you sure you want to <strong>${strUpper(
			changeTo
		)}</strong> this RPF?</div>
	`;
	const data = { id: id, status: changeTo };

	swalNotifConfirm(
		function () {
			$.post(router.rpf.change, data)
				.then((res) => {
					const message = res.errors ?? res.message;

					notifMsgSwal(res.status, message, res.status);
					if (res.status !== STATUS.ERROR) {
						refreshDataTable($("#" + table));
						$("#rpf_items_modal").modal("hide");
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
	const itemFieldCount = itemFieldTable.find("tr").length + 1;

	if (row) {
		itemFieldTable.children("tr#row_" + row).remove();
		return;
	}

	// <td>
	// 	<select class="custom-select supplier_id" name="supplier_id[]" style="width: 100%;"></select>
	// </td>
	const html = `
		<tr class="item-row" id="row_${itemFieldCount}">
			<td>
				<select class="custom-select inventory_id" name="inventory_id[]" style="width: 100%;"></select>
			</td>
			<td>
				<input type="number" name="item_available[]" class="form-control item_available" placeholder="Stock" readonly>
			</td>
			<td>
				<input type="number" name="quantity_in[]" class="form-control quantity_in" placeholder="Qty" min="1" required>
			</td>
			<td>
				<input type="text" name="purpose[]" class="form-control purpose" placeholder="Purpose">
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
function validate(quantity_in, evt) {
	const value = parseFloat(evt.target.value);
	let alertMsg = "";

	if (
		isNumber(value) &&
		Math.floor(parseFloat(quantity_in) < parseFloat(value))
	) {
		alertMsg = "Received qty must not be greater than quantity in!";
	}

	$("#alert_received_q").text(alertMsg);
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
}

/* Load selected item details */
function _loadItemDetails(data) {
	if (data.id) {
		const parentSiblingElem =
			data.element.parentElement.parentElement.nextElementSibling;
		if (data.stocks)
			_populateAvailableItemStocks(parentSiblingElem, data.stocks);
	}
}

/* Populate the item available stocks */
function _populateAvailableItemStocks(parentSiblingElem, stock, noChild) {
	if (parentSiblingElem.tagName === "TD" && typeof stock !== "undefined") {
		if (noChild) {
			$(parentSiblingElem).text(stock);
			return;
		}
		$(parentSiblingElem).children('input[name="item_available[]"]').val(stock);
	}
}
