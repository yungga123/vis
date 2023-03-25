$(document).ready(function () {
	const form = $("#form_mail_config");

	passwordShowHideInit();

	$("#btn_getAccessToken").on("click", function () {
		if (
			isEmpty($("#oauth_client_id").val()) ||
			isEmpty($("#oauth_client_secret").val()) ||
			isEmpty($("#oauth_scope").val()) ||
			isEmpty($("#redirect_uri").val())
		) {
			notifMsgSwal(
				TITLE.ERROR,
				'OAuth2 Google Client fields are required except the "Access Type"!',
				STATUS.ERROR
			);

			return false;
		}

		const href = $(this).attr("href");
		const swalMsg = "continue";
		swalNotifConfirm(
			function () {
				const msg = "You will be redirected now in <b></b> second/s...";
				swalNotifRedirect(TITLE.INFO, msg, STATUS.INFO, href);
			},
			TITLE.CONFIRM,
			swalMsg,
			STATUS.QUESTION
		);
		// if (confirm("Do you really want to continue?")) return true;

		return false;
	});

	formSubmit(form, "continue", function (res, self) {
		const message = res.errors ?? res.message,
			elems = [
				"email_name",
				"email",
				"password",
				"oauth_client_id",
				"oauth_client_secret",
				"oauth_scope",
				"redirect_uri",
				"hostname",
				"access_type",
				"recepients",
			];

		if (res.status !== STATUS.ERROR) {
			notifMsgSwal(res.status, message, res.status);
		}

		showAlertInForm(elems, message, res.status);
	});
});
