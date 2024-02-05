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

	// select2Init("#filter_status");
	// select2Init("#filter_leave_type");

	/* Load dataTable */
	// loadDataTable(table, router.billing_invoice.list, METHOD.POST);

	$("#btn_add_record").on("click", function () {
		$(`#${modal}`).modal("show");
		$(`#${modal}`).removeClass("edit").addClass("add");
		$(`#${modal} .modal-title`).text("Create Billing Invoice");
		$(`#${form}`)[0].reset();
		$("#id").val("");

		clearSelect2Selection("#status");
		clearAlertInForm(elems);
	});

	/* Quotation via ajax data source */
	select2AjaxInit(
		"#tasklead_id",
		"Search & select a tasklead",
		router.admin.common.quotations,
		["id", "quotation", "client", "manager", "type"],
		null,
		{
			search_in: ["quotation", "client", "manager"],
		}
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

			clearSelect2Selection("#status");
			// refreshDataTable($("#" + table));
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
	const status = getSelect2Selection("#filter_status");
	const leave_type = getSelect2Selection("#filter_leave_type");
	const params = {
		status: status,
		leave_type: leave_type,
	};
	const condition = !isEmpty(status) || !isEmpty(leave_type);

	filterParam(
		router.billing_invoice.list,
		table,
		params,
		condition,
		() => {
			clearSelect2Selection("#filter_status");
			clearSelect2Selection("#filter_leave_type");
		},
		reset
	);
}

/* Get record details */
function edit(id) {
	$(`#${modal}`).removeClass("add").addClass("edit");
	$(`#${modal} .modal-title`).text("Edit Leave");
	$("#start_date").attr("min", "");
	$("#id").val(id);

	clearAlertInForm(elems);

	fetchRecord(router.billing_invoice.fetch, { id: id }, modal, (res) => {
		if (res.status === STATUS.SUCCESS) {
			if (inObject(res, "data") && !isEmpty(res.data)) {
				setOptionValue("#leave_type", res.data.leave_type);

				$("#start_date").val(res.data.start_date);
				$("#end_date").val(res.data.end_date);
				$("#leave_reason").val(res.data.leave_reason);
				$("#end_date").removeAttr("readonly");
				$(`#${modal}`).modal("show");
			}
		}
	});
}

/* Delete record */
function remove(id) {
	deleteRecord(router.billing_invoice.delete, { id: id }, table);
}
