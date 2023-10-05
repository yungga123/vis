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

	$("#filterby").on("change", function () {
		const options = {
			params: { filter: $(this).val() },
		};
		loadDataTable(table, route, METHOD.POST, options, true);
	});

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
	let new_client = $("#filter_new_client").val();
	let type = $("#filter_type").val();
	let source = getSelect2Selection("#filter_source");

	showLoading();
	let options = {
		params: {
			new_client: new_client,
			type: type,
			source: source,
		},
	};

	if (reset) {
		options.params = null;
		setOptionValue("#filter_new_client");
		setOptionValue("#filter_type");
		clearSelect2Selection("#filter_source");
	}

	loadDataTable(table, router.customer.list, METHOD.POST, options, true);
	closeLoading();
}
