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
	table = "customer_table";
	modal = "modal_customer";
	form = "form_customer";
	editRoute = $("#edit_url").val();
	removeRoute = $("#remove_url").val();
	prefix = "small";
	elems = [
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
	];

	branch_table = "customerbranch_table";
	branch_modal = "modal_branchcustomer";
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

	let route = $("#" + table).data("url");
	loadDataTable(table, route, METHOD.POST);

	$("#btn_add_record").on("click", function () {
		$(`#${modal}`).modal("show");
		$(`#${modal}`).removeClass("edit").addClass("add");
		$(`#${modal} .modal-title`).text("Add New Customer");
		$(`#${form}`)[0].reset();
		$("#customervt_id").val("");

		clearAlertInForm(elems, null, prefix);
	});

	/* Form for saving customer */
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

		showAlertInForm(elems, message, res.status, prefix);
	});

	/* Form for saving branch customer */
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

		showAlertInForm(branch_elems, message, res.status, prefix);
	});
});

/* Get record details */
function edit(id) {
	$(`#${modal}`).removeClass("add").addClass("edit");
	$(`#${modal} .modal-title`).text("Edit Customer");
	$("#customers_id").val(id);

	clearAlertInForm(elems);
	showLoading();

	$.post(editRoute, { id: id })
		.then((res) => {
			closeLoading();

			if (res.status === STATUS.SUCCESS) {
				$(`#${modal}`).modal("show");

				if (inObject(res, "data") && !isEmpty(res.data)) {
					$.each(res.data, (key, value) => {
						$(`input[name="${key}"]`).val(value);
					});
				}
			} else {
				notifMsgSwal(res.status, res.message, res.status, prefix);
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

function branchCustomerRetrieve(id) {
	$("#modal-customer-branch").modal("show");

	let route = $("#" + branch_table).data("url") + "?customers_id=" + id;
	loadDataTable(branch_table, route, METHOD.GET, null, true);
	// refreshDataTable($("#" + table));
}

// Used in Select Customers from ADD BRANCH modal
function addBranch(id, name) {
	$("#bcustomer_id").val(id);
	$("#bcustomer_name").val(name);
	$("#branch_id").val("");

	$(`#${branch_modal}`).modal("show");
	$(`#${branch_modal}`).removeClass("edit").addClass("add");
	$(`#${branch_modal} .modal-title`).text("Add Customer Branch");

	clearAlertInForm(branch_elems, null, prefix);
}

/* Get record details */
function editBranch(id) {
	let editRoute = $("#editBranch_url").val();

	$(`#${branch_modal}`).removeClass("add").addClass("edit");
	$(`#${branch_modal} .modal-title`).text("Edit Customer Branch");
	$("#branch_id").val(id);

	clearAlertInForm(branch_elems, null, prefix);
	showLoading();

	$.post(editRoute, { id: id })
		.then((res) => {
			closeLoading();

			if (res.status === STATUS.SUCCESS) {
				if (inObject(res, "data") && !isEmpty(res.data)) {
					$.each(res.data, (key, value) => {
						$(`input[name="${key}"]`).val(value);
					});

					$("#bcustomer_name").val(res.data.customer_name);
				}
			} else {
				$(`#${branch_modal}`).modal("hide");
				notifMsgSwal(res.status, res.message, res.status);
			}
		})
		.catch((err) => catchErrMsg(err));
}

function removeBranch(id) {
	const swalMsg = "delete";
	let removeRoute = $("#removeBranch_url").val();

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
