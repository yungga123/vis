var table, modal, form, elems;

$(document).ready(function () {
	table = "leave_table";
	modal = "leave_modal";
	form = "leave_form";
	elems = [
		"employee_id",
		"leave_type",
		"start_date",
		"end_date",
		"leave_reason",
		"leave_remark",
	];

	select2Init("#filter_status");
	select2Init("#filter_leave_type");

	$("#btn_add_record").on("click", function () {
		$(`#${modal}`).modal("show");
		$(`#${modal}`).removeClass("edit").addClass("add");
		$(`#${modal} .modal-title`).text("File a Leave");
		$(`#${form}`)[0].reset();
		$("#id").val("");

		clearAlertInForm(elems);
	});

	$("#start_date").on("change", function () {
		const startDate = $(this).val();

		// Set minimum end date
		$("#end_date").attr("min", startDate).removeAttr("readonly");
	});

	/* Load dataTable */
	loadDataTable(table, router.leave.list, METHOD.POST);

	/* Form for saving record */
	formSubmit($("#" + form), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			$("#id").val("");
			self[0].reset();
			refreshDataTable($("#" + table));
			notifMsgSwal(res.status, res.message, res.status);

			if ($(`#${modal}`).hasClass("edit")) {
				$(`#${modal}`).modal("hide");
			}
		}

		if (res.errors) notifMsg(res.message, res.status);

		showAlertInForm(elems, message, res.status);
	});

	/* Form for change record */
	formSubmit($("#status_form"), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			self[0].reset();
			$("#_id").val("");
			$("#status").val("");
			$("#status_modal").modal("hide");

			refreshDataTable($("#" + table));
			notifMsgSwal(res.status, res.message, res.status);
		}

		if (res.errors) notifMsg(res.message, res.status);

		showAlertInForm(["leave_remark"], message, res.status);
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
		router.leave.list,
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
	$("#id").val(id);

	clearAlertInForm(elems);

	fetchRecord(router.leave.fetch, { id: id }, modal, (res) => {
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

/* Change status record */
function change(id, changeTo, status, with_pay) {
	const title = `Change Leave Status - <strong>${strUpper(
		status
	)} to ${strUpper(changeTo)}</strong>!`;

	$(".form-group.with_pay").removeClass("d-none");
	if (changeTo === "discard") {
		$(".form-group.with_pay").addClass("d-none");
	}

	$("#_id").val(id);
	$("#status").val(changeTo);
	$("#with_pay").prop("checked", with_pay != 0 ? true : false);
	$("#status_modal").modal("show");
	$("#status_modal .modal-title").html(title);
}

/* Delete record */
function remove(id) {
	deleteRecord(router.leave.delete, { id: id }, table);
}
