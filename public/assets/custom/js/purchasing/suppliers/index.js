var table,
	modal,
	form,
	editRoute,
	removeRoute,
	elems,
	branch_table,
	branch_modal,
	branch_form,
	branch_editRoute,
	branch_removeRoute,
	branch_elems;

$(document).ready(function () {
	table = "supplier_table";
	modal = "modal_add_supplier";
	form = "form_add_supplier";
	editRoute = $("#edit_url").val();
	removeRoute = $("#remove_url").val();
	elems = [
		"supplier_name",
		"supplier_type",
		"others_supplier_type",
		"contact_person",
		"contact_number",
		"viber",
		"payment_terms",
		"payment_mode",
		"others_payment_mode",
		"product",
		"email_address",
        "bank_name",
        "bank_number",
		"remarks",
	];

	brand_table = "supplier_table_brand";
	brand_modal = "modal_add_supplier_brand";
	brand_form = "form_add_supplier_brand";
	brand_editRoute = $("#edit_url_brand").val();
	brand_removeRoute = $("#remove_url_brand").val();
	brand_elems = [
		"brand_name",
		"brand_product",
		"brand_warranty",
		"brand_sales_person",
		"brand_sales_contact_number",
		"brand_technical_support",
		"brand_technical_contact_number",
		"brand_remarks",
	];

	$("#btn_add_record").on("click", function () {
		$(`#${modal}`).modal("show");
		$(`#${modal}`).removeClass("edit").addClass("add");
		$(`#${modal} .modal-title`).text("Add New Supplier");
		$(`#${form}`)[0].reset();
		$("#supplier_id").val("");
		clearAlertInForm(elems);
		$("#others_supplier_type")
			.attr("type", "hidden")
			.attr("required", false)
			.val("");
	});

	/* Load dataTable */
	const route = $("#" + table).data("url");
	loadDataTable(table, route, METHOD.POST);

	/* Form for saving item */
	formSubmit($("#" + form), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			self[0].reset();
			refreshDataTable($("#" + table));
			notifMsgSwal(res.status, message, res.status);

			if ($(`#${modal}`).hasClass("edit")) {
				$(`#${modal}`).modal("hide");
			}
		}

		showAlertInForm(elems, message, res.status);
	});

	brand_suppliers();
});

/* Get supplier details */
function edit(id) {
	$(`#${modal}`).removeClass("add").addClass("edit");
	$(`#${modal} .modal-title`).text("Edit Supplier");
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
						$(`#${key}`).val(value);
						if (value == "Others" && key == "supplier_type") {
							selectedOthers(value);
						}
						if (value == "Others" && key == "payment_mode") {
							selectedPaymentMode(value);
						}
					});
				}
				$(`#${modal}`).modal("show");
			} else {
				$(`#${modal}`).modal("hide");
				notifMsgSwal(res.status, res.message, res.status);
			}
		})
		.catch((err) => catchErrMsg(err));
}

function remove(id) {
	const swalMsg = "delete";
	swalNotifConfirm(
		function () {
			$.post(removeRoute, { id: id })
				.then((res) => {
					const message = res.errors ?? res.message;

					refreshDataTable($("#" + table));
					notifMsgSwal(res.status, message, res.status);
				})
				.catch((err) => catchErrMsg(err));
		},
		TITLE.WARNING,
		swalMsg,
		STATUS.WARNING
	);
}

function selectedOthers(val) {
	if (val == "Others") {
		$("#others_supplier_type").attr("type", "text").attr("required", true);
	} else {
		$("#others_supplier_type")
			.attr("type", "hidden")
			.attr("required", false)
			.val("");
	}
}

function selectedPaymentMode(val) {
	if (val == "Others") {
		$("#others_payment_mode").attr("type", "text").attr("required", true);
	} else {
		$("#others_payment_mode")
			.attr("type", "hidden")
			.attr("required", false)
			.val("");
	}
}

function resetSelected() {
	$("#others_supplier_type")
		.attr("type", "hidden")
		.attr("required", false)
		.val("");
	$("#others_payment_mode")
		.attr("type", "hidden")
		.attr("required", false)
		.val("");
}
