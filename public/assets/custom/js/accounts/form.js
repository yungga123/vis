$(document).ready(function () {
	const elems = ["employee_id", "username", "password", "access_level"],
		prefix = "small";

	if (!isEmpty($("#employee_id").data("value"))) {
		$("#employee_id").val($("#employee_id").data("value")).change();
		$("#access_level").val($("#access_level").data("value")).change();
		$("#employee_id").attr("readonly", true);
		$("#small_password").text(
			"Leave it blank if you will not update the password."
		);
	}

	formSubmit($("#form-post-add-account"), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			notifMsgSwal(res.status, message, res.status);
			self[0].reset();
		}

		showAlertInForm(elems, message, res.status, prefix, true);
	});

	formSubmit($("#form-post-edit-account"), "continue", function (res, self) {
		const message = res.errors ?? res.message;
		let msg = " This window will close in <b></b> second/s..";

		if (res.status !== STATUS.ERROR) {
			self[0].reset();

			swalNotifRedirect(res.status, message + msg, res.status, "close", 8000);
		}

		showAlertInForm(elems, message, res.status, prefix, true);
	});
});
