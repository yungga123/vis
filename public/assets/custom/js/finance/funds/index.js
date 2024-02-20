var table, modal, form, elems;

$(document).ready(function () {
	table = "funds_table";
	modal = "funds_modal";
	form = "funds_form";
	elems = ["transaction_amount", "expenses", "remarks"];

	select2Init("#filter_transaction_type");
	select2Init("#filter_coming_from");
	select2Init("#filter_expenses");

	/* Load dataTable */
	loadDataTable(table, router.funds.list, METHOD.POST);

	/* Form for saving record */
	formSubmit($("#" + form), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			_toggleForm();
			_resetForm();
			refreshDataTable($("#" + table));
			notifMsgSwal(res.status, res.message, res.status);

			$("h2.funds").html(numberFormat(res.funds || 0));
			$("#current_funds").val(res.funds);
		}

		if (res.errors) notifMsg(res.message, res.status);

		showAlertInForm(elems, message, res.status);
	});
});

/* Toggle for showing the form */
function btnReleaseFund(funds, canRelease) {
	if (funds == 0 || !canRelease) {
		_toggleForm();
		notifMsgSwal(
			"Can't Release Funds!",
			"You don't have a permission or enough funds to release!",
			STATUS.WARNING
		);

		return;
	}

	_toggleForm(true);
	_resetForm();
}

/* For filtering and reseting */
function filterData(reset = false) {
	const transaction_type = getSelect2Selection("#filter_transaction_type");
	const coming_from = getSelect2Selection("#filter_coming_from");
	const expenses = getSelect2Selection("#filter_expenses");
	const params = {
		transaction_type: transaction_type,
		coming_from: coming_from,
		expenses: expenses,
	};
	const condition =
		!isEmpty(transaction_type) || !isEmpty(coming_from) || !isEmpty(expenses);

	filterParam(
		router.funds.list,
		table,
		params,
		condition,
		() => {
			clearSelect2Selection("#filter_transaction_type");
			clearSelect2Selection("#filter_coming_from");
			clearSelect2Selection("#filter_expenses");
		},
		reset
	);
}

/* Toggle form */
function _toggleForm(show) {
	const elem = $("#" + form);

	if (show) elem.fadeIn(1000).removeClass("d-none");
	else {
		if (!elem.hasClass("d-none")) {
			elem.fadeOut("slow", "swing", () => elem.addClass("d-none"));
		}
	}
}

/* Reset form */
function _resetForm() {
	$(`#${form}`)[0].reset();

	clearAlertInForm(elems);
}
