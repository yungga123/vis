var table, modal, form, elems, _interest, _vat_percent;

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
	_interest = $pjOptions.overdue_interests;
	_vat_percent = $pjOptions.vat_percent;

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
		$("#billing_status").val("");
		$("#days_overdue").val("");
		$("#overdue_interest").val("");
		$("#orig_tasklead").html("");
		$(".tasklead-details").html("");
		$(".form-group.amount_paid").addClass("d-none");
		$("div.with_vat").addClass("d-none");
		$("div.with_interest").addClass("d-none");
		$(".with_interest-checkbox").addClass("d-none");

		clearSelect2Selection("#tasklead_id");
		clearAlertInForm(elems);
	});

	/* Quotation via ajax data source */
	_initTasklead();

	$("#billing_amount").on("keyup", function () {
		$("#with_vat").trigger("change");

		let max = parseFloat($(this).attr("max") || 0);

		clearAlertInForm(["billing_amount"]);

		if (max < parseFloat($(this).val())) {
			const error = {
				billing_amount: `Value must not be greater than ${numberFormat(max)}!`,
			};

			showAlertInForm(["billing_amount"], error, STATUS.ERROR);
		}
	});

	$("#with_vat").on("change", function () {
		let vat_amount = 0;
		let billing_amount = parseFloat($("#billing_amount").val() || 0);
		let overdue_interest = parseFloat($("#overdue_interest").val() || 0);

		$("div.with_vat").addClass("d-none");

		if ($(this).is(":checked")) {
			vat_amount = parseFloat(billing_amount * _vat_percent);

			if (vat_amount) $("div.with_vat").removeClass("d-none");
		}

		const total = parseFloat(
			billing_amount + vat_amount + overdue_interest
		).toFixed(2);

		$("#grand_total").val(total);
		$("#amount_paid").val(total);
		$("#vat_amount").val(parseFloat(vat_amount).toFixed(2));
	});

	$("#with_interest").on("change", function () {
		let interest = 0;
		let billing_amount = parseFloat($("#billing_amount").val() || 0);
		let vat_amount = parseFloat($("#vat_amount").val() || 0);

		$("div.with_interest").addClass("d-none");

		if ($(this).is(":checked")) {
			const daysInterest = parseFloat($("#days_overdue").val() || 0);

			interest = billing_amount * (daysInterest * _interest.per_day);
			interest = parseFloat(interest.toFixed(2));

			if (interest) $("div.with_interest").removeClass("d-none");
		}

		const total = parseFloat(billing_amount + interest + vat_amount).toFixed(2);

		$("#grand_total").val(total);
		$("#amount_paid").val(total);
		$("#overdue_interest").val(interest);
	});

	/* Form for saving record */
	formSubmit($("#" + form), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			$("#id").val("");
			$("#billing_status").val("");
			$("#days_overdue").val("");
			$("#orig_tasklead").html("");
			$(".tasklead-details").html("");
			$("div.with_vat").addClass("d-none");
			$("div.with_interest").addClass("d-none");
			$(".with_interest-checkbox").addClass("d-none");
			$(".with_interest-checkbox label > span").html("");

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
function edit(id, billing_status) {
	let title = "Edit Billing Invoice";

	$(`#${modal}`).removeClass("add").addClass("edit");
	$(`#${modal} .modal-title`).text("Edit Billing Invoice");
	$("#id").val(id);
	$("#billing_status").val("");
	$("#days_overdue").val("");
	$("#overdue_interest").val("");
	$("#orig_tasklead").html("");
	$(".tasklead-details").html("");
	$(".form-group.amount_paid").removeClass("d-none");
	$(".form-group.amount_paid label:first-child").removeClass("required");
	$(".with_interest-checkbox").addClass("d-none");

	if (billing_status && billing_status != "paid") {
		title = "Mark Billing Invoice as PAID";

		$("#billing_status").val("paid");
		$(".form-group.amount_paid label:first-child").addClass("required");
	}

	$(`#${modal} .modal-title`).text(title);

	clearAlertInForm(elems);
	clearSelect2Selection("#tasklead_id");

	fetchRecord(router.billing_invoice.fetch, { id: id }, modal, (res) => {
		if (res.status === STATUS.SUCCESS) {
			if (inObject(res, "data") && !isEmpty(res.data)) {
				const text = `${res.data.tasklead_id} | ${res.data.quotation} | ${res.data.client}`;
				const data = {
					client: res.data.client,
					manager: res.data.manager,
					project: res.data.project,
					project_amount: res.data.project_amount,
					type: res.data.type,
				};

				$("#due_date").val(res.data.due_date);
				$("#billing_amount").val(res.data.billing_amount);
				$("#amount_paid").val(
					billing_status ? res.data.billing_amount : res.data.amount_paid
				);
				$("#days_overdue").val(res.data.days_overdue || "");
				$("#overdue_interest").val(res.data.overdue_interest || "");
				$("#vat_amount").val(res.data.vat_amount || "");
				$("#grand_total").val(res.data.grand_total || "");
				$("#with_vat").prop("checked", res.data.with_vat != 0 ? true : false);
				$("#with_vat").trigger("change");
				$("#with_interest").prop(
					"checked",
					res.data.overdue_interest > 0 ? true : false
				);
				$("#with_interest").trigger("change");
				$("#orig_tasklead").html(
					`Original Task/Lead: <strong>${text}</strong>`
				);

				setSelect2AjaxSelection("#tasklead_id", text, res.data.tasklead_id);
				setTimeout(() => _loadTaskleadDetails(data), 200);

				setOptionValue("#bill_type", res.data.bill_type);
				setOptionValue("#payment_method", res.data.payment_method);

				if (!billing_status && res.data.billing_status !== "paid") {
					$(".form-group.amount_paid").addClass("d-none");
				}

				if (res.data.billing_status === "paid") {
					$(".form-group.amount_paid label:first-child").addClass("required");
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
	deleteRecord(router.billing_invoice.delete, { id: id }, table);
}

/* Change status record */

function change(id, changeTo, status) {
	const title = `${strUpper(status)} to ${strUpper(changeTo)}!`;
	const swalMsg = `
		<div>Billing #: <strong>${id}</strong></div>
		<div>Are you sure you want to <strong>${strUpper(
			changeTo
		)}</strong> this Billing Invoice?</div>
	`;
	const data = { id: id, status: changeTo };

	swalNotifConfirm(
		function () {
			$.post(router.billing_invoice.change, data)
				.then((res) => {
					const message = res.errors ?? res.message;

					if (res.status !== STATUS.ERROR) {
						refreshDataTable($("#" + table));
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

/* Quotation via ajax data source */
function _initTasklead() {
	select2AjaxInit(
		"#tasklead_id",
		"Search & select a tasklead",
		router.admin.common.quotations,
		["id", "quotation", "client"],
		_loadTaskleadDetails,
		{ search_in: ["quotation", "client"] }
	);
}

/* Load selected tasklead/quotation details */
function _loadTaskleadDetails(data) {
	let html = "",
		amount = 0;

	if (data.client) {
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
		amount = data.project_amount || 0;
	}

	$("#billing_amount").attr("max", amount);
	$(".tasklead-details").html(html);
}
