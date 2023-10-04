var table,
	modal,
	form,
	elems,
	prefix,
	editRoute,
	removeRoute,
	branch_table,
	branch_modal,
	branch_form,
	branch_elems;

$(document).ready(function () {
	table = "customervt_table";
	modal = "modal_customervt";
	form = "form_customervt";
	editRoute = $("#edit_url").val();
	removeRoute = $("#remove_url").val();
	elems = [
		"forecast",
		"customer_name",
		"contact_person",
		"address_province",
		"address_city",
		"address_brgy",
		"address_sub",
		"contact_number",
		"email_address",
		"source",
		"notes",
		"referred_by",
		"customer_type"
	];

	branch_table = "customervtbranch_table";
	branch_modal = "modal_branchcustomervt";
	branch_form = "form_branchcustomervt";
	branch_elems = [
		"bcustomer_id",
		"bbranch_name",
		"baddress_province",
		"baddress_city",
		"baddress_brgy",
		"baddress_sub",
		"bcontact_number",
		"bcontact_person",
		"bemail_address",
		"bnotes",
	];

	/* Load dataTable */
	const route = $("#" + table).data("url");
	$("#filterby").on("change", function () {
		const options = {
			params: { filter: $(this).val() },
		};
		loadDataTable(table, route, METHOD.POST, options, true);
	});
	loadDataTable(table, route, METHOD.POST);

	$("#btn_add_record").on("click", function () {
		$(`#${modal}`).modal("show");
		$(`#${modal}`).removeClass("edit").addClass("add");
		$(`#${modal} .modal-title`).text("Add New Client");
		$(`#${form}`)[0].reset();
		$("#customervt_id").val("");

		clearAlertInForm(elems);
	});

	/* Form for saving customervt */
	formSubmit($("#" + form), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			self[0].reset();
			refreshDataTable($("#" + table));
			notifMsgSwal(res.status, message, res.status);

			if ($(`#${modal}`).hasClass("edit")) {
				$(`#${modal}`).modal("hide");
			}
		}

		showAlertInForm(elems, message, res.status, (prefix = "small"));
	});

	/* Form for saving branch */
	formSubmit($("#" + branch_form), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			const _bid = $("#bcustomer_id").val(),
				_bname = $("#bcustomer_name").val();

			notifMsgSwal(res.status, message, res.status);
			self[0].reset();

			$("#branch_id").val("");
			$("#bcustomer_id").val(_bid);
			$("#bcustomer_name").val(_bname);

			if ($(`#${branch_modal}`).hasClass("edit")) {
				$(`#${branch_modal}`).modal("hide");
				refreshDataTable($("#" + branch_table));
			}
		}

		showAlertInForm(branch_elems, message, res.status);
	});
});

/* Get record details */
function edit(id) {
	$(`#${modal}`).removeClass("add").addClass("edit");
	$(`#${modal} .modal-title`).text("Edit Client");
	$("#customervt_id").val(id);

	clearAlertInForm(elems);
	showLoading();

	$.post(editRoute, { id: id })
		.then((res) => {
			closeLoading();

			if (res.status === STATUS.SUCCESS) {
				$(`#${modal}`).modal("show");

				if (inObject(res, "data") && !isEmpty(res.data)) {
					$.each(res.data, (key, value) => {
						$("#" + key).val(value);
					});
				}
			} else {
				notifMsgSwal(res.status, res.message, res.status);
			}
		})
		.catch((err) => catchErrMsg(err));
}

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

function branchCustomervtRetrieve(id) {
	const route = $("#" + branch_table).data("url") + "?customervt_id=" + id;

	$("#modal-customer-branch").modal("show");
	loadDataTable(branch_table, route, METHOD.GET, null, true);
}

// Used in Select Customers from ADD BRANCH modal
function addBranch(id, name) {
	$("#bcustomer_id").val(id);
	$("#bcustomer_name").val(name);
	$("#branch_id").val("");

	$(`#${branch_modal}`).modal("show");
	$(`#${branch_modal}`).removeClass("edit").addClass("add");
	$(`#${branch_modal} .modal-title`).text("Add Client Branch");

	clearAlertInForm(branch_elems);
}

/* Get record details */
function editBranch(id) {
	const editRoute = $("#editBranch_url").val();

	$(`#${branch_modal}`).removeClass("add").addClass("edit");
	$(`#${branch_modal} .modal-title`).text("Edit Client Branch");
	$("#branch_id").val(id);

	clearAlertInForm(branch_elems);
	showLoading();

	$.post(editRoute, { id: id })
		.then((res) => {
			closeLoading();

			if (res.status === STATUS.SUCCESS) {
				$(`#${branch_modal}`).modal("show");

				if (inObject(res, "data") && !isEmpty(res.data)) {
					$.each(res.data, (key, value) => {
						$(`input[name="${key}"]`).val(value);
					});
				}

				$("#bcustomer_name").val(res.data.customer_name);
			} else {
				notifMsgSwal(res.status, res.message, res.status);
			}
		})
		.catch((err) => catchErrMsg(err));
}

function removeBranch(id) {
	const swalMsg = "delete",
		removeRoute = $("#removeBranch_url").val();

	swalNotifConfirm(
		function () {
			$.post(removeRoute, { id: id })
				.then((res) => {
					const message = res.errors ?? res.message;

					refreshDataTable($("#" + branch_table));
					notifMsgSwal(res.status, message, res.status);
				})
				.catch((err) => catchErrMsg(err));
		},
		TITLE.WARNING,
		swalMsg,
		STATUS.WARNING
	);
}
