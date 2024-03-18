var table, modal, form, elems, clientSelector;

$(document).ready(function () {
	table = "customer_support_table";
	modal = "customer_support_modal";
	form = "customer_support_form";
	elems = [
		"customer_id",
		"customer_branch_id",
		"ticket_number",
		"issue",
		"findings",
		"action",
		"troubleshooting",
		"security_ict_system",
		"security_ict_system_other",
		"priority",
		"due_date",
		"follow_up_date",
		"remarks",
	];
	clientSelector = "#customer_id";

	select2Init("#filter_status");
	select2Init("#filter_security_ict_system");
	select2Init("#filter_priority");

	/* Load dataTable */
	loadDataTable(table, router.customer_support.list, METHOD.POST);

	$("#btn_add_record").on("click", function () {
		$(`#${modal}`).modal("show");
		$(`#${modal}`).removeClass("edit").addClass("add");
		$(`#${modal} .modal-title`).text("Add a Record");

		_clearForm();
	});

	/* Initial init of customers (commerical) via ajax data source */
	initSelect2Customers(router.clients.common.customers);
	onChangeCustomerType();
	onSelectCustomer(clientSelector);
	onClearCustomer(clientSelector);
	initSelect2CustomerBranches(router.clients.common.customer_branches);

	/* Init specialist/s (employee) list */
	_initSelect2Specialists();

	$("#security_ict_system").on("change", function () {
		$("div.security_ict_system_other").addClass("d-none");

		if ($(this).val() === "OTHER") {
			$("div.security_ict_system_other").removeClass("d-none");
		}
	});

	/* Form for saving record */
	formSubmit($("#" + form), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			refreshDataTable($("#" + table));
			notifMsgSwal(res.status, res.message, res.status);
			_clearForm();

			if ($(`#${modal}`).hasClass("edit")) {
				$(`#${modal}`).modal("hide");
			}
		}

		if (res.errors) notifMsg(res.message, res.status);

		showAlertInForm(elems, message, res.status);
	});

	/* Form for changing record */
	formSubmit(
		$("#customer_support_change_form"),
		"continue",
		function (res, self) {
			const message = res.errors ?? res.message;

			if (res.status !== STATUS.ERROR) {
				refreshDataTable($("#" + table));
				notifMsgSwal(res.status, res.message, res.status);

				$("#_id").val("");
				$("#_remarks").val("");
				$("#customer_support_change_modal").modal("hide");
			}

			if (res.errors) notifMsg(res.message, res.status);

			showAlertInForm(elems, message, res.status);
		}
	);
});

/* For filtering and reseting */
function filterData(reset = false) {
	const status = getSelect2Selection("#filter_status");
	const security_ict_system = getSelect2Selection(
		"#filter_security_ict_system"
	);
	const priority = getSelect2Selection("#filter_priority");
	const params = {
		status: status,
		security_ict_system: security_ict_system,
		priority: priority,
		payment_method: payment_method,
	};
	const condition =
		!isEmpty(status) || !isEmpty(security_ict_system) || !isEmpty(priority);

	filterParam(
		router.customer_support.list,
		table,
		params,
		condition,
		() => {
			clearSelect2Selection("#filter_status");
			clearSelect2Selection("#filter_security_ict_system");
			clearSelect2Selection("#filter_priority");
		},
		reset
	);
}

/* Get record details */
function edit(id) {
	$(`#${modal}`).removeClass("add").addClass("edit");
	$(`#${modal} .modal-title`).text("Edit Record");

	_clearForm();

	fetchRecord(router.customer_support.fetch, { id: id }, modal, (res) => {
		if (res.status === STATUS.SUCCESS) {
			if (inObject(res, "data") && !isEmpty(res.data)) {
				setSelect2AjaxSelection(
					clientSelector,
					res.data.client_name,
					res.data.customer_id
				);
				initSelect2Customers(
					router.clients.common.customers,
					strLower(res.data.customer_type)
				);

				$("#id").val(id);
				$("#" + strLower(res.data.customer_type)).prop("checked", true);
				$("#client_branch_wrapper").addClass("d-none");

				if (strLower(res.data.customer_type) === "commercial") {
					setTimeout(() => {
						setSelect2Selection(
							"#customer_branch_id",
							res.data.customer_branch_id
						);
					}, 500);

					$("#client_branch_wrapper").removeClass("d-none");
				}

				setOptionValue("#security_ict_system", res.data.security_ict_system);
				setOptionValue("#priority", res.data.priority);
				_setSelect2Specialists(res.data.specialist_ids, res.data.specialists);

				$("#ticket_number").val(res.data.ticket_number);
				$("#security_ict_system_other").val(res.data.security_ict_system_other);
				$("#due_date").val(res.data.due_date);
				$("#follow_up_date").val(res.data.follow_up_date);
				$("#issue").val(res.data.issue);
				$("#findings").val(res.data.findings);
				$("#action").val(res.data.action);
				$("#troubleshooting").val(res.data.troubleshooting);
				$("#remarks").val(res.data.remarks);
				$("#security_ict_system").trigger("change");

				$(`#${modal}`).modal("show");
			}
		}
	});
}

/* Delete record */
function remove(id) {
	deleteRecord(router.customer_support.delete, { id: id }, table);
}

/* Change status record */
function change(id, status) {
	console.log(id, status);

	const modal = "customer_support_change_modal";
	const data = { id: id, status: status };

	fetchRecord(router.customer_support.fetch, data, modal, (res) => {
		setOptionValue("#status", status);

		$("#_id").val(id);
		$("#_remarks").val(res.data.remarks || "");
		$('#status option[value="' + status + '"]').attr("disabled", true);
		$("#" + modal).modal("show");
	});
}

/* Init select2 specialists */
function _initSelect2Specialists() {
	select2AjaxInit(
		"#specialists",
		"Select an specialists",
		router.employee.common.search,
		"text"
	);
}

/* Reset form */
function _clearForm() {
	$(`#${form}`)[0].reset();
	$("#id").val("");
	$("#client_branch_wrapper").addClass("d-none");
	$("div.security_ict_system_other").addClass("d-none");

	clearSelect2Selection(clientSelector);
	clearSelect2Selection("#customer_branch_id");
	clearSelect2Selection("#specialists");
	clearAlertInForm(elems);
}

/* Set select2 specialists */
function _setSelect2Specialists(specialist_ids, specialists) {
	if (!isEmpty(specialist_ids)) {
		_initSelect2Specialists();

		$.each(specialist_ids, (key, val) =>
			setSelect2AjaxSelection("#specialists", specialists[key], val)
		);
	}
}
