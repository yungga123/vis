var cutOff = {},
	employeeInfo = {},
	earnings = {},
	deductions = {},
	settings = {},
	_payroll = {},
	_earnings = {},
	_deductions = {},
	_employeeId = "";

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
	settings = $pjOptions.payroll_settings;
	_payroll = $pjOptions.payroll;
	_earnings = $pjOptions.earnings;
	_deductions = $pjOptions.deductions;

	_resetEmployeeInfo();
	_resetEarnings();
	_resetDeductions();

	if (!isEmpty($("#employee_name").val())) {
		// Cut-off
		cutOff.start_date = _payroll.cutoff_start;
		cutOff.end_date = _payroll.cutoff_end;
		cutOff.sundays = [];

		// Employee info
		employeeInfo.id = _payroll.id;
		employeeInfo.employee_id = _payroll.employee_id;
		employeeInfo.working_days = _payroll.working_days;
		employeeInfo.employment_status = _payroll.employment_status;
		employeeInfo.position = _payroll.position;
		employeeInfo.rate_type = _payroll.salary_type;
		employeeInfo.salary_rate = parseFloat(_payroll.basic_salary);
		employeeInfo.monthly_salary = parseFloat(_payroll.basic_salary);
		employeeInfo.cut_off_pay = parseFloat(_payroll.cutoff_pay);
		employeeInfo.daily_rate = parseFloat(_payroll.daily_rate);
		employeeInfo.hourly_rate = parseFloat(_payroll.hourly_rate);
		employeeInfo.gross_pay = parseFloat(_payroll.gross_pay);
		employeeInfo.net_pay = parseFloat(_payroll.net_pay);
		employeeInfo.notes = _payroll.notes;

		// Earnings
		earnings.basic_pay = _payroll.cutoff_pay;
		earnings.working_days_off = _earnings.working_days_off;
		earnings.working_days_off_amt = _earnings.working_days_off;
		earnings.over_time = _earnings.over_time;
		earnings.over_time_amt = _earnings.over_time_amt;
		earnings.night_diff = _earnings.night_diff;
		earnings.night_diff_amt = _earnings.night_diff_amt;
		earnings.regular_holiday = _earnings.regular_holiday;
		earnings.regular_holiday_amt = _earnings.regular_holiday_amt;
		earnings.special_holiday = _earnings.special_holiday;
		earnings.special_holiday_amt = _earnings.special_holiday_amt;
		earnings.service_incentive_leave = _earnings.service_incentive_leave;
		earnings.service_incentive_leave_amt =
			_earnings.service_incentive_leave_amt;
		earnings.incentives = _earnings.incentives;
		earnings.commission = _earnings.commission;
		earnings.thirteenth_month = _earnings.thirteenth_month;
		earnings.add_back = _earnings.add_back;

		// Deductions
		deductions.absent = _deductions.days_absent;
		deductions.absent_amt = _deductions.days_absent_amt;
		deductions.tardiness = _deductions.hours_late;
		deductions.tardiness_amt = _deductions.hours_late_amt;
		deductions.additional_rest_day = _deductions.addt_rest_days;
		deductions.additional_rest_day_amt = _deductions.addt_rest_days_amt;
		deductions.sss = _deductions.govt_sss;
		deductions.pagibig = _deductions.govt_pagibig;
		deductions.philhealth = _deductions.govt_philhealth;
		deductions.withholding_tax = _deductions.withholding_tax;
		deductions.cash_advance = _deductions.cash_advance;
		deductions.other_deductions = _deductions.others;

		_populateEarnings("both");
		_populateDeductions("both");
		_toggleEmployee(true);
		_toggleEmployeeInfo(true);

		$(".btn-submit").removeAttr("disabled");
		$("td.gross_pay").html(numberFormat(employeeInfo.gross_pay || 0));
		$("td.net_pay").html(numberFormat(employeeInfo.net_pay || 0));
		$("#id").val(employeeInfo.id || "");
		$("#withholding_tax").val(deductions.withholding_tax || "");
		$("#notes").val(employeeInfo.notes || "");

		if (isEmpty(deductions.withholding_tax)) {
			_calculateTax();
		}
	} else {
		_initSelect2Employees();
		_toggleEmployee();
	}

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
			_getWorkingDays();
		}
	});

	$('input[type="number"').on("keyup", function () {
		const wd = $("#working_days").val();
		const name = $(this).attr("name");

		if (!isEmpty(wd) || wd != 0) {
			const value = parseFloat($(this).val());

			if (earnings[name] !== undefined) earnings[name] = value;
			if (deductions[name] !== undefined) deductions[name] = value;

			_payrollCompute();
		}

		if (name === "working_days") {
			employeeInfo.working_days = parseFloat(wd);

			switch (strLower(employeeInfo.rate_type)) {
				case "monthly":
					employeeInfo.daily_rate =
						employeeInfo.working_days > 0
							? employeeInfo.cut_off_pay / employeeInfo.working_days
							: 0;
					employeeInfo.hourly_rate = employeeInfo.daily_rate / 8;
					break;
				case "daily":
					employeeInfo.daily_rate = employeeInfo.salary_rate;
					employeeInfo.hourly_rate = employeeInfo.daily_rate / 8;
					break;
				case "hourly":
					employeeInfo.hourly_rate = employeeInfo.salary_rate;
					employeeInfo.daily_rate = employeeInfo.hourly_rate * 8;
					break;
			}
			earnings.basic_pay = employeeInfo.daily_rate * employeeInfo.working_days;
			employeeInfo.cut_off_pay = earnings.basic_pay;

			$(".info-text.cut_off_pay span").text(
				numberFormat(employeeInfo.cut_off_pay || 0)
			);
			$(".info-text.daily_rate span").text(
				numberFormat(employeeInfo.daily_rate || 0)
			);
			$(".info-text.hourly_rate span").text(
				numberFormat(employeeInfo.hourly_rate || 0)
			);
			$("#basic_pay").val(numberFormat(earnings.basic_pay || 0));
			$("td.basic_pay").text(numberFormat(earnings.basic_pay || 0));

			_getGovtDeductions(true);
		}
	});

	$(".btn-submit").on("click", function () {
		employeeInfo.id = $("#id").val();
		employeeInfo.notes = $("#notes").val();
		swalNotifConfirm(_submit, TITLE.WARNING, "continue", STATUS.WARNING);
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
	_resetEarnings();
	_resetDeductions();
	_populateEarnings();
	_populateDeductions();

	$(".btn-submit").attr("disabled", "true");
	$("[data-earnings]").val("");
	$("[data-deductions]").val("");

	if (data.id) {
		employeeInfo.employee_id = data.id;
		employeeInfo.employment_status = data.employment_status;
		employeeInfo.position = data.position;

		if (data.rate_type) {
			employeeInfo.rate_type = data.rate_type;
			employeeInfo.salary_rate = parseFloat(data.salary_rate || 0);

			switch (strLower(data.rate_type)) {
				case "monthly":
					employeeInfo.monthly_salary = employeeInfo.salary_rate;
					employeeInfo.cut_off_pay = employeeInfo.salary_rate / 2;
					employeeInfo.daily_rate =
						employeeInfo.cut_off_pay / employeeInfo.working_days;
					employeeInfo.hourly_rate = employeeInfo.daily_rate / 8;
					break;
				case "daily":
					employeeInfo.daily_rate = employeeInfo.salary_rate;
					employeeInfo.hourly_rate = employeeInfo.daily_rate / 8;
					employeeInfo.cut_off_pay =
						employeeInfo.daily_rate * employeeInfo.working_days;
					employeeInfo.monthly_salary = employeeInfo.cut_off_pay * 2;
					break;
				case "hourly":
					employeeInfo.hourly_rate = employeeInfo.salary_rate;
					employeeInfo.daily_rate = employeeInfo.hourly_rate * 8;
					employeeInfo.cut_off_pay =
						employeeInfo.daily_rate * employeeInfo.working_days;
					employeeInfo.monthly_salary = employeeInfo.cut_off_pay * 2;
					break;
			}

			// Parse float
			employeeInfo.salary_rate = parseFloat(employeeInfo.salary_rate);
			employeeInfo.cut_off_pay = parseFloat(employeeInfo.cut_off_pay);
			employeeInfo.daily_rate = parseFloat(employeeInfo.daily_rate);
			employeeInfo.hourly_rate = parseFloat(employeeInfo.hourly_rate);

			_getGovtDeductions();
			_payrollCompute();
			_getWorkingDays();

			$(".btn-submit").removeAttr("disabled");
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

/* Populate earnings computation */
function _populateEarnings(param = false) {
	$.each(earnings, (key, val) => {
		if (!isObject(val)) {
			earnings[key] = parseFloat(val || 0);
			if (param === "both") {
				$("#" + key).val(val || 0);
				$("td." + key).html(numberFormat(val || 0));
			} else {
				param
					? $("#" + key).val(val || 0)
					: $("td." + key).html(numberFormat(val || 0));
			}
		}
	});
}

/* Populate deductions computation */
function _populateDeductions(param = false) {
	$.each(deductions, (key, val) => {
		if (!isObject(val)) {
			deductions[key] = parseFloat(val || 0);
			if (param === "both") {
				$("#" + key).val(val || 0);
				$("td." + key).html(numberFormat(val || 0));
			} else {
				param
					? $("#" + key).val(val || 0)
					: $("td." + key).html(numberFormat(val || 0));
			}
		}
	});
}

/* Payroll computation */
function _payrollCompute() {
	// Earnings
	earnings.basic_pay = employeeInfo.cut_off_pay;

	// worked days off * (daily rate * rest day working rate)
	// 1 * (500 * 1.3)
	earnings.working_days_off_amt =
		earnings.working_days_off *
		(employeeInfo.daily_rate * settings.ots_holidays.rest_day);

	// ot hours * (hourly rate + (hourly rate * ot rate))
	// 1 * (100 + (100 * 0.25))
	earnings.over_time_amt =
		earnings.over_time *
		(employeeInfo.hourly_rate +
			employeeInfo.hourly_rate * settings.ots_holidays.overtime);

	// Check if ot rate entered is 125% or 1.25
	// Is ot rate is greater than 1?
	if (settings.ots_holidays.overtime > 1) {
		// ot hours * (hourly rate * ot rate)
		// 1 * (100 * 1.25)
		earnings.over_time_amt =
			earnings.over_time *
			(employeeInfo.hourly_rate * settings.ots_holidays.overtime);
	}

	// night diff * (hourly rate * night diff rate)
	// 1 * (100 * 0.1)
	earnings.night_diff_amt =
		earnings.night_diff *
		(employeeInfo.hourly_rate * settings.ots_holidays.night_diff);

	// regulary holiday * (daily rate * regular holiday rate)
	// 1 * (500 * 2)
	earnings.regular_holiday_amt =
		earnings.regular_holiday *
		(employeeInfo.daily_rate * settings.ots_holidays.regular_holiday);

	// special holiday * (daily rate * special holiday rate)
	// 1 * (500 * 1.3)
	earnings.special_holiday_amt =
		earnings.special_holiday *
		(employeeInfo.daily_rate * settings.ots_holidays.special_holiday);

	// daily rate * service incentive leave
	// 500 * 1
	earnings.service_incentive_leave_amt =
		employeeInfo.daily_rate * earnings.service_incentive_leave;

	// Deductions
	deductions.absent_amt = employeeInfo.daily_rate * deductions.absent;
	deductions.tardiness_amt = employeeInfo.hourly_rate * deductions.tardiness;
	deductions.additional_rest_day_amt =
		employeeInfo.daily_rate * deductions.additional_rest_day;

	setTimeout(() => _calculateTax(), 100);
	_populateEarnings();
	_populateDeductions();

	setTimeout(() => {
		// Total
		const _non_taxable =
			earnings.thirteenth_month +
			earnings.commission +
			earnings.add_back +
			earnings.incentives;

		const gross_pay =
			earnings.basic_pay +
			earnings.working_days_off_amt +
			earnings.over_time_amt +
			earnings.night_diff_amt +
			earnings.regular_holiday_amt +
			earnings.special_holiday_amt +
			earnings.service_incentive_leave +
			_non_taxable;

		const __deductions =
			deductions.absent_amt +
			deductions.tardiness_amt +
			deductions.additional_rest_day_amt +
			deductions.sss +
			deductions.pagibig +
			deductions.philhealth +
			deductions.withholding_tax +
			deductions.cash_advance +
			deductions.other_deductions;

		const net_pay = gross_pay - __deductions;

		employeeInfo.gross_pay = gross_pay;
		employeeInfo.net_pay = net_pay;

		$("#withholding_tax").val(numberFormat(deductions.withholding_tax || 0));
		$("td.gross_pay").html(numberFormat(gross_pay || 0));
		$("td.net_pay").html(numberFormat(net_pay || 0));
	}, 100);
}

/* Calculate tax */
function _calculateTax() {
	const taxable_earnings = earnings.basic_pay;
	const non_taxable_deductions =
		deductions.sss + deductions.pagibig + deductions.philhealth;

	const taxable_income = parseFloat(taxable_earnings - non_taxable_deductions);

	$("#withholding_tax").val("");
	$("td.withholding_tax").text("0.00");

	if (!isEmpty(settings.bir_taxes || null) && !isEmpty(taxable_income)) {
		$.each(settings.bir_taxes, (key, val) => {
			const start_range = Math.round(val.compensation_range_start / 2);
			const end_range = Math.round(val.compensation_range_end / 2);
			const fixed_amt = val.fixed_tax_amount / 2;
			const tax_rate = val.tax_rate / 100;

			// Check if income is below the min start range
			const is_below =
				val.below_or_above === "below" && taxable_income <= start_range;
			// Then, return 0 - not taxable
			if (is_below) {
				deductions.withholding_tax = 0;
				return;
			}

			const is_above =
				val.below_or_above === "above" && taxable_income >= start_range;
			const is_middle =
				taxable_income >= start_range && taxable_income <= end_range;

			if (is_above || is_middle) {
				let result = fixed_amt + (taxable_income - start_range);
				result = result * tax_rate;

				deductions.withholding_tax = result;

				$("#withholding_tax").val(
					numberFormat(deductions.withholding_tax || 0)
				);
				$("td.withholding_tax").text(
					numberFormat(deductions.withholding_tax || 0)
				);
				return;
			}
		});
	}
}

/* Submit data */
function _submit() {
	const data = {
		cut_off: {
			start_date: cutOff.start_date,
			end_date: cutOff.end_date,
			sundays: cutOff.sundays,
		},
		employee_info: employeeInfo,
		payroll_earnings: earnings,
		payroll_deductions: deductions,
	};

	showLoading();

	$.post(router.payroll.computation.save, data)
		.then((res) => {
			closeLoading();

			if (res.status === STATUS.SUCCESS) {
				// Reset
				_resetAll();

				if (!isEmpty(_payroll)) {
					const message = res.message + "<br>Page will be closed in <b></b>...";
					swalNotifRedirect(res.status, message, res.status, "close");
					return;
				}
			}

			notifMsgSwal(res.status, res.message, res.status);
		})
		.catch((err) => catchErrMsg(err));
}

/* Get gov't deductions */
function _getGovtDeductions(isWorkingDays = false) {
	const route = router.payroll.computation.govt_deductions;
	const data = {
		cut_off_pay: employeeInfo.cut_off_pay,
		monthly_salary: employeeInfo.monthly_salary,
	};

	if (_employeeId != employeeInfo.employee_id || isWorkingDays) {
		fetchRecord(route, data, null, (res) => {
			if (res.status === STATUS.SUCCESS) {
				if ((inObject(res), "data")) {
					const _deductions = res.data.deductions;
					deductions.sss = _deductions.sss;
					deductions.pagibig = _deductions.pagibig;
					deductions.philhealth = _deductions.philhealth;

					// Display in input
					$("#sss").val(deductions.sss);
					$("#pagibig").val(deductions.pagibig);
					$("#philhealth").val(deductions.philhealth);

					// Display in overview table
					$("td.sss").text(numberFormat(deductions.sss));
					$("td.pagibig").text(numberFormat(deductions.pagibig));
					$("td.philhealth").text(numberFormat(deductions.philhealth));
				}
			}
		});
	}

	_employeeId = employeeInfo.employee_id;
}

/* Get working days */
function _getWorkingDays() {
	cutOff.start_date = cutOff.elems.start_date.val();
	cutOff.end_date = cutOff.elems.end_date.val();
	cutOff.sundays = [];
	employeeInfo.working_days = 0;

	if (moment(cutOff.start_date).isBefore(cutOff.end_date)) {
		_toggleEmployee(true);

		const sundays = getSundaysBetweenDates(cutOff.start_date, cutOff.end_date);
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

/* Reset data */
function _resetAll() {
	// Reset
	$("input").val("");
	$("#id").val("");
	$("#notes").val("");
	$("td.gross_pay").html("");
	$("td.net_pay").html("");
	$(".btn-submit").attr("disabled", "true");

	employeeInfo.id = 0;
	employeeInfo.working_days = 0;
	cutOff.start_date = "";
	cutOff.end_date = "";
	cutOff.sundays = [];
	cutOff.elems.start_date.val("");
	cutOff.elems.end_date.val("");

	_resetEmployeeInfo();
	_resetEarnings();
	_resetDeductions();
	_toggleEmployee();
	_toggleEmployeeInfo();
	_populateEarnings();
	_populateDeductions();
	clearSelect2Selection("#employee_id");
}

/* Reset employee info */
function _resetEmployeeInfo() {
	employeeInfo.id = 0;
	employeeInfo.employee_id = "";
	employeeInfo.employment_status = "";
	employeeInfo.position = "";
	employeeInfo.rate_type = "";
	employeeInfo.salary_rate = 0;
	employeeInfo.monthly_salary = 0;
	employeeInfo.cut_off_pay = 0;
	employeeInfo.daily_rate = 0;
	employeeInfo.hourly_rate = 0;
	employeeInfo.gross_pay = 0;
	employeeInfo.net_pay = 0;
	employeeInfo.notes = "";

	_toggleEmployeeInfo();
}

/* Reset employee earnings */
function _resetEarnings() {
	earnings.basic_pay = 0;
	earnings.working_days_off = 0;
	earnings.working_days_off_amt = 0;
	earnings.over_time = 0;
	earnings.over_time_amt = 0;
	earnings.night_diff = 0;
	earnings.night_diff_amt = 0;
	earnings.regular_holiday = 0;
	earnings.regular_holiday_amt = 0;
	earnings.special_holiday = 0;
	earnings.special_holiday_amt = 0;
	earnings.service_incentive_leave = 0;
	earnings.service_incentive_leave_amt = 0;
	earnings.incentives = 0;
	earnings.commission = 0;
	earnings.thirteenth_month = 0;
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
