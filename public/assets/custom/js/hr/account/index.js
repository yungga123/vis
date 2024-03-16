var table, modal, form, elems;

$(document).ready(function () {
	table = "account_table";
	modal = "account_modal";
	form = "account_form";
	elems = ["employee_id", "username", "password", "access_level"];

	select2Init("#employee_id");
	select2Init("#filter_access_level");
	passwordShowHideInit();

	$("#btn_add_record").on("click", function () {
		$(`#${modal}`).modal("show");
		$(`#${modal}`).removeClass("edit").addClass("add");
		$(`#${modal} .modal-title`).text("Add New Account");
		$(`#${form}`)[0].reset();

		$("#account_id").val("");
		$("#employee_id").attr("disabled", false).removeClass("d-none");
		$("#employee_id1").val("").attr("name", "employee_id1");
		$("#employee_name").val("").attr("type", "hidden");
		$(".lbl_password").addClass("required");
		$("#small_password").css("display", "none");

		setOptionValue("#employee_id", "");
		clearSelect2Selection("#employee_id");
		clearAlertInForm(elems);

		/* Initialize select2 employees/check by */
		select2AjaxInit(
			"#employee_id",
			"Select an employee",
			router.employee.common.search,
			"text"
		);
	});

	/* Load dataTable */
	const options = {
		columnDefs: {
			targets: 0,
			orderable: false,
		},
	};
	loadDataTable(table, router.account.list, METHOD.POST, options);

	/* Form for saving account */
	formSubmit($("#" + form), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			$("#account_id").val("");
			self[0].reset();
			refreshDataTable($("#" + table));
			notifMsgSwal(res.status, message, res.status);
			clearSelect2Selection("#employee_id", "");

			if ($(`#${modal}`).hasClass("edit")) {
				$(`#${modal}`).modal("hide");
			}
		}

		showAlertInForm(elems, message, res.status);
	});
});

/* For filtering and reseting */
function filterData(reset = false) {
	const access_level = getSelect2Selection("#filter_access_level");
	const start_date = $("#filter_start_date").val();
	const end_date = $("#filter_end_date").val();
	const params = {
		access_level: access_level,
		start_date: start_date,
		end_date: end_date,
	};
	const condition =
		!isEmpty(access_level) || (!isEmpty(start_date) && !isEmpty(end_date));

	filterParam(
		router.account.list,
		table,
		params,
		condition,
		() => {
			clearSelect2Selection("#filter_access_level");
			$("#filter_start_date").val("");
			$("#filter_end_date").val("");
		},
		reset
	);
}

/* Get account details */
function edit(id) {
	$(`#${modal}`).removeClass("add").addClass("edit");
	$(`#${modal} .modal-title`).text("Edit Account");
	$("#account_id").val(id);
	$("#employee_name").val("").attr("type", "hidden");

	clearAlertInForm(elems);
	showLoading();

	$.post(router.account.fetch, { id: id })
		.then((res) => {
			closeLoading();

			if (res.status === STATUS.SUCCESS) {
				if (inObject(res, "data") && !isEmpty(res.data)) {
					destroySelect2("#employee_id");
					setOptionValue("#access_level", res.data.access_level);

					$("#username").val(res.data.username);
					$("#prev_username").val(res.data.username);
					$("#employee_id").attr("disabled", true).addClass("d-none");
					$("#employee_id1")
						.val(res.data.employee_id)
						.attr("name", "employee_id");
					$("#employee_name").val(res.data.employee_name).attr("type", "text");
					$(".lbl_password").removeClass("required");
					$("#small_password").css("display", "block");

					$(`#${modal}`).modal("show");
				}
			} else {
				$(`#${modal}`).modal("hide");
				notifMsgSwal(res.status, res.message, res.status);
			}
		})
		.catch((err) => catchErrMsg(err));
}

/* Delete account */
function remove(id) {
	const swalMsg = "delete";
	swalNotifConfirm(
		function () {
			$.post(router.account.delete, { id: id })
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
