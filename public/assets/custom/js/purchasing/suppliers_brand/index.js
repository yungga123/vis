function brand_suppliers() {
	/* Form for saving item */
	formSubmit($("#" + brand_form), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			self[0].reset();
			notifMsgSwal(res.status, message, res.status);

			if ($(`#${brand_modal}`).hasClass("edit")) {
				$(`#${brand_modal}`).modal("hide");
				refreshDataTable($("#" + brand_table));
			}
		}

		showAlertInForm(brand_elems, message, res.status, "alert_brand");
	});
}

/* Get supplier details */
function brand_edit(id) {
	$(`#${brand_modal}`).removeClass("add").addClass("edit");
	$(`#${brand_modal} .modal-title`).text("Edit Brand Supplier");
	$("#brand_id").val(id);

	clearAlertInForm(brand_elems);
	showLoading();

	$.post(brand_editRoute, { id: id })
		.then((res) => {
			closeLoading();

			if (res.status === STATUS.SUCCESS) {
				if (inObject(res, "data") && !isEmpty(res.data)) {
					$.each(res.data, (key, value) => {
						$(`input[name="${key}"]`).val(value);
					});
					setOptionValue("#brand_warranty", res.data.warranty);
				}
				$(`#${brand_modal}`).modal("show");
			} else {
				$(`#${brand_modal}`).modal("hide");
				notifMsgSwal(res.status, res.message, res.status);
			}
		})
		.catch((err) => catchErrMsg(err));
}

function brand_remove(id) {
	const swalMsg = "delete";
	swalNotifConfirm(
		function () {
			$.post(brand_removeRoute, { id: id })
				.then((res) => {
					const message = res.errors ?? res.message;

					refreshDataTable($("#" + brand_table));
					notifMsgSwal(res.status, message, res.status);
				})
				.catch((err) => catchErrMsg(err));
		},
		TITLE.WARNING,
		swalMsg,
		STATUS.WARNING
	);
}

function brand_add(id) {
	// Used in Select Customers from ADD BRANCH modal
	$("#brand_supplier_id").val(id);

	$(`#${brand_modal}`).modal("show");
	$(`#${brand_modal}`).removeClass("edit").addClass("add");
	$(`#${brand_modal} .modal-title`).text("Add Supplier Brand");

	clearAlertInForm(brand_elems);
	$(`#${brand_form}`)[0].reset();
}

function supplierbrandRetrieve(id, name) {
	const route = $("#" + brand_table).data("url") + "?supplier_id=" + id;

	$("#modal_supplier_brand").modal("show");
	$("#modal_supplier_brand .card-title").text(name);
	loadDataTable(brand_table, route, METHOD.GET, null, true);
}
