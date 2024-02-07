var _dropzone;

$(document).ready(function () {
	/* Load data */
	initLoadData();

	_dropzoneInit();

	/* Form for saving data */
	formSubmit($("#form_system_info"), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			notifMsgSwal(res.status, message, res.status);
		}

		showAlertInForm(["system_name", "vat_percent"], message, res.status);
	});

	/* Form for saving data */
	formSubmit(
		$("#form_company_logo"),
		"continue",
		function (res, self) {
			const message = res.errors ?? res.message;
			const elems = ["company_logo"];

			if (res.status !== STATUS.ERROR) {
				notifMsgSwal(res.status, message, res.status);
			}

			closeLoading();
			showAlertInForm(elems, message, res.status);
		},
		METHOD.AJAX,
		true
	);

	/* Form for saving data */
	formSubmit($("#form_company_info"), "continue", function (res, self) {
		const message = res.errors ?? res.message;
		const elems = [
			"company_name",
			"company_address",
			"company_contact_number",
			"company_email_address",
			"company_bank_name",
			"company_bank_account_name",
			"company_bank_account_number",
			"company_bank_branch",
		];

		if (res.status !== STATUS.ERROR) {
			notifMsgSwal(res.status, message, res.status);
		}

		showAlertInForm(elems, message, res.status);
	});

	/* Form for saving data */
	formSubmit($("#form_form_codes"), "continue", function (res, self) {
		const message = res.errors ?? res.message;
		const elems = ["purchase_order_form_code", "billing_invoice_form_code"];

		if (res.status !== STATUS.ERROR) {
			notifMsgSwal(res.status, message, res.status);
		}

		showAlertInForm(elems, message, res.status);
	});

	$("#company_logo").on("change", function (e) {
		if (!isEmpty($(this).val())) {
			const preview = $("#preview_logo");
			preview.attr("src", URL.createObjectURL(e.target.files[0]));
			preview.on("load", function () {
				URL.revokeObjectURL(preview.attr("src")); // free memory
			});
		}
	});
});

function initLoadData() {
	showLoading();
	$.get(router.general_info.fetch)
		.then((res) => {
			if (res.status === STATUS.SUCCESS) {
				$.each(res.data, (key, value) => {
					$(`input[name="${value["key"]}"]`).val(value["value"]);
				});
			}
			closeLoading();
		})
		.catch((err) => catchErrMsg(err));
}

/* Dropzone init */
function _dropzoneInit() {
	const form = "form_company_logo";
	const button = "#btn_upload_logo";
	const options = {
		paramName: "company_logo",
		acceptedFiles: ".jpg, .jpeg, .png",
		dictDefaultMessage: "Drop file or click to upload.",
		maxFilesize: 5,
		maxFiles: 1,
		uploadMultiple: false,
		addRemoveLinks: false,
		parallelUploads: 2,
	};

	_dropzone = dropzoneInit(form, null, button, options);
	dzGetFiles(_dropzone, router.general_info.fetch + "?q=" + options.paramName);
}
