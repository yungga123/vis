var table, modal, form, elems, $technicians;

$(document).ready(function () {
	table = "dispatch_table";
	modal = "dispatch_modal";
	form = "dispatch_form";
	elems = [
		"schedule_id",
		"customer_id",
		"sr_number",
		"dispatch_date",
		"dispatch_out",
		"time_in",
		"time_out",
		"remarks",
		"service_type",
		"comments",
		"with_permit",
		"technicians",
	];
	$technicians = formatOptionsForSelect2(
		$pjOptions.employees,
		"employee_id",
		"employee_name"
	);

	/* Load dataTable */
	loadDataTable(table, router.dispatch.list, METHOD.POST);

	/* Toggle modal */
	$("#btn_add_record").on("click", function () {
		$(`#${modal}`).modal("show");
		$(`#${modal}`).removeClass("edit").addClass("add");
		$(`#${modal} .modal-title`).text("Add Dispatch");
		$(`#${form}`)[0].reset();
		$("#dispatch_id").val("");
		$("#schedule_id").val("");
		$("#orig_schedule").addClass("d-none");
		$(".schedule-details").html("");
		clearSelect2Selection("#schedules");
		clearSelect2Selection("#technicians");
		clearSelect2Selection("#checked_by");
		clearAlertInForm(elems);
	});

	/* Schedules select2 via ajax data source */
	select2AjaxInit(
		"#schedules",
		"Search a schedule",
		router.admin.common.schedules,
		"title",
		loadScheduleDetails,
		{ from_jo_only: true }
	);

	/* Initialize select2 employees/technicians */
	select2Init("#technicians", "Select technicians", $technicians);

	/* Initialize select2 employees/check by */
	select2Init("#checked_by", "Select an employee");

	/* Form for saving record */
	formSubmit($("#" + form), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			self[0].reset();
			refreshDataTable($("#" + table));
			notifMsgSwal(res.status, message, res.status);
			$("#dispatch_id").val("");
			$("#schedule_id").val("");
			$("#orig_schedule").addClass("d-none");
			$(".schedule-details").html("");
			clearSelect2Selection("#schedules");
			clearSelect2Selection("#technicians");
			clearSelect2Selection("#checked_by");

			if ($(`#${modal}`).hasClass("edit")) {
				$(`#${modal}`).modal("hide");
			}
		}

		showAlertInForm(elems, message, res.status);
	});
});

/* Get record details */
function edit(id) {
	$(`#${modal}`).removeClass("add").addClass("edit");
	$(`#${modal} .modal-title`).text("Edit Item");
	$("#dispatch_id").val(id);
	$("#schedule_id").val("");

	clearAlertInForm(elems);
	showLoading();

	$.post(router.dispatch.fetch, { id: id })
		.then((res) => {
			closeLoading();

			if (res.status === STATUS.SUCCESS) {
				// Set selected schedule in select2
				setSelect2AjaxSelection(
					"#schedules",
					res.data.schedule,
					res.data.schedule_id
				);

				// Set selected technicians in select2
				setSelect2Technicians(res.data.technicians);

				if (!isEmpty(res.data.customer_type))
					$("#" + strLower(res.data.customer_type)).prop("checked", true);

				// Set selected employee/checked by in select2
				setSelect2Selection("#checked_by", res.data.checked_by);

				$.each(res.data, (key, value) => {
					if (key !== "customer_type") $(`input[name="${key}"]`).val(value);
				});

				$("#orig_schedule")
					.removeClass()
					.html(`Original schedule: <strong>${res.data.schedule}</strong>`);
				$("#remarks").val(res.data.remarks);
				$("#comments").val(res.data.comments);
				setOptionValue("#service_type", res.data.service_type);
				setOptionValue("#with_permit", res.data.with_permit);

				setTimeout(() => {
					$("#schedule_id").val(res.data.schedule_id);
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
			$.post(router.dispatch.delete, { id: id })
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

/* Load selected tasklead/quotation details */
function loadScheduleDetails(data) {
	let html = "",
		id = "";

	if (data.title) {
		const stype = $pjOptions.schedule_type[data.type];
		const format = "MMM DD, YYYY hh:mm A";

		id = data.id;
		html = `
			<h5 class="text-center">Details</h5>
			<table class="table table-bordered">
				<thead>
					<tr>
						<th>ID</th>
						<th>Description</th>
						<th>Start Date & Time</th>
						<th>Start Date & Time</th>
						<th>Type</th>
					</tr>
				</thead>
				<tbody>
					<tr class="text-white text-bold" style="background-color: ${stype.color};">					
						<td>${data.id}</td>
						<td>${data.description}</td>
						<td>${moment(data.start).format(format)}</td>
						<td>${moment(data.end).format(format)}</td>
						<td>${stype.text}</td>
					</tr>
				</tbody>
			</table>
		`;
	}

	$("#schedule_id").val(id);
	$(".schedule-details").html(html);
}

/* Set select2 technicians */
function setSelect2Technicians(technicians) {
	if (!isEmpty(technicians)) {
		const data = $.map(technicians, (val, index) => val.employee_id);
		setSelect2Selection("#technicians", data);
	}
}
