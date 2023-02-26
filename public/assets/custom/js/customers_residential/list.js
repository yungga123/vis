var table, modal, form, editRoute, removeRoute, elems;
$(document).ready(function () {
	table = "customers_residential_table";
	modal = "modal_customers_residential";
	form = "form_customers_residential";
	editRoute = $("#edit_url").val();
	removeRoute = $("#remove_url").val();
	elems = [
		"forecast",
		"customer_name",
		"contact_person",
		"address_province",
		"address_city",
		"address_brgy",
		"address_sub",
		"contact_number",
		"email_address",
		"source",
		"notes",
	];

	$("#btn_add_record").on("click", function () {
		$(`#${modal}`).modal("show");
		$(`#${modal}`).removeClass("edit").addClass("add");
		$(`#${modal} .modal-title`).text("Add New Item");
		$(`#${form}`)[0].reset();
		$("#customerresidential_id").val("");

		clearAlertInForm(elems);
	});

	/* Load dataTable */
	
	const route = $("#" + table).data("url");
	let custom_button = [
		{
			text: 'All',
			action: function () {
				//alert( 'Button FORECAST' );
	
				loadDataTable(table, route, METHOD.POST,options = {},destroy = true,custom_button);
			}
		},
		{
			text: 'Forecast',
			action: function () {
				//alert( 'Button FORECAST' );
	
				let url = $('#forecast_url').val() + "?forecast=YES";
				loadDataTable(table, url, METHOD.GET,options = {},destroy = true,custom_button);
			}
		},
		{
			text: 'Official',
			action: function () {
				//alert( 'Button OFFICIAL' );
				let url = $('#forecast_url').val() + "?forecast=NO";
				loadDataTable(table, url, METHOD.GET,options = {},destroy = true,custom_button);
			}
		}];
	
		loadDataTable(table, route, METHOD.POST,options = {},destroy = false,custom_button);

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

/* Get record details */
function edit(id) {
	$(`#${modal}`).removeClass("add").addClass("edit");
	$(`#${modal} .modal-title`).text("Edit Item");
	$("#customerresidential_id").val(id);

	clearAlertInForm(elems);
	showLoading();

	$.post(editRoute, { id: id })
		.then((res) => {
			closeLoading();

			if (res.status === STATUS.SUCCESS) {
				$(`#${modal}`).modal("show");

				if (inObject(res, "data") && !isEmpty(res.data)) {
					$.each(res.data, (key, value) => {
						$("#" + key).val(value);
					});
				}
			} else {
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
