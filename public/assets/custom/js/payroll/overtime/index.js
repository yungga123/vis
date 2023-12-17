var table, modal, form, elems;

$(document).ready(function () {
	table = "overtime_table";
	modal = "overtime_modal";
	form = "overtime_form";
	elems = ["date", "time_start", "time_end", "reason"];

	select2Init("#filter_status");

	$("#btn_add_record").on("click", function () {
		$(`#${modal}`).modal("show");
		$(`#${modal}`).removeClass("edit").addClass("add");
		$(`#${modal} .modal-title`).text("File an Overtime");
		$(`#${form}`)[0].reset();
		$("#id").val("");

		clearAlertInForm(elems);
	});

	/* Load dataTable */
	loadDataTable(table, router.overtime.list, METHOD.POST);

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
	const params = {
		status: status,
	};
	const condition = !isEmpty(status);

	filterParam(
		router.overtime.list,
		table,
		params,
		condition,
		() => clearSelect2Selection("#filter_status"),
		reset
	);
}

/* Get record details */
function edit(id) {
	$(`#${modal}`).removeClass("add").addClass("edit");
	$(`#${modal} .modal-title`).text("Edit Overtime");
	$("#id").val(id);

	clearAlertInForm(elems);

	fetchRecord(router.overtime.fetch, { id: id }, modal, (res) => {
		if (res.status === STATUS.SUCCESS) {
			if (inObject(res, "data") && !isEmpty(res.data)) {
				$("#date").val(res.data.date);
				$("#time_start").val(res.data.time_start);
				$("#time_end").val(res.data.time_end);
				$("#reason").val(res.data.reason);
				$(`#${modal}`).modal("show");
			}
		}
	});
}

/* Change status record */
function change(id, changeTo, status, with_pay) {
	const title = `Change Overtime Status - <strong>${strUpper(
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
	deleteRecord(router.overtime.delete, { id: id }, table);
}
