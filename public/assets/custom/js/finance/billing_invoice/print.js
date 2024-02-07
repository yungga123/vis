$(document).ready(function () {
	const initialValAttentionTo = $("#attention_to").val();
	const initialValWithVat = $("#form_with_vat").is(":checked");

	$("#print_billing_modal").modal("show");

	$("#print_billing_form").on("submit", function (e) {
		e.preventDefault();

		const attentionTo = $("#attention_to").val();
		const withVat = $("#form_with_vat").is(":checked");

		if (
			!isEmpty(attentionTo) &&
			attentionTo == initialValAttentionTo &&
			withVat == initialValWithVat
		) {
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
					let vat_percent = parseFloat($('input[name="vat_percent"]').val());
					let vat_amount = parseFloat($('input[name="vat_amount"]').val());
					let subtotal_amount = parseFloat(
						$('input[name="subtotal_amount"]').val()
					);
					let total_amount = subtotal_amount;

					vat_amount = parseFloat(withVat ? subtotal_amount * vat_percent : 0);
					total_amount = parseFloat(subtotal_amount + vat_amount);

					$("#attention_to_text").text(attentionTo);
					$("td.vat_amount span").html(numberFormat(vat_amount));
					$("td.total_amount span").html(numberFormat(total_amount));
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
