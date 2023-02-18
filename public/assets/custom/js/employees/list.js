var table, modal, form, editRoute, removeRoute, elems;

$(document).ready(function () {
	table = "employee_table";
	modal = "employee_modal";
	form = "employee_form";
	editRoute = $("#edit_url").val();
	removeRoute = $("#remove_url").val();
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
		"language",
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
	const route = $("#" + table).data("url");
	loadDataTable(table, route, METHOD.POST, { order: [1, "asc"] });

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

		showAlertInForm(elems, message, res.status);
	});
});

/* Get employee details */
function edit(id) {
	$(`#${modal}`).removeClass("add").addClass("edit");
	$(`#${modal} .modal-title`).text("Edit Employee");
	$("#id").val(id);

	clearAlertInForm(elems);
	showLoading();

	$.post(editRoute, { id: id })
		.then((res) => {
			closeLoading();

			if (res.status === STATUS.SUCCESS) {
				if (inObject(res, "data") && !isEmpty(res.data)) {
					$.each(res.data, (key, value) => {
						$(`input[name="${key}"]`).val(value);
					});

					$("#employee_id").attr("readonly", true);
					$("#prev_employee_id").val(res.data.employee_id);
					setOptionValue("#gender", res.data.gender);
					setOptionValue("#civil_status", res.data.civil_status);
					setOptionValue("#date_of_birth", res.data.date_of_birth);
					setOptionValue("#employment_status", res.data.employment_status);
					setOptionValue("#date_hired", res.data.date_hired);
					setOptionValue("#date_resigned", res.data.date_resigned);
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
			$.post(removeRoute, { id: id })
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
