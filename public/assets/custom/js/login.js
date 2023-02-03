$(document).ready(function () {
	$("#form_login").submit(function (e) {
		e.preventDefault();

		const self = $(this);
		const route = self.attr("action");
		const data = self.serialize();

		showLoading();

		$.post(route, data)
			.then((res) => {
				let message = res.errors ?? res.message;

				if (res.status === STATUS.SUCCESS) {
					const msg = message + " You will be redirected in <b></b> second/s..";

					swalNotifRedirect(res.status, msg, res.status, "/dashboard");
					$(".btn-login").attr("disabled", true);
				} else notifMsgSwal(res.status, message, res.status);

				closeLoading();
			})
			.catch((err) => catchErrMsg(err));
	});
});
