var table, modal, form, elems;

$(document).ready(function () {
	table = "employee_table";
	modal = "employee_modal";
	form = "employee_form";
	elems = [
		"employee_id",
		"firstname",
		"middlename",
		"lastname",
		"gender",
		"civil_status",
		"date_of_birth",
		"place_of_birth",
		"postal_code",
		"address_province",
		"address_city",
		"address_brgy",
		"address_sub",
		"contact_number",
		"email_address",
		"sss_no",
		"tin_no",
		"philhealth_no",
		"pag_ibig_no",
		"educational_attainment",
		"course",
		"emergency_name",
		"emergency_contact_no",
		"emergency_address",
		"name_of_spouse",
		"spouse_contact_no",
		"no_of_children",
		"spouse_address",
		"position",
		"employment_status",
		"date_hired",
		"date_resigned",
	];

	select2Init("#filter_status");

	$("#btn_add_record").on("click", function () {
		$(`#${modal}`).modal("show");
		$(`#${modal}`).removeClass("edit").addClass("add");
		$(`#${modal} .modal-title`).text("Add New Employee");
		$(`#${form}`)[0].reset();
		$("#id").val("");
		$("#employee_id").attr("readonly", false);
		$("#prev_employee_id").val("");

		clearAlertInForm(elems);
	});

	/* Load dataTable */
	loadDataTable(table, router.employee.list, METHOD.POST);

	/* Form for saving employee */
	formSubmit($("#" + form), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			$("#id").val("");
			self[0].reset();
			refreshDataTable($("#" + table));
			notifMsgSwal(res.status, message, res.status);

			if ($(`#${modal}`).hasClass("edit")) {
				$(`#${modal}`).modal("hide");
			}
		}

		if (inObject(res, "errors")) {
			notifMsgSwal(res.status, res.message, res.status);
		}

		showAlertInForm(elems, message, res.status);
	});

	/* Form for changing employment status */
	formSubmit($("#employment_status_form"), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			self[0].reset();
			refreshDataTable($("#" + table));
			$(`#employment_status_modal`).modal("hide");
		}

		notifMsgSwal(res.status, message, res.status);
	});

	$("#employment_status_modal #_employment_status").on("change", function () {
		$("#label_date_resigned").removeClass("required");

		if ($(this).val() === "Resigned")
			$("#label_date_resigned").addClass("required");
	});
});

/* For filtering and reseting */
function filterData(reset = false) {
	const status = getSelect2Selection("#filter_status");
	const params = {
		employment_status: status,
	};
	const condition = !isEmpty(status);

	filterParam(
		router.employee.list,
		table,
		params,
		condition,
		() => clearSelect2Selection("#filter_status"),
		reset
	);
}

/* Get employee details */
function edit(id) {
	$(`#${modal}`).removeClass("add").addClass("edit");
	$(`#${modal} .modal-title`).text("Edit Employee");
	$("#id").val(id);

	clearAlertInForm(elems);
	showLoading();

	$.post(router.employee.fetch, { id: id })
		.then((res) => {
			closeLoading();

			if (res.status === STATUS.SUCCESS) {
				if (inObject(res, "data") && !isEmpty(res.data)) {
					$.each(res.data, (key, value) => {
						$(`input[name="${key}"]`).val(value);
					});

					$("#prev_employee_id").val(res.data.employee_id);
					setOptionValue("#gender", res.data.gender);
					setOptionValue("#civil_status", res.data.civil_status);
					setOptionValue("#date_of_birth", res.data.date_of_birth);
					setOptionValue("#employment_status", res.data.employment_status);
					setOptionValue("#date_hired", res.data.date_hired);
					setOptionValue("#date_resigned", res.data.date_resigned);
					$(`#${modal}`).modal("show");
				}
			} else {
				$(`#${modal}`).modal("hide");
				notifMsgSwal(res.status, res.message, res.status);
			}
		})
		.catch((err) => catchErrMsg(err));
}

/* Delete employee */
function remove(id) {
	const swalMsg = "delete";
	swalNotifConfirm(
		function () {
			$.post(router.employee.delete, { id: id })
				.then((res) => {
					const message = res.errors ?? res.message;

					if (res.status === STATUS.SUCCESS) refreshDataTable($("#" + table));
					notifMsgSwal(res.status, message, res.status);
				})
				.catch((err) => catchErrMsg(err));
		},
		TITLE.WARNING,
		swalMsg,
		STATUS.WARNING
	);
}

/* Change employment status */
function change(id, employee_id, status) {
	const modal = "employment_status_modal";
	$(`#${modal}`).modal("show");

	// Set value
	$(`#${modal} #_id`).val(id);
	$(`#${modal} #_employee_id`).val(employee_id);
	$(`#${modal} #_date_resigned`).val("");
	setOptionValue(`#${modal} #_employment_status`, status);
}
