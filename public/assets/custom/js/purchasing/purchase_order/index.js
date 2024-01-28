var table,
	modal,
	form,
	elems,
	itemFieldTable,
	rpfSelector,
	poItemModal,
	_status;

$(document).ready(function () {
	table = "purchase_order_table";
	modal = "purchase_order_modal";
	form = "purchase_order_form";
	elems = ["rpf_id"];
	itemFieldTable = $("#rpf_field_table tbody");
	rpfSelector = "#rpf_id";
	poItemModal = "po_items_modal";
	_status = $pjOptions.po_status;

	/* Load dataTable */
	loadDataTable(table, router.purchase_order.list, METHOD.POST);

	/* Init filter */
	select2Init("#filter_status");

	/* Toggle modal */
	$("#btn_add_record").on("click", function () {
		$(`#${modal}`).removeClass("edit").addClass("add");
		$(`#${modal} .modal-title`).text("Add Purchase Order");
		toggleModal();
		clearForm();
		toggleAttentionTo(false);
	});

	/* Suppliers select2 via ajax data source */
	select2AjaxInit(
		rpfSelector,
		"Search & select an RPF",
		router.purchasing.common.rpf,
		"text"
	);

	$(rpfSelector).on("select2:select", function (e) {
		if (e.params) {
			showLoading();
			$.post(router.rpf.fetch, { id: $(this).val() })
				.then((res) => {
					closeLoading();
					const message = res.errors ?? res.message;

					if (res.status === STATUS.SUCCESS && !isEmpty(res.data)) {
						toggleRpfDetails(
							res.data.date_needed_formatted,
							res.data.created_at_formatted
						);
						populateRpfItems(res.data.items);
					} else {
						notifMsgSwal(res.status, message, res.status);
					}
				})
				.catch((err) => catchErrMsg(err));
		}
	});

	$(rpfSelector).on("select2:clear", () => clearForm());

	/* Form for saving record */
	formSubmit($("#" + form), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			self[0].reset();
			refreshDataTable($("#" + table));
			notifMsgSwal(res.status, message, res.status);
			clearForm();

			if ($(`#${modal}`).hasClass("edit")) toggleModal(false);
		}

		showAlertInForm(elems, message, res.status);
	});

	/* Form for status received */
	formSubmit($("#po_items_form"), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			self[0].reset();
			refreshDataTable($("#" + table));

			$("#po_id_received").val("");
			$("#rpf_id_received").val("");
			$("#status_received").val("");
			$(`#po_items_modal`).modal("hide");
		}

		notifMsgSwal(res.status, message, res.status);
	});
});

/* For filtering and reseting */
function filterData(reset = false) {
	let status = getSelect2Selection("#filter_status");

	filterParam(
		router.purchase_order.list,
		table,
		{ status: status },
		!isEmpty(status),
		() => clearSelect2Selection("#filter_status"),
		reset
	);
}

/* Get PO items */
function view(id, changeTo, status) {
	$(`#${poItemModal} .modal-title`).html("PRF Item Details");
	$(`#${poItemModal} .modal-footer #btn_mark`).remove();
	$(`#${poItemModal} #po_id_text`).text(`PO #: ${id}`);
	$("#po_id_received").val("");
	$("#rpf_id_received").val("");
	$("#status_received").val("");

	if (inArray(["approve", "receive"], changeTo)) {
		let markAs = strUpper(_status[changeTo]);
		let onclick = "";
		let btnType = "submit";

		if (changeTo === "approve") {
			onclick = `onclick="change(${id}, '${changeTo}', '${status}')"`;
		}

		// Append button
		$(`#${poItemModal} .modal-footer`).append(`
			<button type="${btnType}" class="btn btn-success" id="btn_mark" ${onclick}>
				Mark as ${markAs}
			</button>
		`);
		$(`#${poItemModal} .modal-title`).html(
			`Change Status from ${strUpper(status)} to ${strUpper(changeTo)}`
		);
		$("#po_id_received").val(id);
		$("#status_received").val(changeTo);
	}

	showLoading();

	const data = { id: id, po_items: true };

	$.post(router.purchase_order.fetch, data)
		.then((res) => {
			closeLoading();

			if (res.status === STATUS.SUCCESS) {
				populateRpfItems(res.data.items, "po_items_table", changeTo);

				$("#rpf_id_received").val(res.data.rpf_id);
				$(`#${poItemModal}`).modal("show");
			} else {
				$(`#${poItemModal}`).modal("hide");

				notifMsgSwal(res.status, res.message, res.status);
			}
		})
		.catch((err) => catchErrMsg(err));
}

