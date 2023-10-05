var branch_table,
	branch_modal,
	branch_table_modal,
	branch_form,
	branch_elems,
	client_id,
	client_name;

$(document).ready(function () {
	branch_table = "customer_branch_table";
	branch_table_modal = "customer_branch_table_modal";
	branch_modal = "customer_branch_modal";
	branch_form = "customer_branch_form";
	branch_elems = [
		"bcustomer_id",
		"bbranch_name",
		"bprovince",
		"bcity",
		"bbarangay",
		"bsubdivision",
		"bcontact_number",
		"bcontact_person",
		"bemail_address",
		"bnotes",
	];

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

			if ($(`#${branch_modal}`).hasClass("edit") || client_id) {
				$(`#${branch_modal}`).modal("hide");
				$("#" + branch_table_modal).modal("show");
				refreshDataTable($("#" + branch_table));
			}
		}

		showAlertInForm(branch_elems, message, res.status);
	});

	$("#" + branch_modal + " .modal-footer .btn-close").on("click", function () {
		if (client_id) $("#" + branch_table_modal).modal("show");
	});

	$("#btn_add_branch").on("click", function () {
		addBranch(client_id, client_name);
		$("#" + branch_table_modal).modal("hide");
	});
});

function branchList(id, client) {
	client_id = id;
	client_name = client;
	const routeList = router.customer.branch.list + "?c=" + id;

	loadDataTable(branch_table, routeList, METHOD.POST, null, true);
	$("#" + branch_table_modal).modal("show");
	$("#" + branch_table_modal + " .modal-title").text(client + "'s Branches");
}

// Used in Select Customers from ADD BRANCH modal
function addBranch(id, client) {
	if (!$(`#${branch_table_modal}`).hasClass("show")) {
		client_id = null;
		client_name = null;
	}

	$("#bcustomer_id").val(id);
	$("#bcustomer_name").val(client);
	$("#branch_id").val("");

	$(`#${branch_modal}`).modal("show");
	$(`#${branch_modal}`).removeClass("edit").addClass("add");
	$(`#${branch_modal} .modal-title`).text("Add Client Branch");

	clearAlertInForm(branch_elems);
}

/* Get record details */
function editBranch(id) {
	client_id = id;
	$("#" + branch_table_modal).modal("hide");
	$(`#${branch_modal}`).removeClass("add").addClass("edit");
	$(`#${branch_modal} .modal-title`).text("Edit Client Branch");
	$("#branch_id").val(id);

	clearAlertInForm(branch_elems);
	showLoading();

	$.post(router.customer.branch.fetch, { id: id })
		.then((res) => {
			closeLoading();

			if (res.status === STATUS.SUCCESS) {
				$(`#${branch_modal}`).modal("show");

				if (inObject(res, "data") && !isEmpty(res.data)) {
					$.each(res.data, (key, value) => {
						$(`input[name="${key}"]`).val(value);
					});
				}

				$("#bcustomer_name").val(client_name);
			} else {
				notifMsgSwal(res.status, res.message, res.status);
			}
		})
		.catch((err) => catchErrMsg(err));
}

function removeBranch(id) {
	const swalMsg = "delete";

	swalNotifConfirm(
		function () {
			$.post(router.customer.branch.delete, { id: id })
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
