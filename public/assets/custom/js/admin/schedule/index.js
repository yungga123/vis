var table, modal, form, elems, $calendar, dateRangeformat, startEndDateFormat;

$(document).ready(function () {
	table = "schedule_table";
	modal = "schedule_modal";
	form = "schedule_form";
	elems = ["job_order_id", "title", "description", "type", "date_range"];
	dateRangeformat = "MMM DD, YYYY hh:mm A";
	startEndDateFormat = "YYYY-MM-DD HH:mm:ss";

	/* Toggle modal */
	$("#btn_add_record").on("click", function () {
		$(`#${modal}`).modal("show");
		$(`#${modal}`).removeClass("edit").addClass("add");
		$(`#${modal} .modal-title`).text("Add Schedule");
		$(`#${form}`)[0].reset();
		$("#schedule_id").val("");

		clearSelect2Selection("#job_order_id");
		_toggleDeleteBtn();
		clearAlertInForm(elems);
	});

	/* Initialize & customize date range picker */
	initDateRangePicker(
		"#date_range",
		{
			opens: "center",
			timePicker: true,
			timePickerIncrement: 10,
			locale: {
				format: dateRangeformat,
				cancelLabel: "Clear",
			},
			autoUpdateInput: false,
		},
		function (ev, picker) {
			$("#start").val(picker.startDate.format(startEndDateFormat));
			$("#end").val(picker.endDate.format(startEndDateFormat));
		},
		function (ev, picker) {
			$("#start").val("");
			$("#end").val("");
		}
	);

	/* Initialize fullcalendar */
	// option for event pop hover
	const options = {
		eventPopHover: (info) => {
			const job_order_id =
				info.event.extendedProps.job_order.id != 0
					? info.event.extendedProps.job_order.id
					: "N/A";

			$(info.el).popover({
				container: "body",
				placement: "top",
				trigger: "hover",
				html: true,
				title: `<strong>${info.event.title}</strong>`,
				content: `
					<div>
						<strong>ID: </strong>
						<span>${info.event.id}</span>
					</div>
					<div>
						<strong>Job Order #: </strong>
						<span>${job_order_id}</span>
					</div>
					<div>
						<strong>Schedule Type</strong>
						<div>${info.event.extendedProps.typeText}</div>
					</div>
					<div>
						<strong>Description</strong>
						<div>${info.event.extendedProps.description}</div>
					</div>
				`,
			});
		},
	};
	$calendar = initFullCalendar("calendar", router.schedule.list, options);

	/* Job Orders select2 via ajax data source */
	select2AjaxInit(
		"#job_order_id",
		"Search and select a job order",
		router.admin.common.job_orders,
		"text",
		(data) => {
			if (data.client) {
				$("#title").val(data.client);
			}
		}
	);

	/* Form for saving record */
	formSubmit($("#" + form), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			if (res.status === STATUS.SUCCESS) {
				self[0].reset();
				$("#schedule_id").val("");
				clearSelect2Selection("#job_order_id");

				if ($(`#${modal}`).hasClass("edit")) {
					$(`#${modal}`).modal("hide");
				}
			}
			notifMsgSwal(res.status, message, res.status);
			refreshFullCalendar($calendar);
			_toggleDeleteBtn();
		}

		showAlertInForm(elems, message, res.status);
	});
});

/* Get/populate record details */
function fcEventClick(param) {
	$(`#${modal}`).removeClass("add").addClass("edit");
	$(`#${modal} .modal-title`).text("Edit Schedule");

	clearSelect2Selection("#job_order_id");
	clearAlertInForm(elems);
	showLoading();

	$("#schedule_id").val(param.event.id);
	$("#title").val(param.event.title);
	$("#start").val(moment(param.event.start).format(startEndDateFormat));
	$("#end").val(moment(param.event.end).format(startEndDateFormat));
	$("#description").val(param.event.extendedProps.description);
	setOptionValue("#type", param.event.extendedProps.type);
	$("#date_range").val(
		moment(param.event.start).format(dateRangeformat) +
			" - " +
			moment(param.event.end).format(dateRangeformat)
	);

	$(`#${modal}`).modal("show");

	_setValueInJobOrderSelect2(
		param.event.extendedProps.job_order.id,
		param.event.extendedProps.job_order.client_name
	);
	_toggleDeleteBtn(param.event.id);
	closeLoading();
}

/* Delete record */
function remove() {
	const swalMsg = "delete";
	const id = $("#schedule_id").val();

	swalNotifConfirm(
		function () {
			$.post(router.schedule.delete, { id: id })
				.then((res) => {
					const message = res.errors ?? res.message;
					if (res.status === STATUS.SUCCESS) refreshFullCalendar($calendar);

					$(`#${modal}`).modal("hide");
					notifMsgSwal(res.status, message, res.status);
				})
				.catch((err) => catchErrMsg(err));
		},
		TITLE.WARNING,
		swalMsg,
		STATUS.WARNING
	);
}

/* Toggle delete button */
function _toggleDeleteBtn(id) {
	if (id) $("#btn_delete").removeClass("d-none");
	else $("#btn_delete").addClass("d-none");
}

/* Set value in job_order_id select2 */
function _setValueInJobOrderSelect2(id, text) {
	if (text) {
		text = `${id} | ${text}`;
		setSelect2AjaxSelection("#job_order_id", text, id);
	}
}
