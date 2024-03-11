var table,
	modal,
	form,
	elems,
	joSelector,
	invSelector,
	itemFieldTable,
	_fetchItems,
	_status,
	_remarks;

$(document).ready(function () {
	table = "prf_table";
	modal = "prf_modal";
	form = "prf_form";
	elems = ["job_order_id", "inventory_id", "quantity_out", "process_date"];
	joSelector = "#job_order_id";
	invSelector = ".inventory_id";
	itemFieldTable = $("#item_field_table tbody");
	_fetchItems = [];
	_status = $pjOptions.prf_status;
	_remarks = $pjOptions.prf_remarks;

	/* Load dataTable */
	loadDataTable(table, router.prf.list, METHOD.POST);

	/* Init filter */
	select2Init("#filter_status");

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
		$(".original-item").html("");
		clearSelect2Selection(joSelector);
		clearSelect2Selection(invSelector);
		clearAlertInForm(elems);
	});

	/* Job Order select2 via ajax data source */
	select2AjaxInit(
		joSelector,
		"Search & select a job order",
		router.admin.common.joborders,
		"option_text",
		_loadJobOrderDetails
	);

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

	/* Form for status filed */
	formSubmit($("#prf_file_form"), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			self[0].reset();
			refreshDataTable($("#" + table));
			notifMsgSwal(res.status, message, res.status);
			$("#prf_id").val("");
			$("#inventory_id_file").val("");
			$("#status_file").val("");
			$(`#prf_items_modal`).modal("hide");
		}

		showAlertInForm(["remarks"], message, res.status);
	});

	showItemsIfRedirectedFromMail();
});

/* For filtering and reseting */
function filterData(reset = false) {
	const status = getSelect2Selection("#filter_status");
	const start_date = $("#filter_start_date").val();
	const end_date = $("#filter_end_date").val();
	const params = {
		status: status,
		start_date: start_date,
		end_date: end_date,
	};
	const condition =
		!isEmpty(status) || (!isEmpty(start_date) && !isEmpty(end_date));

	filterParam(
		router.prf.list,
		table,
		params,
		condition,
		() => {
			clearSelect2Selection("#filter_status");
			$("#filter_start_date").val("");
			$("#filter_end_date").val("");
		},
		reset
	);
}

