var table, modal, form, editRoute, removeRoute, elems;

$(document).ready(function () {
	table = "roles_table";
	modal = "roles_modal";
	form = "roles_form";
	editRoute = $("#edit_url").val();
	removeRoute = $("#remove_url").val();
	elems = ["role_code", "description"];

	$("#btn_add_record").on("click", function () {
		$(`#${modal}`).modal("show");
		$(`#${modal}`).removeClass("edit").addClass("add");
		$(`#${modal} .modal-title`).text("Add Role");
		$(`#${form}`)[0].reset();
		$("#role_id").val("");
		$("#prev_role_code").val("");

		clearAlertInForm(elems);
	});

	/* Load dataTable */
	const route = $("#" + table).data("url"),
		options = {
			columnDefs: {
				orderable: false,
				targets: -1,
			},
			order: [0, "asc"],
		};

	loadDataTable(table, route, METHOD.POST, options);

	/* Form for saving employee */
	formSubmit($("#" + form), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			self[0].reset();
			$("#role_id").val("");
			$("#prev_role_code").val("");

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
	$(`#${modal}`).modal("show");
	$(`#${modal}`).removeClass("add").addClass("edit");
	$(`#${modal} .modal-title`).text("Edit Role");
	$("#role_id").val(id);

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
					$("#prev_role_code").val(res.data.role_code);
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
