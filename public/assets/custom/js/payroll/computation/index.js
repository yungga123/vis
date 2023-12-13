var cutOff = {},
	employeeInfo = {},
	earnings = {},
	deductions = {};

$(document).ready(function () {
	cutOff = {
		start_date: "",
		end_date: "",
		sundays: [],
		elems: {
			start_date: $("#start_date"),
			end_date: $("#end_date"),
		},
	};
	employeeInfo.working_days = 0;

	_initSelect2Employees();
	_toggleEmployee();
	_resetEmployeeInfo();
	_resetEarnings();
	_resetDeductions();

	$(".btn-cut-off").on("click", function () {
		clearSelect2Selection("#employee_id");
		_toggleEmployee();

		if (
			isEmpty(cutOff.elems.start_date.val()) ||
			isEmpty(cutOff.elems.end_date.val())
		) {
			notifMsgSwal(
				TITLE.ERROR,
				"Please select a cut-off date first!",
				STATUS.WARNING
			);
		} else {
			cutOff.start_date = cutOff.elems.start_date.val();
			cutOff.end_date = cutOff.elems.end_date.val();
			cutOff.sundays = [];
			employeeInfo.working_days = 0;

			if (moment(cutOff.start_date).isBefore(cutOff.end_date)) {
				_toggleEmployee(true);

				const sundays = getSundaysBetweenDates(
					cutOff.start_date,
					cutOff.end_date
				);
				const diffDays = getDateDiffCount(cutOff.start_date, cutOff.end_date);

				cutOff.sundays = sundays;
				employeeInfo.working_days = parseFloat(diffDays - sundays.length);
			} else {
				notifMsgSwal(
					TITLE.ERROR,
					"Start Date must be before the End Date!",
					STATUS.WARNING
				);
			}
			$("#working_days").val(employeeInfo.working_days);
		}
	});

	$('input[type="number"').on("keyup", function () {
		const value = parseFloat($(this).val());
		if (value) {
			const name = $(this).attr("name");

			if (earnings[name] !== undefined) earnings[name] = value;
			if (deductions[name] !== undefined) deductions[name] = value;

			_payrollCompute();
		}
	});

	/* Form for saving record */
	formSubmit($("#" + form), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			$("#id").val("");
			self[0].reset();
			notifMsgSwal(res.status, res.message, res.status);
			clearSelect2Selection("#employee_id");

			if ($(`#${modal}`).hasClass("edit")) {
				$(`#${modal}`).modal("hide");
			}
		}

		if (res.errors) notifMsg(res.message, res.status);

		showAlertInForm(elems, message, res.status);
	});
});

/* Employees select2 via ajax data source */
function _initSelect2Employees() {
	const options = { is_payroll_computation: true };

	select2AjaxInit(
		"#employee_id",
		"Select an employee",
		router.hr.common.employees,
		"text",
		_populateEmployeeInfo,
		options
	);
}

/* Employees select2 callack */
function _populateEmployeeInfo(data) {
	_resetEmployeeInfo();

	if (data.id) {
		employeeInfo.employee_id = data.id;
		employeeInfo.employment_status = data.employment_status;
		employeeInfo.position = data.position;

		if (data.rate_type) {
			employeeInfo.rate_type = data.rate_type;
			employeeInfo.salary_rate = parseFloat(data.salary_rate || 0);

			switch (strLower(data.rate_type)) {
				case "monthly":
					employeeInfo.cut_off_pay = employeeInfo.salary_rate / 2;
					employeeInfo.daily_rate =
						employeeInfo.cut_off_pay / employeeInfo.working_days;
					employeeInfo.hourly_rate = employeeInfo.daily_rate / 8;
					break;
				case "daily":
					employeeInfo.daily_rate = employeeInfo.salary_rate;
					employeeInfo.cut_off_pay =
						employeeInfo.daily_rate * employeeInfo.working_days;
					employeeInfo.hourly_rate = employeeInfo.daily_rate / 8;
					break;
				case "hourly":
					employeeInfo.hourly_rate = employeeInfo.salary_rate;
					employeeInfo.daily_rate = employeeInfo.hourly_rate * 8;
					employeeInfo.cut_off_pay =
						employeeInfo.daily_rate * employeeInfo.working_days;
					break;
			}

			_payrollCompute();
		}
	}

	$(".info-text.employee_id span").text(employeeInfo.employee_id);
	$(".info-text.employment_status span").text(employeeInfo.employment_status);
	$(".info-text.position span").text(employeeInfo.position);
	$(".info-text.rate_type span").text(employeeInfo.rate_type || "N/A");
	$(".info-text.salary_rate span").text(
		numberFormat(employeeInfo.salary_rate || 0)
	);
	$(".info-text.cut_off_pay span").text(
		numberFormat(employeeInfo.cut_off_pay || 0)
	);
	$(".info-text.daily_rate span").text(
		numberFormat(employeeInfo.daily_rate || 0)
	);
	$(".info-text.hourly_rate span").text(
		numberFormat(employeeInfo.hourly_rate || 0)
	);
	$("#basic_pay").val(numberFormat(employeeInfo.cut_off_pay || 0));

	_toggleEmployeeInfo(!isEmpty(data.id));
}

