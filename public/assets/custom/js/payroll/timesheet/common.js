/* Get current clock attendance */
function getCurrentClockAttendance() {
	if ($(".clock-attendance").length) {
		_post(router.payroll.timesheet.fetch, { current: true }, (res) => {
			if (res.status === STATUS.ERROR) {
				catchErrMsg(res.message);
				return;
			}
			$("#_clock_in_id").val("");
			$("#btn_clock_in").removeAttr("disabled");
			$("#btn_clock_in span").text("");
			$("#btn_clock_out span").text("");
			$("#btn_clock_out").attr("disabled", "true");

			if (res.data) {
				const _clock_in = res.data._clock_in ? "- " + res.data._clock_in : "";
				const _clock_out = res.data._clock_out
					? "- " + res.data._clock_out
					: "";

				$("#_clock_in_id").val(res.data.id || "");
				$("#btn_clock_in span").text(_clock_in);
				$("#btn_clock_out span").text(_clock_out);
				$("#btn_clock_in").attr("disabled", "true");
				$("#btn_clock_out").removeAttr("disabled");

				if (res.data.clock_in && res.data.clock_out) {
					$("#btn_clock_in").attr("disabled", "true");
					$("#btn_clock_out").attr("disabled", "true");
				}
			}
		});
	}
}

/* Save the clock in attendance */
function attendanceClockIn() {
	if (confirm("Are you sure you want to continue?")) {
		_post(router.payroll.timesheet.clock, { action: "clock_in" }, (res) => {
			if (res.status === STATUS.SUCCESS) {
				$("#btn_clock_in").attr("disabled", "true");
				notifMsg("You have CLOCKED IN!");
				getCurrentClockAttendance();
			}
		});
	}
}

/* Save the clock out attendance */
function attendanceClockOut() {
	const data = {
		id: $("#_clock_in_id").val(),
		action: "clock_out",
	};
	if (confirm("Are you sure you want to continue?")) {
		_post(router.payroll.timesheet.clock, data, (res) => {
			if (res.status === STATUS.SUCCESS) {
				$("#btn_clock_in").attr("disabled", "true");
				$("#btn_clock_out").attr("disabled", "true");
				notifMsg("You have CLOCKED OUT!");
				getCurrentClockAttendance();
			}
		});
	}
}

/* Ajax post method to send request */
function _post(route, data, callable) {
	showLoading();

	$.post(route, data)
		.then((res) => {
			closeLoading();

			if (res.status === STATUS.ERROR) {
				notifMsg(res.message, STATUS.ERROR);
				return;
			}

			callable(res);
		})
		.catch((err) => catchErrMsg(err));
}
