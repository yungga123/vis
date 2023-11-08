var table, modal, form, elems, itemFieldTable, rpfSelector, poItemModal;

$(document).ready(function () {
	table = "purchase_order_table";
	modal = "purchase_order_modal";
	form = "purchase_order_form";
	elems = ["rpf_id"];
	itemFieldTable = $("#rpf_field_table tbody");
	rpfSelector = "#rpf_id";
	poItemModal = "po_items_modal";

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
function view(id, status) {
	$(`#${poItemModal} .modal-footer #btn_mark`).remove();
	if (inArray(["pending", "received"], status)) {
		// Append button
		const markAs = status === "pending" ? "approved" : "filed";
		$(`#${poItemModal} .modal-footer`).append(`
			<button type="submit" class="btn btn-success" id="btn_mark" onclick="change(${id}, '${markAs}', '${status}')">
				Mark as ${strCapitalize(markAs)}
			</button>	
		`);
	}

	$(`#${poItemModal} #po_id_text`).text(`PO #: ${id}`);

	showLoading();

	const data = { id: id, po_items: true };
	$.post(router.purchase_order.fetch, data)
		.then((res) => {
			closeLoading();

			if (res.status === STATUS.SUCCESS) {
				populateRpfItems(res.data, "po_items_table");
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

					notifMsgSwal(res.status, message, res.status);
					if (res.status !== STATUS.ERROR) {
						refreshDataTable($("#" + table));
						$(`#${poItemModal}`).modal("hide");
					}
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
function populateRpfItems(items, itemId) {
	let html = "";
	let totalAmount = 0;

	if (!isEmpty(items)) {
		$.each(items, (key, val) => {
			const totalCost = Math.floor(val.quantity_in * val.item_sdp);
			totalAmount = Math.floor(totalAmount + totalCost);
			html += "<tr>";

			if (val.rpf_id && itemId) html += `<td>${val.rpf_id}</td>`;

			html += `
					<td>${val.inventory_id}</td>
					<td>${val.brand}</td>
					<td>${val.item_model}</td>
					<td>${val.item_description}</td>
					<td>${val.supplier_name || "N/A"}</td>
					<td>${val.stocks}</td>
					<td>${val.quantity_in}</td>
					<td>${val.unit || "N/A"}</td>
					<td>${numberFormat(val.item_sdp)}</td>
					<td>${numberFormat(totalCost)}</td>
				</tr>
			`;
		});
	} else {
		html = '<tr><td colspan="11" class="center">No PO items found...</td></tr>';
	}

	itemId = itemId || "rpf_item_details";
	$(`#${itemId} tbody`).html(html);
	$(`#${itemId} .total_amount`).text(numberFormat(totalAmount) || "");
}

/* Toggle attention_to field */
function toggleAttentionTo(show = true) {
	if (show) $("#attention_to_wrapper").removeClass("d-none");
	else $("#attention_to_wrapper").addClass("d-none");
}
