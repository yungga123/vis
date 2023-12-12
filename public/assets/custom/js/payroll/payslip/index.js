var table, modal, form, elems;

$(document).ready(function () {
	table = "payslip_table";
	modal = "payslip_modal";
	form = "payslip_form";
	elems = [];

	$("#btn_add_record").on("click", function () {
		$(`#${modal}`).modal("show");
		$(`#${modal}`).removeClass("edit").addClass("add");
		$(`#${modal} .modal-title`).text("Add New Employee");
		$(`#${form}`)[0].reset();
		$("#id").val("");

		clearAlertInForm(elems);
	});

	/* Load dataTable */
	// loadDataTable(table, router.payslip.list, METHOD.POST);

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
});

/* Get employee details */
function edit(id) {
	$(`#${modal}`).removeClass("add").addClass("edit");
	$(`#${modal} .modal-title`).text("Edit Employee");
	$("#id").val(id);

	clearAlertInForm(elems);
	showLoading();

	$.post(router.payslip.fetch, { id: id })
		.then((res) => {
			closeLoading();

			if (res.status === STATUS.SUCCESS) {
				if (inObject(res, "data") && !isEmpty(res.data)) {
					$.each(res.data, (key, value) => {
						$(`input[name="${key}"]`).val(value);
					});
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
			$.post(router.payslip.delete, { id: id })
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
