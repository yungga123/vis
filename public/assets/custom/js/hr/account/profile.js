var _dropzone;

$(document).ready(function () {
	const form = $("#form_account");

	passwordShowHideInit("current_password", "show_password");
	passwordShowHideInit("password", "show_password1");
	passwordShowHideInit("confirm_password", "show_password2");

	formSubmit(form, "continue", function (res, self) {
		const message = res.errors ?? res.message;
		const elems = ["current_password", "password", "confirm_password"];

		if (res.status !== STATUS.ERROR) {
			self[0].reset();

			swalNotifRedirect(res.status, message, res.status, "/logout");
		}

		closeLoading();
		showAlertInForm(elems, message, res.status);
	});

	// Image upload using this
	_dropzoneInit();
});

/* Dropzone init */
function _dropzoneInit() {
	const form = "form_profile_img";
	const button = "#modal_profile_img .btn-upload";
	const options = {
		paramName: "profile_img",
		acceptedFiles: ".jpg, .jpeg, .png",
		dictDefaultMessage: "Drop file or click to upload.",
		maxFilesize: 5,
		maxFiles: 1,
		uploadMultiple: false,
		addRemoveLinks: false,
		parallelUploads: 2,
	};

	_dropzone = dropzoneInit(form, null, button, options);
	dzOnSuccessEvent(_dropzone, button, _dzOnSuccessEvent);
}

// Dropzone on success event custom callback
function _dzOnSuccessEvent(file, response) {
	const message = response.errors ?? response.message;
	if (response.status !== STATUS.ERROR)
		swalNotifRedirect(response.status, message, response.status, "reload");
	else notifMsgSwal(response.status, message, response.status);
}
