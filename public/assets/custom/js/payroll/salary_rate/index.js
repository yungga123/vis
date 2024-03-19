var table, modal, form, elems;

$(document).ready(function () {
	table = "salary_rate_table";
	modal = "salary_rate_modal";
	form = "salary_rate_form";
	elems = ["employee_id", "salary_rate", "rate_type"];

	select2Init("#filter_employee_status");
	select2Init("#filter_rate_type");
	select2Init("#filter_payout");

	$("#btn_add_record").on("click", function () {
		$(`#${modal}`).modal("show");
		$(`#${modal}`).removeClass("edit").addClass("add");
		$(`#${modal} .modal-title`).text("Add Salary Rate");
		$(`#${form}`)[0].reset();
		$("#id").val("");

		clearSelect2Selection("#employee_id");
		clearAlertInForm(elems);
		_isUpdate(false);
	});

	/* Load dataTable */
	loadDataTable(table, router.salary_rate.list, METHOD.POST);

	/* Form for saving record */
	formSubmit($("#" + form), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			$("#id").val("");
			self[0].reset();
			refreshDataTable($("#" + table));
			notifMsgSwal(res.status, res.message, res.status);
			clearSelect2Selection("#employee_id");

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
	const employee_status = getSelect2Selection("#filter_employee_status");
	const rate_type = getSelect2Selection("#filter_rate_type");
	const payout = getSelect2Selection("#filter_payout");
	const params = {
		employee_status: employee_status,
		rate_type: rate_type,
		payout: payout,
	};
	const condition =
		!isEmpty(employee_status) || !isEmpty(rate_type) || !isEmpty(payout);

	filterParam(
		router.salary_rate.list,
		table,
		params,
		condition,
		() => {
			clearSelect2Selection("#filter_employee_status");
			clearSelect2Selection("#filter_rate_type");
			clearSelect2Selection("#filter_payout");
		},
		reset
	);
}

/* Get record details */
function edit(id) {
	$(`#${modal}`).removeClass("add").addClass("edit");
	$(`#${modal} .modal-title`).text("Edit Salary Rate");
	$("#id").val(id);

	clearSelect2Selection("#employee_id");
	clearAlertInForm(elems);
	showLoading();
	_isUpdate();

	fetchRecord(router.salary_rate.fetch, { id: id }, modal, (res) => {
		if (res.status === STATUS.SUCCESS) {
			if (inObject(res, "data") && !isEmpty(res.data)) {
				setSelect2AjaxSelection(
					"#employee_id",
					res.data.employee_name,
					res.data.employee_id
				);
				setOptionValue("#rate_type", res.data.rate_type);
				setOptionValue("#payout", res.data.payout);

				$("#salary_rate").val(res.data.salary_rate);
				$(`#${modal}`).modal("show");
			}
		}
	});
}

/* Delete record */
function remove(id) {
	deleteRecord(router.salary_rate.delete, { id: id }, table);
}

/* Employees select2 via ajax data source */
function _initSelect2Employees() {
	select2AjaxInit(
		"#employee_id",
		"Select an employee",
		router.employee.common.search,
		null,
		null,
		{ is_salary_rate: true }
	);
}

/**
 * Whether to toggle multiple selection of employee.
 * For multiple insert of records.
 */
function _isUpdate(update = true) {
	$("#employee_id").attr("name", "employee_id[]");
	$("#employee_id").attr("multiple", "true");
	$("#employee_id").removeAttr("disabled");

	if (update) {
		destroySelect2("#employee_id");
		$("#employee_id").html("");
		$("#employee_id").attr("name", "employee_id");
		$("#employee_id").attr("disabled", "true");
		$("#employee_id").removeAttr("multiple");
	}
	_initSelect2Employees();
}
