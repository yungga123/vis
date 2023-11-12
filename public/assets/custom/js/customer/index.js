var table, modal, form, elems;

$(document).ready(function () {
	table = "customer_table";
	modal = "customer_modal";
	form = "customer_form";
	elems = [
		"name",
		"province",
		"city",
		"barangay",
		"subdivision",
		"contact_person",
		"contact_number",
		"email_address",
		"type",
		"forecast",
		"source",
		"notes",
		"referred_by",
	];

	select2Init("#filter_source");

	/* Load dataTable */
	loadDataTable(table, router.customer.list, METHOD.POST);

	$("#btn_add_record").on("click", function () {
		$(`#${modal}`).modal("show");
		$(`#${modal}`).removeClass("edit").addClass("add");
		$(`#${modal} .modal-title`).text("Add New Client");
		$(`#${form}`)[0].reset();
		$("#customer_id").val("");

		clearAlertInForm(elems);
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

		showAlertInForm(elems, message, res.status);
	});
});

/* Get record details */
function edit(id) {
	$(`#${modal}`).removeClass("add").addClass("edit");
	$(`#${modal} .modal-title`).text("Edit Client");
	$("#customer_id").val(id);

	clearAlertInForm(elems);
	showLoading();

	$.post(router.customer.fetch, { id: id })
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
			$.post(router.customer.delete, { id: id })
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

/* For filtering and reseting */
function filterData(reset = false) {
	const new_client = $("#filter_new_client").val();
	const type = $("#filter_type").val();
	const source = getSelect2Selection("#filter_source");
	const params = {
		new_client: new_client,
		type: type,
		source: source,
	};
	const condition = !isEmpty(new_client) || !isEmpty(type) || !isEmpty(source);

	filterParam(
		router.customer.list,
		table,
		params,
		condition,
		() => {
			setOptionValue("#filter_new_client");
			setOptionValue("#filter_type");
			clearSelect2Selection("#filter_source");
		},
		reset
	);
}
