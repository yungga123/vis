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
	formSubmit($("#rpf_received_form"), "continue", function (res, self) {
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
});

/* Get rpf items */
function view(id, status) {
	$(`#rpf_items_modal .modal-title`).html("RPF Items Detials");
	$("#received_remarks").addClass("d-none");
	if (!status) $("#note_item_out").html("");
	$("#rpf_items_modal .modal-footer #btn_received").remove();
	if (status === "received") {
		$(`#rpf_items_modal .modal-title`).html(
			`Change Status from REVIEWED to RECEIVE`
		);
		$("#received_remarks").removeClass("d-none");

		// Remove and append button
		$("#rpf_items_modal .modal-footer #btn_received").remove();
		$("#rpf_items_modal .modal-footer").append(`
			<button type="submit" class="btn btn-success" id="btn_received">Save Changes</button>	
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
						<input type="date" name="received_date[]" id="received_date_file" class="form-control" placeholder="Quantity" value="${currentDate()}" max="${currentDate()}">
					`;
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
							'onkeyup="compute(' + parseFloat(val.quantity_in) + ', event)"';
						const received_q = `
							<input type="number" name="received_q[]" id="received_q_file" class="form-control" placeholder="Quantity" ${onkeyEvent} max="${val.quantity_in}">
						`;
						html += `
							<tr>
								<td>
									${val.inventory_id}
									${inventory_id}
								</td>
								<td>${val.category_name}</td>
								<td>${val.item_model}</td>
								<td>${val.item_description}</td>
								<td>
									${val.stocks}
									${stocks}
								</td>
								<td>
									${val.quantity_in}
									${quantity_in}
								</td>
								<td>${val.unit || "N/A"}</td>
								<td>${val.item_sdp}</td>
								<td>${parseFloat(val.quantity_in) * parseFloat(val.item_sdp)}</td>
								<td>${status === "receive" ? received_q : val.received_q || "0.00"}</td>
								<td>${
									status === "receive"
										? received_date
										: val.received_date_formatted || "N/A"
								}</td>
							</tr>
						`;
					});
				} else {
					html =
						'<tr><td colspan="9" align="center">No rpf items found...</td></tr>';
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
	$(`#${modal} .modal-title`).text("Edit Item");
	$("#rpf_id").val(id);
	$(".quantity_in").val("");
	$(".item-row").remove();

	clearSelect2Selection(joSelector);
	clearSelect2Selection(invSelector);
	clearAlertInForm(elems);
	showLoading();

	$.post(router.rpf.fetch, { id: id })
		.then((res) => {
			closeLoading();

			if (res.status === STATUS.SUCCESS) {
				// Set selected job order in select2
				setSelect2AjaxSelection(
					joSelector,
					res.data.job_order.option_text,
					res.data.job_order_id
				);

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
						$(qelem).attr("max", parseInt(item.quantity_in));
						// Get the parent next sibling td (which where the item_available input) each
						const parentSiblingElem = $(elem).parent().next();
						// Set available stocks each
						_populateAvailableItemStocks(parentSiblingElem[0], item.stocks);
					}
				}

				$.each(res.data, (key, value) => $(`input[name="${key}"]`).val(value));
				$("#orig_job_order")
					.removeClass()
					.html(
						`Original JO: <strong>${res.data.job_order.option_text}</strong>`
					);

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
		const onclick = 'onclick="edit(' + id + ')"';
		const note = `
			Please review the items details first before you proceed to <strong>ITEM OUT</strong>! 
			Click the <button type="button" class="btn btn-sm btn-warning" ${onclick} title="Edit"><i class="fas fa-edit"></i></button> button/icon to update details.
		`;
		$("#note_item_out").html(note);

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
		$("#rpf_items_modal .modal-footer #btn_item_out").remove();
		$("#rpf_items_modal .modal-footer #btn_file").remove();
		$("#rpf_items_modal .modal-footer").append(`
			<button type="button" class="btn btn-success" id="btn_item_out" ${changeClick}">Item Out</button>	
		`);
		return;
	}

	if (status === "item_out") {
		$("#rpf_id_file").val(id);
		$("#status_receive").val(changeTo);

		// Display the items details
		view(id, changeTo);
		return;
	}

	const title = `${strUpper(status)} to ${strUpper(changeTo)}!`;
	const swalMsg = `
		<div>rpf #: <strong>${id}</strong></div>
		<div>Are you sure you want to <strong>${strUpper(
			changeTo
		)}</strong> this rpf?</div>
	`;
	const data = { id: id, status: changeTo };

	swalNotifConfirm(
		function () {
			$.post(router.rpf.change, data)
				.then((res) => {
					const message = res.errors ?? res.message;

					refreshDataTable($("#" + table));
					notifMsgSwal(res.status, message, res.status);
					$("#rpf_items_modal").modal("hide");
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
				<input type="number" name="item_available[]" class="form-control item_available" placeholder="Stock" readonly>
			</td>
			<td>
				<input type="number" name="quantity_in[]" class="form-control quantity_in" placeholder="Quantity" min="1" required>
			</td>
			<td>
				<select class="custom-select supplier_id" name="supplier_id[]" style="width: 100%;"></select>
			</td>
			<td>
				<input type="text" name="purpose[]" class="form-control quantity_out" placeholder="Purpose">
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
function compute(quantity_in, evt) {
	const returned = parseFloat(evt.target.value);
	if (isNumber(returned)) {
		const consumed = parseFloat(quantity_in) - returned;
		const parentSiblingElem = evt.target.parentElement.nextElementSibling;
		_populateAvailableItemStocks(parentSiblingElem, consumed, true);
	}
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
