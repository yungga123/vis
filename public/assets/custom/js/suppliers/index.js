var table, modal, form, editRoute, removeRoute, elems;

$(document).ready(function(){
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
        "remarks",
	];

	$("#btn_add_record").on("click", function () {
		$(`#${modal}`).modal("show");
		$(`#${modal}`).removeClass("edit").addClass("add");
		$(`#${modal} .modal-title`).text("Add New Supplier");
		$(`#${form}`)[0].reset();
		$("#supplier_id").val("");
		clearAlertInForm(elems);
		$('#others_supplier_type').attr('type','hidden').attr('required',false).val("");
	});

	/* Load dataTable */
	const route = $("#" + table).data("url");
	loadDataTable(table, route, METHOD.POST);

    /* Form for saving item */
	formSubmit($("#" + form), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			self[0].reset();
			refreshDataTable();
			notifMsgSwal(res.status, message, res.status);

			if ($(`#${modal}`).hasClass("edit")) {
				$(`#${modal}`).modal("hide");
			}
		}

		showAlertInForm(elems, message, res.status);
	});

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
						//$(`input[name="${key}"]`).val(value);
						$(`#${key}`).val(value);
						if (value == "Others" && key == "supplier_type") {
							selectedOthers(value);
						}
						if (value == "Others" && key == "payment_mode") {
							selectedPaymentMode(value);
						}
						console.log(key,value);
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

function remove(id) {
	const swalMsg = "delete";
	swalNotifConfirm(
		function () {
			$.post(removeRoute, { id: id })
				.then((res) => {
					const message = res.errors ?? res.message;

					refreshDataTable();
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
		$('#others_supplier_type').attr('type','text').attr('required',true);
	} else {
		$('#others_supplier_type').attr('type','hidden').attr('required',false).val("");
	}
}

function selectedPaymentMode(val) {
	if (val == "Others") {
		$('#others_payment_mode').attr('type','text').attr('required',true);
	} else {
		$('#others_payment_mode').attr('type','hidden').attr('required',false).val("");
	}
}

function resetSelected() {
	$('#others_supplier_type').attr('type','hidden').attr('required',false).val("");
	$('#others_payment_mode').attr('type','hidden').attr('required',false).val("");
}