/* Change status record */
function change(id, changeTo, status) {
	if (changeTo === "receive") {
		view(id, changeTo, status);
		return;
	}

	const title = `${strUpper(status)} to ${strUpper(changeTo)}!`;
	const swalMsg = `
		<div>PO #: <strong>${id}</strong></div>
		<div>Are you sure you want to <strong>${strUpper(
			changeTo
		)}</strong> this PO?</div>
	`;
	const data = { id: id, status: changeTo };

	swalNotifConfirm(
		function () {
			$.post(router.purchase_order.change, data)
				.then((res) => {
					const message = res.errors ?? res.message;

					if (res.status !== STATUS.ERROR) {
						refreshDataTable($("#" + table));
						$(`#${poItemModal}`).modal("hide");
					}

					notifMsgSwal(res.status, message, res.status);
				})
				.catch((err) => catchErrMsg(err));
		},
		title,
		swalMsg,
		STATUS.WARNING
	);
}

/* Get record details */
function edit(id) {
	$(`#${modal}`).removeClass("add").addClass("edit");
	$(`#${modal} .modal-title`).text("Edit Purchase Order");
	$("#po_id").val(id);

	clearForm();
	showLoading();

	$.post(router.purchase_order.fetch, { id: id })
		.then((res) => {
			closeLoading();

			if (res.status === STATUS.SUCCESS) {
				// Set rpf select2 value
				const text = `${res.data.rpf_id} | ${res.data.rpf.requested_by}`;
				setSelect2AjaxSelection(rpfSelector, text, res.data.rpf_id);

				// Toggle and populate rpf details
				toggleRpfDetails(res.data.rpf.date_needed, res.data.rpf.requested_at);
				populateRpfItems(res.data.items);

				// Show attention_to field
				$("#attention_to").val(res.data.attention_to);
				toggleAttentionTo();

				toggleModal();
				setTimeout(() => {
					if (!$("body").hasClass("modal-open"))
						$("body").addClass("modal-open");
				}, 100);
			} else {
				toggleModal(false);
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
			$.post(router.purchase_order.delete, { id: id })
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

/* Clear form */
function clearForm() {
	clearSelect2Selection(rpfSelector);
	clearAlertInForm(elems);
	toggleRpfDetails("", "");
}

/* Toggle main modal */
function toggleModal(show = true) {
	const toggle = show ? "show" : "hide";
	$(`#${modal}`).modal(toggle);
}

/* Toggle RPF details */
function toggleRpfDetails(dateNeed, requestedAt) {
	$("#date_needed_text").text(dateNeed);
	$("#requested_at_text").text(requestedAt);

	if (isEmpty(dateNeed))
		$("#rpf_item_details").addClass("d-none").removeClass("mb-3");
	else $("#rpf_item_details").removeClass("d-none").addClass("mb-3");
}

/* Populate RPF items */
function populateRpfItems(items, itemId, changeTo) {
	let html = "";

	if (!isEmpty(items)) {
		const received_date = `
			<input type="date" name="received_date[]" class="form-control" placeholder="Quantity" value="${currentDate()}" max="${currentDate()}">
		`;
		let totalCostAvg = 0,
			totalDiscountAvg = 0,
			totalAmount = 0,
			totalAmountReceived = 0;

		$.each(items, (index, val) => {
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
				'onkeyup="validate(event, ' + parseFloat(val.quantity_in) + ')"';
			const received_q = `
				<input type="number" name="received_q[]" id="received_q_${index}" class="form-control" placeholder="Qty" ${onkeyEvent} value="${val.quantity_in}" max="${val.quantity_in}" data-item_cost="${val.item_sdp}">
			`;
			const rfpId = val.rpf_id ? `<td>${val.rpf_id}</td>` : "";
			const receivedQtyDate = val.rpf_id
				? `
					<td>${changeTo === "receive" ? received_q : val.received_q || "0.00"}</td>
					<td>${
						changeTo === "receive"
							? received_date
							: val.received_date_formatted || "N/A"
					}</td>
				`
				: "";

			let totalCost = Math.floor(val.quantity_in * val.item_sdp);
			let totalCostReceived = Math.floor(val.received_q * val.item_sdp);

			totalCostAvg += parseFloat(val.item_sdp || 0);
			totalDiscountAvg += parseFloat(val.discount || 0);
			totalAmount = parseFloat(totalAmount + totalCost);
			totalAmountReceived = parseFloat(totalAmountReceived + totalCostReceived);

			let discount = `
				<input type="number" name="discount[]" id="discount_${index}" class="form-control" 
				placeholder="0.00" value="${val.discount || ""}" max="${val.item_sdp}" 
				onkeyup="validate(event, '${val.quantity_in}', '${val.item_sdp}', true)">
			`;
			discount = val.rpf_id ? val.discount || "N/A" : discount;
			totalCost = totalCost - (val.discount || 0);

			html += `
				<tr>
					${rfpId}
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
					<td>
						${discount}
						<small class="text-danger"></small>
					</td>
					<td class="total_cost" data-value="${totalCost}">${numberFormat(totalCost)}</td>
					${receivedQtyDate}
					<td>${val.purpose || "N/A"}</td>
				</tr>
			`;
		});

		$(`.total_cost`)
			.text(numberFormat(totalCostAvg))
			.attr("data-value", totalCostAvg);
		$(`.total_discount`)
			.text(numberFormat(totalDiscountAvg))
			.attr("data-value", totalDiscountAvg);
		$(`.total_amount`)
			.text(numberFormat(totalAmount - totalDiscountAvg))
			.attr("data-value", totalAmount);
		$(`#total_amount_received`).text(
			totalAmountReceived
				? numberFormat(totalAmountReceived - totalDiscountAvg || 0)
				: ""
		);
	} else {
		html = '<tr><td colspan="15" class="center">No PO items found...</td></tr>';
	}

	itemId = itemId || "rpf_item_details";
	$(`#${itemId} tbody`).html(html);
}

/* Toggle attention_to field */
function toggleAttentionTo(show = true) {
	if (show) $("#attention_to_wrapper").removeClass("d-none");
	else $("#attention_to_wrapper").addClass("d-none");
}

/* Validate qty in */
function validate(evt, quantity_in, itemCost, isDiscount = false) {
	const value = parseFloat(evt.target.value || 0);
	let alertMsg = "";

	if (isDiscount) {
		quantity_in = parseFloat(quantity_in || 0);
		itemCost = parseFloat(itemCost || 0);

		const totalCost = parseFloat(quantity_in * itemCost);
		const totalCostTdElem = evt.target.parentElement.nextElementSibling;

		if (value > totalCost) {
			alertMsg = "Value must not be greater than the total cost.";
		}

		$(evt.target.nextElementSibling).text(alertMsg);
		$(totalCostTdElem).text(numberFormat(totalCost - value || 0));
		$(totalCostTdElem).attr("data-value", totalCost - value || 0);

		const totalDiscountElem = $("#rpf_item_details table td.total_discount");
		const totalAmountElem = $("#rpf_item_details table td.total_amount");
		const inputDiscountElems = $(
			"#rpf_item_details table input[name='discount[]']"
		);
		const totalAmountCurr = totalAmountElem.data("value");

		let totalDiscount = 0;

		for (let index = 0; index < inputDiscountElems.length; index++) {
			const elem = $(inputDiscountElems[index]);
			const discount = parseFloat(elem.val() || 0);

			totalDiscount += discount;
		}

		totalAmountElem.text(numberFormat(totalAmountCurr - totalDiscount));
		totalDiscountElem.text(numberFormat(totalDiscount));

		return;
	}

	if (isNumber(value) && parseFloat(quantity_in) < parseFloat(value)) {
		alertMsg = "Received qty must not be greater than quantity in!";
	}

	$("#alert_received_q").text(alertMsg);
}
