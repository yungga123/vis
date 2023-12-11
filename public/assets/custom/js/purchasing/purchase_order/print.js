$(document).ready(function () {
	const initialValAttentionTo = $("#attention_to").val();
	const initialValWithVat = $("#form_with_vat").is(":checked");

	$("#print_po_modal").modal("show");

	$("#print_po_form").on("submit", function (e) {
		e.preventDefault();

		const attentionTo = $("#attention_to").val();
		const withVat = $("#form_with_vat").is(":checked");

		if (
			!isEmpty(attentionTo) &&
			attentionTo == initialValAttentionTo &&
			withVat == initialValWithVat
		) {
			$("#print_po_modal").modal("hide");
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
					const net_of_vat_amount = $('input[name="net_of_vat_amount"]').val();
					const vat_amount = $('input[name="vat_amount"]').val();

					$("#attention_to_text").text(attentionTo);
					$("#net_of_vat_amount").html(withVat ? net_of_vat_amount : "0.00");
					$("#vat_amount").html(withVat ? vat_amount : "0.00");
					$("#print_po_modal").modal("hide");

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
