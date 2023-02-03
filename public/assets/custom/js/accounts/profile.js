$(document).ready(function () {
	const form = $("#form_account");

	form.on("submit", function (e) {
		e.preventDefault();

		if (confirm("Do you really want to continue?")) {
			const self = $(this);
			const route = self.attr("action");
			const data = self.serialize();

			showLoading();

			$.post(route, data)
				.then((res) => {
					const message = res.errors ?? res.message;
					const elems = ["current_password", "password", "confirm_password"];

					if (res.status === STATUS.SUCCESS) {
						self[0].reset();

						swalNotifRedirect(res.status, message, res.status, "/logout");
					}

					closeLoading();
					showAlertInForm(elems, message, res.status);
				})
				.catch((err) => catchErrMsg(err));
		}
	});

	const swalMsg = "delete";
	swalNotifConfirm(removeRecord, TITLE.WARNING, swalMsg, STATUS.WARNING);
});
