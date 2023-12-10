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
			const currValue = $(this).val();
			const mail_config_id = event.target.dataset.mail_config_id;
			const data = {
				mail_config_id: mail_config_id,
				is_enable: value,
			};

			if (currValue !== value) {
				$.post(router.mail_config.save, data)
					.then((res) => {
						const message = res.errors ?? res.message;

						if (!isEmpty(res.message)) notifMsg(message, res.status);
						if (res.status === STATUS.ERROR) {
							$(this).bootstrapSwitch("state", !state);
						} else $(this).val(value);
					})
					.catch((err) => catchErrMsg(err));
			}
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

function save(module_code, has_mail, is_enabled) {
	const row = "#ROW_" + module_code;
	const recipients = $(row + ' textarea[name="recipients"]');
	const hasMailElem = $(row + ' input[name="has_mail_notif"]');
	const isEnabledElem = $(row + ' input[name="is_mail_notif_enabled"]');
	const recipAlertElem = $(row + " small.alert-recipients");

	if (hasMailElem.length) has_mail = hasMailElem.is(":checked");
	if (isEnabledElem.length) is_enabled = isEnabledElem.is(":checked");

	has_mail = has_mail ? 1 : 0;
	is_enabled = is_enabled ? 1 : 0;

	if (has_mail == 0 && isEnabledElem.is(":checked")) {
		const message =
			"You need to set the <strong>Has Mail Notif?</strong> to YES first!";
		notifMsgSwal(TITLE.ERROR, message, STATUS.WARNING);
		isEnabledElem.bootstrapSwitch("state", false);
		return;
	}

	hasMailElem.val(has_mail);
	isEnabledElem.val(is_enabled);
	recipAlertElem.text("");

	const data = {
		module_code: module_code,
		has_mail_notif: has_mail,
		is_mail_notif_enabled: is_enabled,
		cc_recipients: recipients.val(),
	};

	$.post(router.mail_config.save, data)
		.then((res) => {
			const message = res.errors ?? res.message;

			if (!isEmpty(res.message)) notifMsg(res.message, res.status);
			if (res.errors) {
				$(row + " small.alert-recipients").text(res.errors.recipients);
			}
			if (res.recipients) recipients.val(res.recipients);
		})
		.catch((err) => catchErrMsg(err));
}
