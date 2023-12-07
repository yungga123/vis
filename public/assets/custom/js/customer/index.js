var table, modal, form, elems, _dropzone;

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

	/* Inputmask init */
	const mobile = {
		mask: "0999-999-9999",
		placeholder: "09XX-XXX-XXXX",
	};
	$("#contact_number").inputmask(mobile);
	$("#contact_number2").inputmask(mobile);
	$("#telephone").inputmask({
		mask: "(99) 9999-9999",
		placeholder: "(02) 8XXX-XXXX",
	});

	/* Load dataTable */
	loadDataTable(table, router.customer.list, METHOD.POST);

	$("#btn_add_record").on("click", function () {
		$(`#${modal}`).modal("show");
		$(`#${modal}`).removeClass("edit").addClass("add");
		$(`#${modal} .modal-title`).text("Add New Client");
		$(`#${form}`)[0].reset();
		$("#customer_id").val("");
		$("#unformatted_cn").html("");

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

	/* Dropzone init */
	_dropzoneInit();
});

/* Get record details */
function edit(id) {
	$(`#${modal}`).removeClass("add").addClass("edit");
	$(`#${modal} .modal-title`).text("Edit Client");
	$("#customer_id").val(id);
	$("#unformatted_cn").html("");

	clearAlertInForm(elems);
	showLoading();

	$.post(router.customer.fetch, { id: id })
		.then((res) => {
			closeLoading();

			if (res.status === STATUS.SUCCESS) {
				$(`#${modal}`).modal("show");

				if (inObject(res, "data") && !isEmpty(res.data)) {
					if (res.data.unformatted_cn) {
						$("#unformatted_cn").html(
							"<strong>Previous unformatted contact number:</strong> " +
								res.data.unformatted_cn || res.data.contact_number
						);
					}

					$.each(res.data, (key, value) => $("#" + key).val(value || ""));
				}
			} else {
				notifMsgSwal(res.status, res.message, res.status);
			}
		})
		.catch((err) => catchErrMsg(err));
}

/* Delete record */
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

/* Uploading files */
function upload(id, client) {
	$("#upload_customer_id").val(id);
	$("#upload_modal").modal("show");
	$("#upload_modal .modal-title").html(
		`Upload Files for <strong>${client}</strong>`
	);

	dzGetFiles(_dropzone, router.customer.files.fetch + "/" + id);
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

/* Dropzone init */
function _dropzoneInit() {
	const form = "upload_form";
	const button = "#upload_modal .btn-upload";

	_dropzone = dropzoneInit(form, router.customer.files.upload, button);
	dzOnRemoveFileEvent(_dropzone, router.customer.files.remove);
}
