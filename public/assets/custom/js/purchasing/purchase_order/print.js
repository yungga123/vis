$(document).ready(function () {
	const initialValAttentionTo = $("#attention_to").val();
	const initialValWithVat = $("#form_with_vat").is(":checked");
	const initialValVatAmount = $('input[name="vat_amount"]').val();
	const initialValSubTotal = $('input[name="sub_total"]').val();

	$("#form_with_vat").on("change", function () {
		let vat_amount = 0;

		if ($(this).is(":checked")) {
			vat_amount = $("#vat_amount").val();
		}

		$('input[name="vat_amount"]').val(vat_amount);
	});

	$("#print_po_modal").modal("show");

	$("#print_po_form").on("submit", function (e) {
		e.preventDefault();

		const attentionTo = $("#attention_to").val();
		const withVat = $("#form_with_vat").is(":checked");
		const net_of_vat_amount = $('input[name="net_of_vat_amount"]').val();
		const vat_amount = $("#vat_amount_formatted").val();
		const sub_total = $("#sub_total_formatted").val();
		const total_amount = $("#total_amount").val();
		const _vat_amount = $('input[name="vat_amount"]').val();
		const _sub_total = $('input[name="sub_total"]').val();

		$("#attention_to_text").text(attentionTo);
		$("#net_of_vat_amount").html(withVat ? net_of_vat_amount : "0.00");
		$("td div.vat_amount").html(withVat ? vat_amount : "0.00");
		$("td span.total_amount").html(withVat ? total_amount : sub_total);

		if (
			!isEmpty(attentionTo) &&
			attentionTo == initialValAttentionTo &&
			withVat == initialValWithVat &&
			_vat_amount == initialValVatAmount &&
			_sub_total == initialValSubTotal
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
