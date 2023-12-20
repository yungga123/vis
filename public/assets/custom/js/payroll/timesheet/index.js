var table, modal, form, elems;

$(document).ready(function () {
	table = "timesheet_table";
	modal = "timesheet_modal";
	form = "timesheet_form";
	elems = ["clock_date", "clock_in", "clock_out", "remark"];

	$("#btn_add_record").on("click", function () {
		$(`#${modal}`).modal("show");
		$(`#${modal}`).removeClass("edit").addClass("add");
		$(`#${modal} .modal-title`).text("Add Timesheet");
		$(`#${form}`)[0].reset();
		$("#id").val("");

		clearAlertInForm(elems);
	});

	$(".clock-attendance .card-header").addClass("d-none");

	getCurrentClockAttendance();

	/* Load dataTable */
	loadDataTable(table, router.payroll.timesheet.list, METHOD.POST);

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
});

/* For filtering and reseting */
function filterData(reset = false) {
	const start_date = $("#filter_start_date").val();
	const end_date = $("#filter_end_date").val();
	const view = $("#filter_view").val();
	const params = {
		start_date: start_date,
		end_date: end_date,
		view: view,
	};
	const condition =
		!isEmpty(start_date) || !isEmpty(end_date) || !isEmpty(view);

	filterParam(
		router.payroll.timesheet.list,
		table,
		params,
		condition,
		() => {
			$("#filter_start_date").val("");
			$("#filter_end_date").val("");
			$("#filter_view").val("all");
		},
		reset
	);
}

/* Get record details */
function edit(id) {
	$(`#${modal}`).removeClass("add").addClass("edit");
	$(`#${modal} .modal-title`).text("Edit Timesheet");
	$("#id").val(id);

	clearAlertInForm(elems);
	showLoading();

	fetchRecord(router.payroll.timesheet.fetch, { id: id }, modal, (res) => {
		if (res.status === STATUS.SUCCESS) {
			if (inObject(res, "data") && !isEmpty(res.data)) {
				$("#clock_date").val(res.data.clock_date);
				$("#clock_in").val(res.data.clock_in);
				$("#clock_out").val(res.data.clock_out);
				$("#remark").val(res.data.remark);

				$(`#${modal}`).modal("show");
			}
		}
	});
}

/* Delete record */
function remove(id) {
	deleteRecord(router.payroll.timesheet.delete, { id: id }, table);
}
