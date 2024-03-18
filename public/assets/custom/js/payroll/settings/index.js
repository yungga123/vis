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
	const endElem = $("#compensation_range_end");

	select2Init("#working_days");

	fetch();
	fetchTaxes();

	$(".rb_type").on("change", function (e) {
		const value = $(this).val();

		if (e.target.checked) {
			if (value === "below") {
				endElem.attr("readonly", "true");
				endElem.attr("type", "text");
				endElem.val(strCapitalize("below"));
			} else if (value === "above") {
				endElem.attr("readonly", "true");
				endElem.attr("type", "text");
				endElem.val(strCapitalize("above"));
			} else {
				endElem.removeAttr("readonly");
				endElem.attr("type", "number");
				endElem.val(endElem.val());
			}
		}
	});

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
				"sss_contri_rate_employee",
				"sss_contri_rate_employeer",
				"sss_salary_range_min",
				"sss_salary_range_max",
				"sss_next_diff_range_start_amount",
				"sss_starting_msc",
				"sss_last_msc",
				"sss_next_diff_msc_total_amount",
				"pagibig_contri_rate_employee",
				"pagibig_contri_rate_employeer",
				"pagibig_max_monthly_contri",
				"philhealth_contri_rate",
				"philhealth_income_floor",
				"philhealth_if_monthly_premium",
				"philhealth_income_ceiling",
				"philhealth_ic_monthly_premium",
			],
			message,
			res.status
		);
	});

	/* Form for saving record */
	formSubmit($("#form_bir_tax"), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			$("#tax_id").val("");
			$("#rb_amount").prop("checked", "true");

			endElem.removeAttr("readonly");
			self[0].reset();

			notifMsgSwal(res.status, res.message, res.status);
			fetchTaxes();
		}

		if (res.errors) notifMsg(res.message, res.status);

		showAlertInForm(
			[
				"compensation_range_start",
				"compensation_range_end",
				"fixed_tax_amount",
				"compensation_level",
				"tax_rate",
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

/* Get tax records */
function fetchTaxes() {
	fetchRecord(router.payroll.settings.tax.fetch, {}, null, (res) => {
		if (res.status === STATUS.SUCCESS) {
			let html = `
				<tr>
					<td colspan="5" class="text-center">No data found...</td>
				</tr>
			`;

			if (!isEmpty(res.data)) {
				html = "";

				$.each(res.data, (key, val) => {
					const range = val.below_or_above
						? `${numberFormat(val.compensation_range_start)} and ${
								val.below_or_above
						  }`
						: `${numberFormat(val.compensation_range_start)} to ${numberFormat(
								val.compensation_range_end
						  )}`;

					html += `
						<tr>
							<td>${range}</td>
							<td>${numberFormat(val.fixed_tax_amount)}</td>
							<td>${numberFormat(val.compensation_level)}</td>
							<td>${val.tax_rate} %</td>
							<td>
								<button class="btn btn-sm btn-warning" onclick="editTax(${
									val.id
								})"><i class="fa fa-edit"></i></button>
								<button class="btn btn-sm btn-danger" onclick="removeTax(${
									val.id
								})"><i class="fa fa-trash"></i></button>
							</td>
						</tr>
					`;
				});
			}

			$("#tax_table tbody").html(html);
		}
	});
}

/* Edit tax */
function editTax(id) {
	fetchRecord(router.payroll.settings.tax.fetch, { id: id }, null, (res) => {
		if (res.status === STATUS.SUCCESS) {
			if (!isEmpty(res.data)) {
				$("#tax_id").val(id);
				$("#compensation_range_start").val(res.data.compensation_range_start);
				$("#compensation_range_end").val(res.data.compensation_range_end);
				$("#fixed_tax_amount").val(res.data.fixed_tax_amount);
				$("#compensation_level").val(res.data.compensation_level);
				$("#tax_rate").val(res.data.tax_rate);

				const rb_type = res.data.below_or_above
					? res.data.below_or_above
					: "amount";

				$("#rb_" + rb_type).prop("checked", "true");
				$(".rb_type").trigger("change");
			}
		}
	});
}

/* Remove tax */
function removeTax(id) {
	deleteRecord(router.payroll.settings.tax.delete, { id: id }, null, (res) => {
		if (res.status === STATUS.SUCCESS) {
			fetchTaxes();
		}
		notifMsg(res.message, res.status);
	});
}
