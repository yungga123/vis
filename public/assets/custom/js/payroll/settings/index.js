$(document).ready(function () {
	$("[data-percentage]").inputmask({
		alias: "percentage",
		integerDigits: 3,
		digits: 2,
		max: 500,
		allowMinus: false,
		digitsOptional: false,
		placeholder: "0",
	});

	select2Init("#working_days");

	fetch();

	/* Form for saving record */
	formSubmit($("#form_working_days_time"), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			self[0].reset();
			notifMsgSwal(res.status, res.message, res.status);
			fetch();
		}

		if (res.errors) notifMsg(res.message, res.status);

		showAlertInForm(
			[
				"working_days",
				"working_time_in",
				"working_time_out",
				"default_vacation_leave",
				"default_sick_leave",
			],
			message,
			res.status
		);
	});

	/* Form for saving record */
	formSubmit($("#form_overtime"), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			self[0].reset();
			notifMsgSwal(res.status, res.message, res.status);
			fetch();
		}

		if (res.errors) notifMsg(res.message, res.status);

		showAlertInForm(
			[
				"overtime",
				"night_diff",
				"rest_day",
				"rest_day_overtime",
				"regular_holiday",
				"regular_holiday_overtime",
				"special_holiday",
				"special_holiday_overtime",
			],
			message,
			res.status
		);
	});

	/* Form for saving record */
	formSubmit($("#form_government"), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			self[0].reset();
			notifMsgSwal(res.status, res.message, res.status);
			fetch();
		}

		if (res.errors) notifMsg(res.message, res.status);

		showAlertInForm(
			[
				"sss_contri_rate_employeer",
				"sss_contri_rate_employee",
				"sss_salary_range_min",
				"sss_salary_range_max",
				"sss_starting_msc",
				"sss_last_msc",
				"sss_next_diff_amount",
				"pagibig_contri_rate_employeer",
				"pagibig_contri_rate_employee",
				"philhealth_contri_rate",
			],
			message,
			res.status
		);
	});
});

/* Get records */
function fetch() {
	fetchRecord(router.payroll.settings.fetch, {}, null, (res) => {
		if (res.status === STATUS.SUCCESS) {
			if (!isEmpty(res.data)) {
				$.each(res.data, (key, val) => {
					if (val.key === "working_days") {
						setSelect2Selection("#working_days", JSON.parse(val.value));
					} else {
						$(`#${val.key}`).val(val.value);
					}
				});
			}
		}
	});
}
