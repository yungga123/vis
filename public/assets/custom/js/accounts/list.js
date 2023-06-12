var table, modal, form, editRoute, removeRoute, elems;

$(document).ready(function () {
	table = "account_table";
	modal = "account_modal";
	form = "account_form";
	editRoute = $("#edit_url").val();
	removeRoute = $("#remove_url").val();
	elems = ["employee_id", "username", "password", "access_level"];

	select2Init("#employee_id");

	$("#btn_add_record").on("click", function () {
		$(`#${modal}`).modal("show");
		$(`#${modal}`).removeClass("edit").addClass("add");
		$(`#${modal} .modal-title`).text("Add New Account");
		$(`#${form}`)[0].reset();
		$("#account_id").val("");
		$("#employee_id").attr("disabled", false);
		$("#employee_id1").val("").attr("name", "employee_id1");
		$(".lbl_password").addClass("required");
		$("#small_password").css("display", "none");
		setOptionValue("#employee_id", "");

		clearAlertInForm(elems);
	});

	/* Load dataTable */
	const route = $("#" + table).data("url");
	const options = {
		columnDefs: {
			targets: -1,
			orderable: false,
		},
	};
	loadDataTable(table, route, METHOD.POST, options);

	/* Form for saving account */
	formSubmit($("#" + form), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			$("#account_id").val("");
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

/* Get account details */
function edit(id) {
	$(`#${modal}`).removeClass("add").addClass("edit");
	$(`#${modal} .modal-title`).text("Edit Account");
	$("#account_id").val(id);

	clearAlertInForm(elems);
	showLoading();

	$.post(editRoute, { id: id })
		.then((res) => {
			closeLoading();

			if (res.status === STATUS.SUCCESS) {
				if (inObject(res, "data") && !isEmpty(res.data)) {
					$("#username").val(res.data.username);
					$("#prev_username").val(res.data.username);
					$("#employee_id").attr("disabled", true);
					setOptionValue("#employee_id", res.data.employee_id);
					setOptionValue("#access_level", res.data.access_level);
					$("#employee_id1")
						.val(res.data.employee_id)
						.attr("name", "employee_id");
					$(".lbl_password").removeClass("required");
					$("#small_password").css("display", "block");
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
