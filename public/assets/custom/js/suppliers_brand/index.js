
function brand_suppliers() {
    brand_table = "supplier_table_brand";
	brand_modal = "modal_add_supplier_brand";
	brand_form = "form_add_supplier_brand";
	brand_editRoute = $("#edit_url_brand").val();
	brand_removeRoute = $("#remove_url_brand").val();
	brand_elems = [
		"brand_name",
        "product",
        "warranty",
        "sales_person",
        "sales_contact_number",
        "technical_support",
        "technical_contact_number",
        "remarks"
	];

    /* Load dataTable */
	const route = $("#" + brand_table).data("url");
	loadDataTable(brand_table, route, METHOD.POST);

    /* Form for saving item */
	formSubmit($("#" + brand_form), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			self[0].reset();
			refreshDataTable();
			notifMsgSwal(res.status, message, res.status);

			if ($(`#${brand_modal}`).hasClass("edit")) {
				$(`#${brand_modal}`).modal("hide");
			}
		}

		showAlertInForm(brand_elems, message, res.status);
	});

}



/* Get supplier details */
function brand_edit(id) {
	$(`#${brand_modal}`).removeClass("add").addClass("edit");
	$(`#${brand_modal} .modal-title`).text("Edit Brand Supplier");
	$("#supplier_id").val(id);

	clearAlertInForm(elems);
	resetSelected();
	
	showLoading();

	$.post(editRoute, { id: id })
		.then((res) => {
			closeLoading();

			if (res.status === STATUS.SUCCESS) {
				
				if (inObject(res, "data") && !isEmpty(res.data)) {
					$.each(res.data, (key, value) => {
						$(`input[name="${key}"]`).val(value);
						// $(`#${key}`).val(value);
						// if (value == "Others" && key == "supplier_type") {
						// 	selectedOthers(value);
						// }
						// if (value == "Others" && key == "payment_mode") {
						// 	selectedPaymentMode(value);
						// }
						// console.log(key,value);
					});
				}
				// selectedOthers(val);
				// selectedPaymentMode(val);
			} else {
				$(`#${modal}`).modal("hide");
				notifMsgSwal(res.status, res.message, res.status);
			}
		})
		.catch((err) => catchErrMsg(err));
}

function brand_remove(id) {
	const swalMsg = "delete";
	swalNotifConfirm(
		function () {
			$.post(removeRoute, { id: id })
				.then((res) => {
					const message = res.errors ?? res.message;

					refreshDataTable($("#" + branch_table));
					notifMsgSwal(res.status, message, res.status);
				})
				.catch((err) => catchErrMsg(err));
		},
		TITLE.WARNING,
		swalMsg,
		STATUS.WARNING
	);
}