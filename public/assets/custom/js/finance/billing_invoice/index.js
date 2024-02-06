var table, modal, form, elems;

$(document).ready(function () {
	table = "billing_invoice_table";
	modal = "billing_invoice_modal";
	form = "billing_invoice_form";
	elems = [
		"tasklead_id",
		"status",
		"due_date",
		"bill_type",
		"billing_amount",
		"payment_method",
		"amount_paid",
	];

	select2Init("#filter_billing_status");
	select2Init("#filter_bill_type");
	select2Init("#filter_payment_method");

	/* Load dataTable */
	loadDataTable(table, router.billing_invoice.list, METHOD.POST);

	$("#btn_add_record").on("click", function () {
		$(`#${modal}`).modal("show");
		$(`#${modal}`).removeClass("edit").addClass("add");
		$(`#${modal} .modal-title`).text("Create Billing Invoice");
		$(`#${form}`)[0].reset();
		$("#id").val("");
		$("#orig_tasklead").html("");
		$(".tasklead-details").html("");

		clearSelect2Selection("#tasklead_id");
		clearAlertInForm(elems);
	});

	/* Quotation via ajax data source */
	select2AjaxInit(
		"#tasklead_id",
		"Search & select a tasklead",
		router.admin.common.quotations,
		["id", "quotation"],
		_loadTaskleadDetails
	);

	$("#status").on("change", function () {
		$(".form-group.amount_paid label").removeClass();

		if ($(this).val() === "paid") {
			$(".form-group.amount_paid label").addClass("required");
		}
	});

	/* Form for saving record */
	formSubmit($("#" + form), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			$("#id").val("");
			self[0].reset();

			clearSelect2Selection("#tasklead_id");
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
		!isEmpty(billing_status) || !isEmpty(bill_type) || !isEmpty(payment_method);

	filterParam(
		router.billing_invoice.list,
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
function edit(id) {
	$(`#${modal}`).removeClass("add").addClass("edit");
	$(`#${modal} .modal-title`).text("Edit Billing Invoice");
	$("#id").val(id);
	$("#orig_tasklead").html("");
	$(".tasklead-details").html("");

	clearAlertInForm(elems);
	clearSelect2Selection("#tasklead_id");

	fetchRecord(router.billing_invoice.fetch, { id: id }, modal, (res) => {
		if (res.status === STATUS.SUCCESS) {
			if (inObject(res, "data") && !isEmpty(res.data)) {
				const text = `${res.data.tasklead_id} | ${res.data.quotation}`;

				setSelect2AjaxSelection("#tasklead_id", text, res.data.tasklead_id);
				setOptionValue("#bill_type", res.data.bill_type);
				setOptionValue("#payment_method", res.data.payment_method);
				setOptionValue("#billing_status", res.data.billing_status);

				$("#orig_tasklead").html(
					`Original Task/Lead: <strong>${text}</strong>`
				);
				$("#due_date").val(res.data.due_date);
				$("#billing_amount").val(res.data.billing_amount);
				$("#amount_paid").val(res.data.amount_paid);
				$(`#${modal}`).modal("show");
			}
		}
	});
}

/* Delete record */
function remove(id) {
	deleteRecord(router.billing_invoice.delete, { id: id }, table);
}

/* Load selected tasklead/quotation details */
function _loadTaskleadDetails(data) {
	let html = "";

	if (data.id) {
		id = data.id;
		quotation = data.quotation;
		employee_id = data.employee_id;
		html = `
			<h5 class="text-center">Task/Lead Details</h5>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Client</th>
						<th>Manger</th>
						<th>Project</th>
						<th>Amount</th>
						<th>Quotation Type</th>
					</tr>
				</thead>
				<tbody>				
					<tr>					
						<td>${data.client}</td>
						<td>${data.manager}</td>
						<td>${data.project}</td>
						<td>${numberFormat(data.project_amount)}</td>
						<td>${data.type || "N/A"}</td>
					</tr>
				</tbody>
			</table>
		`;
	}

	$(".tasklead-details").html(html);
}
