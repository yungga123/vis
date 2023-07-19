var table, modal, form, elems;

$(document).ready(function () {
	table = "permission_table";
	modal = "permission_modal";
	form = "permission_form";
	editRoute = $("#edit_url").val();
	removeRoute = $("#remove_url").val();
	elems = ["role_code", "module_code", "permissions"];

	select2Init("#permissions");

	$("#btn_add_record").on("click", function () {
		$(`#${modal}`).modal("show");
		$(`#${modal}`).removeClass("edit").addClass("add");
		$(`#${modal} .modal-title`).text("Add Permission");
		$(`#${form}`)[0].reset();
		$("#permission_id").val("");
		$("#permissions").val("").change();

		clearAlertInForm(elems);
	});

	$("#module_code").on("change", function () {
		changePermissionsOptions($(this).val() === "INVENTORY");
	});

	/* Load dataTable */
	const options = {
		columnDefs: {
			orderable: false,
			targets: -1,
		},
		order: [0, "asc"],
	};

	loadDataTable(table, router.permission.list, METHOD.POST, options);

	/* Form for saving employee */
	formSubmit($("#" + form), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			self[0].reset();
			$("#id").val("");
			$("#permissions").val("").trigger("change");

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
	$(`#${modal} .modal-title`).text("Edit Permission");
	$("#permission_id").val(id);

	clearAlertInForm(elems);
	showLoading();

	$.post(router.permission.edit, { id: id })
		.then((res) => {
			closeLoading();

			if (res.status === STATUS.SUCCESS) {
				if (inObject(res, "data") && !isEmpty(res.data)) {
					setOptionValue("#role_code", res.data.role_code);
					setOptionValue("#module_code", res.data.module_code);
					$("#permissions").val(res.data.permissions).change();
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
			$.post(router.permission.delete, { id: id })
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

/* For changing permissions options */
function changePermissionsOptions(withItemInAndOut = false) {
	let options = "",
		select = "#permissions";

	$.each(
		ACTIONS,
		(key, val) => (options += `<option value="${key}">${val}</option>`)
	);

	if (withItemInAndOut) {
		options += `
			<option value="ITEM_IN">Item In</option>
			<option value="ITEM_OUT">Item Out</option>
		`;
	}

	if (isSelect2Initialized(select)) $(select).select2("destroy");
	$(select).html("").append(options);
	$(select).select2().val("").trigger("change");
}