/* Reset employee info */
function _resetEmployeeInfo() {
	employeeInfo.employee_id = "";
	employeeInfo.employment_status = "";
	employeeInfo.position = "";
	employeeInfo.rate_type = "";
	employeeInfo.salary_rate = 0;
	employeeInfo.cut_off_pay = 0;
	employeeInfo.daily_rate = 0;
	employeeInfo.hourly_rate = 0;

	_toggleEmployeeInfo();
}

/* Reset employee earnings */
function _resetEarnings() {
	earnings.basic_pay = 0;
	earnings.working_days = 0;
	earnings.working_day_off = 0;
	earnings.working_day_off_amt = 0;
	earnings.over_time = 0;
	earnings.over_time_amt = 0;
	earnings.night_diff = 0;
	earnings.night_diff_amt = 0;
	earnings.regular_holiday = 0;
	earnings.regular_holiday_amt = 0;
	earnings.special_holiday = 0;
	earnings.special_holiday_amt = 0;
	earnings.vacation_leave = 0;
	earnings.vacation_leave_amt = 0;
	earnings.sick_leave = 0;
	earnings.sick_leave_amt = 0;
	earnings.incentives = 0;
	earnings.commission = 0;
	earnings.thirteen_month = 0;
	earnings.add_back = 0;
}

/* Reset employee deductions */
function _resetDeductions() {
	deductions.absent = 0;
	deductions.absent_amt = 0;
	deductions.tardiness = 0;
	deductions.tardiness_amt = 0;
	deductions.additional_rest_day = 0;
	deductions.additional_rest_day_amt = 0;
	deductions.sss = 0;
	deductions.pagibig = 0;
	deductions.philhealth = 0;
	deductions.withholding_tax = 0;
	deductions.cash_advance = 0;
	deductions.other_deductions = 0;
}

/* Toggle employee field */
function _toggleEmployee(show = false) {
	$("#employee").addClass("d-none").fadeOut(500);
	$("#employee_id").attr("disabled", "true");

	if (show) {
		$("#employee").removeClass("d-none").fadeIn(500);
		$("#employee_id").removeAttr("disabled");
	}
}

/* Toggle employee info */
function _toggleEmployeeInfo(show = false) {
	$(".row.employee_info").addClass("d-none");

	if (show) $(".row.employee_info").removeClass("d-none");
}

/* Payroll computation */
function _payrollCompute() {
	// Earnings
	earnings.basic_pay = employeeInfo.cut_off_pay;
	earnings.working_day_off_amt =
		employeeInfo.daily_rate * earnings.working_day_off * 1.3;
	earnings.over_time_amt = employeeInfo.hourly_rate * earnings.over_time * 1.25;
	earnings.night_diff_amt =
		employeeInfo.hourly_rate * earnings.night_diff * 0.1;
	earnings.regular_holiday_amt =
		employeeInfo.daily_rate * earnings.regular_holiday * 0.3;
	earnings.special_holiday_amt =
		employeeInfo.daily_rate * earnings.special_holiday;
	earnings.vacation_leave_amt =
		employeeInfo.daily_rate * earnings.vacation_leave;
	earnings.sick_leave_amt = employeeInfo.daily_rate * earnings.sick_leave;

	// Deductions
	deductions.absent_amt = employeeInfo.daily_rate * deductions.absent;
	deductions.tardiness_amt = employeeInfo.hourly_rate * deductions.tardiness;
	deductions.additional_rest_day_amt =
		employeeInfo.daily_rate * deductions.additional_rest_day;

	_populateEarnings();
	_populateDeductions();

	// Total
	const gross_pay =
		earnings.basic_pay +
		earnings.working_day_off_amt +
		earnings.over_time_amt +
		earnings.night_diff_amt +
		earnings.regular_holiday_amt +
		earnings.special_holiday_amt +
		earnings.vacation_leave_amt +
		earnings.sick_leave_amt -
		(deductions.absent_amt + deductions.tardiness_amt);

	const net_pay =
		gross_pay +
		earnings.thirteen_month +
		earnings.commission +
		earnings.add_back +
		earnings.incentives -
		(deductions.additional_rest_day_amt +
			deductions.sss +
			deductions.pagibig +
			deductions.philhealth +
			deductions.withholding_tax +
			deductions.cash_advance +
			deductions.other_deductions);

	$("td.gross_pay").html(numberFormat(gross_pay || 0));
	$("td.net_pay").html(numberFormat(net_pay || 0));
}

/* Populate earnings computation */
function _populateEarnings() {
	$.each(earnings, (key, val) => {
		if (!isObject(val)) {
			$("td." + key).html(numberFormat(val || 0));
		}
	});
}

/* Populate deductions computation */
function _populateDeductions() {
	$.each(deductions, (key, val) => {
		if (!isObject(val)) {
			$("td." + key).html(numberFormat(val || 0));
		}
	});
}
