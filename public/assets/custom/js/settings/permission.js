var table, modal, form, elems, $actions, $permission, $initialPermissions;

$(document).ready(function () {
	table = "permission_table";
	modal = "permission_modal";
	form = "permission_form";
	editRoute = $("#edit_url").val();
	removeRoute = $("#remove_url").val();
	elems = ["role_code", "module_code", "permissions"];
	$actions = $pjOptions.actions;
	$permission = "#permissions";
	$initialPermissions = initialPermissions();

	$("#btn_add_record").on("click", function () {
		$(`#${modal}`).modal("show");
		$(`#${modal}`).removeClass("edit").addClass("add");
		$(`#${modal} .modal-title`).text("Add Permission");
		$(`#${form}`)[0].reset();
		$("#permission_id").val("");
		$("#permissions").val("").change();

		clearAlertInForm(elems);
	});

	select2Init($permission, "Select Permissions", $initialPermissions);

	/* Init filter */
	select2Init("#filter_role_code");
	select2Init("#filter_module_code");

	$("#module_code").on("change", function () {
		otherPermissions($(this).val());
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

/* For filtering and reseting */
function filterData(reset = false) {
	const role_code = getSelect2Selection("#filter_role_code");
	const module_code = getSelect2Selection("#filter_module_code");
	const params = {
		role_code: role_code,
		module_code: module_code,
	};
	const condition = !isEmpty(role_code) || !isEmpty(module_code);

	filterParam(
		router.permission.list,
		table,
		params,
		condition,
		() => {
			clearSelect2Selection("#filter_role_code");
			clearSelect2Selection("#filter_module_code");
		},
		reset
	);
}

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
					setSelect2Selection($permission, res.data.permissions);
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

/* Populate initial permissions options */
function initialPermissions() {
	const options = $.map($actions, (action, key) => {
		// Not include the OTHERS options yet
		if (key !== "OTHERS") return { id: key, text: action };
	});

	return options;
}

/* Check and populate other permissions options */
function otherPermissions(val) {
	let data = $initialPermissions;

	if (val && inObject($actions.OTHERS, val)) {
		const _actions = $actions.OTHERS[val];
		const options = $.map(_actions, (text, key) => {
			// Include the OTHERS options
			if (key !== "OTHERS_ONLY") return { id: key, text: text };
		});

		data = options;

		if (!inObject(_actions, "OTHERS_ONLY")) {
			data = $initialPermissions.concat(options);
		}
	}

	select2Reinit($permission, "Select Permissions", data);
}
