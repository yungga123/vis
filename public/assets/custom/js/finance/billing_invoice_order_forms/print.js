$(document).ready(function () {
	const initialValAttentionTo = $("#attention_to").val();

	$("#print_billing_modal").modal("show");

	$("#print_billing_form").on("submit", function (e) {
		e.preventDefault();

		const attentionTo = $("#attention_to").val();

		if (!isEmpty(attentionTo) && attentionTo == initialValAttentionTo) {
			$("#print_billing_modal").modal("hide");
			toggleWindowPrint();
			return;
		}

		// Submit form
		const self = $(this);
		const route = self.attr("action");
		const data = self.serialize();

		showLoading();

		$.post(route, data)
			.then((res) => {
				const message = res.errors ?? res.message;

				if (res.status !== STATUS.ERROR) {
					$("#attention_to_text").text(attentionTo);
					$("#print_billing_modal").modal("hide");

					toggleWindowPrint();
				}

				showAlertInForm(["attention_to"], message, res.status);
				closeLoading();
			})
			.catch((err) => catchErrMsg(err));
	});
});

function toggleWindowPrint() {
	window.setTimeout(function () {
		window.addEventListener("load", window.print());
	}, 500);
}
