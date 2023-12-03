var form, elems;

$(document).ready(function () {
	form = "form_export";
	elems = ["module", "start_date", "end_date"];

	/* Form for saving record */
	// formSubmit($("#" + form), "continue", function (res, self) {
	// 	if (res.status) {
	// 		const message = res.errors ?? res.message;

	// 		if (res.status !== STATUS.ERROR) {
	// 			self[0].reset();
	// 			notifMsgSwal(res.status, message, res.status);
	// 		}

	// 		showAlertInForm(elems, message, res.status);
	// 	}
	// });
});
