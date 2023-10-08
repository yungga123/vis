var table, modal, form, elems, is_manual;

$(document).ready(function () {
	table = "job_order_table";
	modal = "job_order_modal";
	form = "job_order_form";
	elems = [
		"quotation",
		"work_type",
		"comments",
		"date_requested",
		"date_reported",
		"date_committed",
		"warranty",
	];

	/* Filters */
	initSelect2Filters("filter_status", $pjOptions.status);
	initSelect2Filters("filter_qtype", $pjOptions.qtype);
	initSelect2Filters("filter_worktype", $pjOptions.worktype);

	/* Quotation via ajax data source */
	select2AjaxInit(
		"#select2Quotation",
		"Search a quotation",
		router.admin.common.quotations,
		"quotation",
		loadQDetails
	);

	/* Load dataTable */
	loadDataTable(table, router.job_order.list, METHOD.POST);

	/* Toggle modal */
	$("#btn_add_record").on("click", function () {
		$(`#${modal}`).modal("show");
		$(`#${modal}`).removeClass("edit").addClass("add");
		$(`#${modal} .modal-title`).text("Add Job Order");
		$(`#${form}`)[0].reset();
		$("#job_order_id").val("");
		$("#orig_qn").addClass("d-none");

		clearAlertInForm(elems);
	});

	$("#is_manual").on("change", function (e) {
		if (e.target.checked) {
			is_manual = true;
			toggleQuotationFields(is_manual);
			if (!isSelect2Initialized("#customer_id")) initSelect2Customers();
			return;
		}

		toggleQuotationFields();
	});

	/* Initial init of customers (commerical) via ajax data source */
	onChangeCustomerType();

	/* Initialize employee_id select2 */
	select2Init("#employee_id_status", "Select person incharge");

	/* Form for saving job order */
	formSubmit($("#" + form), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			self[0].reset();
			refreshDataTable($("#" + table));
			notifMsgSwal(res.status, message, res.status);
			clearSelect2Selection("#select2Quotation");
			$("#job_order_id").val("");
			$("#tasklead_id").val("");
			$("#quotation").val("");
			$("#orig_qn").addClass("d-none");

			if (is_manual) {
				clearSelect2Selection("#customer_id");
				toggleQuotationFields();
			}

			if ($(`#${modal}`).hasClass("edit")) {
				$(`#${modal}`).modal("hide");
			}
		}

		showAlertInForm(elems, message, res.status);
	});

	/* For saving job order status form */
	formSubmit($("#status_form"), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			self[0].reset();
			refreshDataTable($("#" + table));
			notifMsgSwal(res.status, message, res.status);
			$("#status_modal").modal("hide");
			$("#status").val("");
		}

		showAlertInForm(elems, message, res.status);
	});
});

/* For filtering and reseting */
function filterData(reset = false) {
	const status = getSelect2Selection("#filter_status");
	const qtype = getSelect2Selection("#filter_qtype");
	const worktype = getSelect2Selection("#filter_worktype");

	showLoading();
	if (!isEmpty(status) || !isEmpty(qtype) || !isEmpty(worktype)) {
		let options = {
			params: {
				status: status,
				type: qtype,
				work_type: worktype,
			},
		};

		if (reset) {
			options.params = null;
			clearSelect2Selection("#filter_status");
			clearSelect2Selection("#filter_qtype");
			clearSelect2Selection("#filter_worktype");
		}

		loadDataTable(table, router.job_order.list, METHOD.POST, options, true);
	} else {
		closeLoading();
		if (reset) return;
		notifMsgSwal(TITLE.WARNING, "Please select at least first!", STATUS.INFO);
	}
	closeLoading();
}

/* Load selected tasklead/quotation details */
function loadQDetails(data) {
	let html = "",
		id = "",
		quotation = "",
		employee_id = "";

	if (data.quotation) {
		id = data.id;
		quotation = data.quotation;
		employee_id = data.employee_id;
		html = `
			<h5 class="text-center">Details</h5>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>Client</th>
						<th>Manger</th>
						<th>Project</th>
						<th>Amount</th>
						<th>Start Date</th>
						<th>Finish Date</th>
					</tr>
				</thead>
				<tbody>				
					<tr>					
						<td>${data.client}</td>
						<td>${data.manager}</td>
						<td>${data.project}</td>
						<td>${data.project_amount}</td>
						<td>${data.project_start_date}</td>
						<td>${data.project_finish_date}</td>
					</tr>
				</tbody>
			</table>
		`;
	}

	$("#tasklead_id").val(id);
	$("#quotation").val(quotation);
	$("#employee_id").val(employee_id);

	$(".q-details").html(html);
}

