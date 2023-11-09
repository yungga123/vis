var table,
	modal,
	form,
	elems,
	brand_table,
	brand_modal,
	brand_form,
	brand_elems;

$(document).ready(function () {
	table = "supplier_table";
	modal = "modal_add_supplier";
	form = "form_add_supplier";
	elems = [
		"supplier_name",
		"supplier_type",
		"others_supplier_type",
		"address",
		"contact_person",
		"contact_number",
		"viber",
		"payment_terms",
		"payment_mode",
		"others_payment_mode",
		"product",
		"email_address",
		"bank_name",
		"bank_account_name",
		"bank_number",
		"remarks",
	];

	brand_table = "supplier_table_brand";
	brand_modal = "modal_add_supplier_brand";
	brand_form = "form_add_supplier_brand";
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
	loadDataTable(table, router.supplier.list, METHOD.POST);

	/* Filters */
	select2Init("#filter_supplier_type");
	select2Init("#filter_payment_terms");
	select2Init("#filter_payment_mode");

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

/* For filtering and reseting */
function filterData(reset = false) {
	const supplier_type = getSelect2Selection("#filter_supplier_type");
	const payment_terms = getSelect2Selection("#filter_payment_terms");
	const payment_mode = getSelect2Selection("#filter_payment_mode");
	const params = {
		supplier_type: supplier_type,
		payment_terms: payment_terms,
		payment_mode: payment_mode,
	};
	const condition =
		!isEmpty(supplier_type) ||
		!isEmpty(payment_terms) ||
		!isEmpty(payment_mode);

	filterParam(
		router.supplier.list,
		table,
		params,
		condition,
		() => {
			clearSelect2Selection("#filter_supplier_type");
			clearSelect2Selection("#filter_payment_terms");
			clearSelect2Selection("#filter_payment_mode");
		},
		reset
	);
}

/* Get supplier details */
function edit(id) {
	$(`#${modal}`).removeClass("add").addClass("edit");
	$(`#${modal} .modal-title`).text("Edit Supplier");
	$("#supplier_id").val(id);

	clearAlertInForm(elems);
	resetSelected();

	showLoading();

	$.post(router.supplier.edit, { id: id })
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
			$.post(router.supplier.delete, { id: id })
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