/* Get prf items */
function view(id, changeTo, status) {
	$(`#prf_items_modal .modal-title`).html("PRF Item Details");
	$("#prf_items_modal .modal-footer .change-btn").html("");
	$("#prf_items_modal #prf_id_text").text("PRF #: " + id);
	$("#file_remarks").addClass("d-none");
	$("#note_item_out").html("");

	if (inArray(["accept", "item_out", "receive", "file"], changeTo)) {
		let markAs = strUpper(_status[changeTo]);
		let onclick = "";
		let btnType = "submit";

		if (changeTo === "item_out") {
			const note = `
				Please review the items details first before you proceed to <strong>${markAs}</strong>! 
				Click the <button type="button" class="btn btn-sm btn-warning" onclick="edit(${id})" title="Edit"><i class="fas fa-edit"></i></button> button/icon to update details.
			`;
			$("#note_item_out").html(note);
		}

		if (changeTo === "file") {
			$("#prf_id_file").val(id);
			$("#status_file").val(changeTo);
			$("#file_remarks").removeClass("d-none");
			$("#note_item_out").html(
				"If item quantity were all consumed, just put zero <strong>(0)</strong> in the input field. Returned item quantity will be added back to the Masterlist stocks."
			);
		}

		if (changeTo !== "file") {
			onclick = `onclick="change(${id}, '${changeTo}', '${status}', ${true})"`;
			btnType = "button";
		}

		$(`#prf_items_modal .modal-title`).html(
			`Change Status from ${strUpper(status)} to ${strUpper(changeTo)}`
		);

		// Append button
		$("#prf_items_modal .modal-footer .change-btn").append(`
			<button type="${btnType}" class="btn btn-success" id="btn_${changeTo}" ${onclick}>
				Mark as ${markAs}
			</button>	
		`);
	}

	showLoading();

	const data = { id: id, prf_items: true };

	$.post(router.prf.fetch, data)
		.then((res) => {
			closeLoading();

			if (res.status === STATUS.SUCCESS) {
				let html = "";

				if (!isEmpty(res.data)) {
					const returned_date = `
						<input type="date" name="returned_date[]" id="returned_date_file" class="form-control" placeholder="Quantity" value="${currentDate()}" max="${currentDate()}">
					`;
					$.each(res.data, (index, val) => {
						const inventory_id = `
							<input type="hidden" name="inventory_id[]" value="${val.inventory_id}" class="form-control" readonly>
						`;
						const stocks = `
							<input type="hidden" name="stocks[]" value="${val.stocks}" class="form-control" readonly>
						`;
						const quantity_out = `
							<input type="hidden" name="quantity_out[]" value="${val.quantity_out}" class="form-control" readonly>
						`;
						const remarks = `
							<input type="hidden" name="remarks[]" value="${val.remarks}" class="form-control" readonly>
						`;
						const onkeyEvent =
							'onkeyup="compute(' + parseFloat(val.quantity_out) + ', event)"';
						const returned_q = `
							<input type="number" name="returned_q[]" id="returned_q_file" class="form-control" placeholder="Quantity" ${onkeyEvent} max="${
							val.quantity_out
						}" value="${parseFloat(val.returned_q || 0)}">
						`;
						html += `
							<tr>
								<td>
									${val.inventory_id}
									${inventory_id}
								</td>
								<td>${val.supplier_name}</td>
								<td>${val.category_name}</td>
								<td>${val.item_model}</td>
								<td>${val.item_description}</td>
								<td>${val.unit || "N/A"}</td>
								<td>${val.size || "N/A"}</td>
								<td>
									${val.stocks}
									${stocks}
								</td>
								<td>
									${val.quantity_out}
									${quantity_out}
								</td>
								<td>${changeTo === "file" ? returned_q : val.returned_q || "0.00"}</td>
								<td>${val.consumed}</td>
								<td>${
									changeTo === "file"
										? returned_date
										: val.returned_date_formatted || "N/A"
								}</td>
								<td>
									${val.remarks || "N/A"}
									${remarks}
								</td>
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
	_fetchItems = [];

	$("#prf_items_modal").modal("hide");
	$(`#${modal}`).removeClass("add").addClass("edit");
	$(`#${modal} .modal-title`).text("Edit Item");
	$("#prf_id").val(id);
	$(".quantity_out").val("");
	$(".item-row").remove();
	$(".original-item").html("");

	clearSelect2Selection(joSelector);
	clearSelect2Selection(invSelector);
	clearAlertInForm(elems);
	showLoading();

	$.post(router.prf.fetch, { id: id })
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
					const quantity_out = $(".quantity_out");
					const remarks = $(".remarks");

					for (let x = 0; x < itemFields.length; x++) {
						const elem = itemFields[x];
						const qelem = quantity_out[x];
						const remarksElem = remarks[x];
						const item = items[x];
						const text = `${item.inventory_id} | ${item.item_model} | ${
							item.item_description
						} | ${item.size || "N/A"}`;

						// Store items in a variable with inventory_id as key
						_fetchItems[item.inventory_id] = item;
						// Set selected item in each select2
						setSelect2AjaxSelection(elem, text, item.inventory_id);
						// Display the selected item in a div under each select2
						$(elem)
							.parent()
							.children(".original-item")
							.html(`Original Item: <strong>${text}</strong>`);
						// Set quantity_out in each input
						$(qelem).val(parseInt(item.quantity_out));
						// $(qelem).attr("max", parseInt(item.quantity_out));
						// Get the parent next sibling td (which where the item_available input) each
						const parentSiblingElem = $(elem).parent().next();
						// Set available stocks each item
						_populateAvailableItemStocks(parentSiblingElem[0], item.stocks);
						// Display the item unit in each item
						$(elem).parent().children(".item-unit").text(item.unit);
						// Display the remarks in each item
						$(remarksElem).val(item.remarks).change();
					}
				}

				$("#process_date").val(res.data.process_date);
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
function change(id, changeTo, status, proceed) {
	const allowedInView = ["accept", "item_out", "receive", "file"];

	if (inArray(allowedInView, changeTo) && !proceed) {
		$("#prf_id_file").val(id);
		$("#status_file").val(changeTo);

		// Display the items details
		view(id, changeTo, status);
		return;
	}

	const title = `${strUpper(status)} to ${strUpper(changeTo)}!`;
	const swalMsg = `
		<div>PRF #: <strong>${id}</strong></div>
		<div>Are you sure you want to <strong>${strUpper(
			changeTo
		)}</strong> this PRF?</div>
	`;
	const data = { id: id, status: changeTo };

	swalNotifConfirm(
		function () {
			$.post(router.prf.change, data)
				.then((res) => {
					const message = res.errors ?? res.message;

					notifMsgSwal(res.status, message, res.status);

					if (res.status !== STATUS.ERROR) {
						refreshDataTable($("#" + table));
						$("#prf_items_modal").modal("hide");
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

	const html = `
		<tr class="item-row" id="row_${itemFieldCount}">
			<td>
				<select class="custom-select inventory_id" name="inventory_id[]" style="width: 100%;"></select>
				<div class="original-item"></div>
			</td>
			<td class="text-center items-center">
				<input type="hidden" name="item_available[]" class="form-control item_available" placeholder="Stock" readonly>
				<div class="item-unit text-bold"></div>
			</td>
			<td>
				<input type="number" name="quantity_out[]" class="form-control quantity_out" placeholder="Quantity" min="1" required>
			</td>
			<td>${_prfItemRemarks()}</td>
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
function compute(quantity_out, evt) {
	const returned = parseFloat(evt.target.value);
	if (isNumber(returned)) {
		const consumed = parseFloat(quantity_out) - returned;
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
						<td class="text-left" width="50%">${data.tasklead_id || "N/A"}</td>
                    </tr>
                    <tr>
                        <th class="text-right" width="50%">Client</th>
						<td class="text-left" width="50%">${data.client}</td>
                    </tr>
                    <tr>
                        <th class="text-right" width="50%">Manager</th>
						<td class="text-left" width="50%">${data.manager || "N/A"}</td>
                    </tr>
                    <tr>
                        <th class="text-right" width="50%">Work Type</th>
						<td class="text-left" width="50%">${data.work_type}</td>
                    </tr>
				</tbody>
			</table>
		`;
	}

	$(".job-order-details").html(html);
}

/* Load selected item details */
function _loadItemDetails(data) {
	if (data.id) {
		const parentSiblingElem =
			data.element.parentElement.parentElement.nextElementSibling;
		let stocks = data.stocks;
		let unit = data.unit;

		if (_fetchItems[data.id]) {
			stocks = _fetchItems[data.id].stocks;
			unit = _fetchItems[data.id].unit;
		}

		if (!isEmpty(stocks))
			_populateAvailableItemStocks(parentSiblingElem, stocks, false, unit);
	}
}

/* Populate the item available stocks */
function _populateAvailableItemStocks(
	parentSiblingElem,
	stock,
	noChild,
	item_unit
) {
	if (parentSiblingElem.tagName === "TD" && typeof stock !== "undefined") {
		if (noChild) {
			$(parentSiblingElem).text(stock);
			return;
		}
		$(parentSiblingElem).children('input[name="item_available[]"]').val(stock);

		$(parentSiblingElem)
			.children(".item-unit")
			.text(item_unit || "N/A");
	}
}

/* PRF item remarks select */
function _prfItemRemarks(val) {
	let html = "";

	$.each(_remarks, (key, value) => {
		html += `
			<option value="${key}" ${val === key ? "selected" : ""}>${value}</option>
		`;
	});

	html = `
		<select type="text" class="form-control remarks" name="remarks[]">
			<option value="">Select a remarks</option>
			${html}
		</select>
	`;

	return html;
}