/* Get record details */
function edit(id) {
	$(`#${modal}`).removeClass("add").addClass("edit");
	$(`#${modal} .modal-title`).text("Edit Item");
	$("#job_order_id").val(id);

	clearAlertInForm(elems);
	showLoading();

	$.post(router.job_order.fetch, { id: id })
		.then((res) => {
			closeLoading();

			if (res.status === STATUS.SUCCESS) {
				const isNotManual = res.data.is_manual == "0";
				if (isNotManual) {
					// Set selected quotation in select2
					setSelect2AjaxSelection("#select2Quotation", res.data.quotation, id);
					clearSelect2Selection("#customer_id");
				} else {
					$("#is_manual").prop("checked", true);
					$("#" + res.data.customer_type).prop("checked", true);
					setSelect2AjaxSelection(
						"#customer_id",
						res.data.client,
						res.data.customer_id
					);
					initSelect2Customers(strLower(res.data.customer_type));
					clearSelect2Selection("#select2Quotation");
				}

				toggleQuotationFields(!isNotManual);
				$.each(res.data, (key, value) => {
					if (key !== "customer_type") $(`input[name="${key}"]`).val(value);
				});
				$("#orig_qn")
					.removeClass()
					.html(`Original Quotation #: <strong>${res.data.quotation}</strong>`);
				$("#comments").val(res.data.comments);
				$("#created_by").val(res.data.requested_by);

				setTimeout(() => {
					$("#tasklead_id").val(res.data.tasklead_id);
					$("#employee_id").val(res.data.employee_id);
					$("#quotation").val(res.data.quotation);
				}, 500);

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
			$.post(router.job_order.delete, { id: id })
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
function status(id, changeTo, status) {
	const modal = $("#status_modal");
	$("#status").val("");

	if (modal.hasClass("show")) modal.modal("hide");
	if (changeTo === "discard" || changeTo === "pending") {
		// Change status directly - no need form
		let swalMsg = `
			<div>Job Order #: <strong>${id}</strong></div>
			<div>Are you sure you want to <strong>${strUpper(
				changeTo
			)}</strong> this JO?</div>
		`;

		if (changeTo === "pending") {
			swalMsg += `<div>This JO will be reverted back to ${changeTo}!</div>`;
		}

		const data = { id: id, status: changeTo };

		swalNotifConfirm(
			function () {
				$.post(router.job_order.status, data)
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
	} else {
		toggleStatusFields(changeTo, "accept");
		toggleStatusFields(changeTo, "file");

		$("#status_modal").modal("show");
		$("#job_order_id_status").val(id);
		$("#status").val(changeTo);

		$("#status_modal .modal-title").text(
			`Change Status from ${status.toUpperCase()} to ${changeTo.toUpperCase()}`
		);

		// Get other details
		$.post(router.job_order.fetch, { id: id, status: true })
			.then((res) => {
				$("#date_committed_status").val(res.data.date_committed);
				setSelect2Selection("#employee_id_status", res.data.employee_id);
				$("#type").val(res.data.type || "Project");
				$("#remarks").val(res.data.remarks);
			})
			.catch((err) => catchErrMsg(err));
	}
}

/* Toggle addtional fields for status modal */
function toggleStatusFields(changeTo, val) {
	const fields = $("#fields_" + changeTo);

	if (val === changeTo) fields.removeClass("d-none");
	else $("#fields_" + val).addClass("d-none");
}

/* Initialize select2 filters */
function initSelect2Filters(id, options) {
	$("#" + id).select2({
		data: formatOptionsForSelect2(options),
		allowClear: true,
		width: "100%",
	});
}

/* Toggle (hide or show) default or manual quotation */
function toggleQuotationFields(isManual = false) {
	if (isManual) {
		$("#quotation_wrapper").addClass("d-none");
		$("#manual_quotation_wrapper").removeClass("d-none");
	} else {
		$("#quotation_wrapper").removeClass("d-none");
		$("#manual_quotation_wrapper").addClass("d-none");
	}
}
