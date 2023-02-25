$(document).ready(function () {
	const form = $("#form_account");

	formSubmit(form, "continue", function (res, self) {
		const message = res.errors ?? res.message;
		const elems = ["current_password", "password", "confirm_password"];

		if (res.status === STATUS.SUCCESS) {
			self[0].reset();

			swalNotifRedirect(res.status, message, res.status, "/logout");
		}

		closeLoading();
		showAlertInForm(elems, message, res.status);
	});
});
