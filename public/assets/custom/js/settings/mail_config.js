$(document).ready(function () {
	const form = $("#form_mail_config");

	passwordShowHideInit();

	initBootstrapSwitch("input[data-bootstrap-switch]", {
		onText: "YES",
		offText: "NO",
	});

	$("input[name='is_enable']").on(
		"switchChange.bootstrapSwitch",
		function (event, state) {
			const value = state ? "YES" : "NO";
			$(this).val(value);
		}
	);

	$("input[name='has_mail_notif'], input[name='is_mail_notif_enabled']").on(
		"switchChange.bootstrapSwitch",
		function (event, state) {
			event.preventDefault();

			const name = event.target.name;
			const module_code = event.target.dataset.module_code;
			const has_mail_notif = event.target.dataset.has_mail_notif;

			if (
				name === "is_mail_notif_enabled" &&
				(has_mail_notif == 0 || isEmpty(has_mail_notif))
			) {
				const message =
					"You need to set the <strong>Has Mail Notif?</strong> to YES first!";
				notifMsgSwal(TITLE.ERROR, message, STATUS.WARNING);
				$(this).bootstrapSwitch("state", false);
				return;
			}

			const data = {
				module_code: module_code,
				column: name,
				value: state ? 1 : 0,
			};

			$.post(router.mail_config.save, data)
				.then((res) => {
					const message = res.errors ?? res.message;

					if (!isEmpty(message)) notifMsg(message, res.status);

					if (res.status === STATUS.SUCCESS) {
						if (name === "has_mail_notif") {
							const elem = $("#IS_" + module_code);

							if (!state) elem.bootstrapSwitch("state", false);

							// Set the data-has_mail_notif to state (1 or 0)
							elem[0].dataset.has_mail_notif = state ? 1 : 0;
						}
					}
				})
				.catch((err) => catchErrMsg(err));
		}
	);

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
				"is_enable",
			];

		if (res.status !== STATUS.ERROR) {
			notifMsgSwal(res.status, message, res.status);
		}

		showAlertInForm(elems, message, res.status);
	});
});
