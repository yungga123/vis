/* Declaration for global variable */
var STATUS = {
		SUCCESS: "success",
		ERROR: "error",
		INFO: "info",
		QUESTION: "question",
		WARNING: "warning",
		CONFIRM: "confirm",
	},
	TITLE = {
		SUCCESS: "Success!",
		ERROR: "Oops!",
		INFO: "Info!",
		QUESTION: "Question!",
		WARNING: "Warning!",
		CONFIRM: "Confirmation!",
	},
	METHOD = {
		GET: "GET",
		POST: "POST",
		AJAX: "AJAX",
	},
	dtTable;

$(document).ready(function () {
	$.ajaxSetup({
		headers: {
			"X-CSRF-TOKEN": $("meta#csrf").attr("content"),
		},
	});

	// Initialize toastr
	if (isToastrLoaded()) {
		toastr.options = {
			closeButton: false,
			debug: false,
			newestOnTop: false,
			progressBar: true,
			positionClass: "toast-top-center",
			preventDuplicates: false,
			onclick: null,
			showDuration: "300",
			hideDuration: "1000",
			timeOut: "4000",
			extendedTimeOut: "1000",
			showEasing: "swing",
			hideEasing: "linear",
			showMethod: "fadeIn",
			hideMethod: "fadeOut",
		};

		console.log("Toastr is loaded!");
	} else {
		console.log("Toastr is not loaded!");
	}

	if (isSwalLoaded()) console.log("Swal is loaded!");
	else console.log("Swal is not loaded!");
});

/* Show loading overlay - need to include the templates/loading view to work */
function showLoading(id) {
	id = id || "modal_loading";
	$("#" + id).modal("show");
}

/* Close loading overlay - need to include the templates/loading view to work */
function closeLoading(id) {
	id = id || "modal_loading";
	$("#" + id).modal("hide");

	if ($(".modal").hasClass("show")) $("body").addClass("modal-open");
}

/* Convert status into title */
function convertStatusToTitle(title) {
	switch (title) {
		case STATUS.SUCCESS:
			title = TITLE.SUCCESS;
			break;
		case STATUS.ERROR:
			title = TITLE.ERROR;
			break;
		case STATUS.INFO:
			title = TITLE.INFO;
			break;
		case STATUS.WARNING:
			title = TITLE.WARNING;
			break;
		case STATUS.QUESTION:
			title = TITLE.QUESTION;
			break;
		case STATUS.CONFIRM:
			title = TITLE.CONFIRM;
			break;
	}

	return title;
}

/**
 * General notif message using toastr
 * @param {string} message  - message to display
 * @param {string} status   - status of notif - refer to 'STATUS' global variable
 * @param {bool} objMsg     - identifier if message is object
 */
function notifMsg(message, status = STATUS.SUCCESS, objMsg = false) {
	if (objMsg || (!isEmpty(message) && isObject(message))) {
		$.each(message, function (key, value) {
			notifMsg(value, status);
		});
	} else {
		switch (status) {
			case STATUS.SUCCESS:
				toastr.success(message);
				break;
			case STATUS.ERROR:
				toastr.error(message);
				break;
			case STATUS.INFO:
				toastr.info(message);
				break;
			default:
				break;
		}
	}
}

/**
 * Swal notification message
 * @param {string} title    - title of the notification - refer to 'TITLE' global variable
 * @param {string} message  - message to display
 * @param {string} status   - status of notif - refer to 'STATUS' global variable
 */
function notifMsgSwal(title, message, status) {
	title = convertStatusToTitle(title);

	if (!isEmpty(message) && isObject(message)) {
		let msg = "";
		$.each(message, (key, value) => (msg += value + " <br>"));
		notifMsgSwal(title, msg, status);
	} else {
		Swal.fire({ title: title, html: message, icon: status });
	}
}

/**
 *  Show swal message with confirmation
 * @param {function} customFunc - custom function to pass after confirming
 * @param {string} title        - title of the notification - refer to 'TITLE' global variable
 * @param {string} message      - message to display
 * @param {string} status       - status of notif - refer to 'STATUS' global variable
 * @param {string} confirmText  - text for confirm button
 * @param {string} cancelText   - text for cancel button
 */
function swalNotifConfirm(
	customFunc,
	title,
	message,
	status,
	confirmText,
	cancelText
) {
	let color = "#dc3545";
	title = convertStatusToTitle(title);

	if (message === "delete") {
		message = "Are you really sure you want to delete this?";
		confirmText = "Yes, delete it!";
	} else if ((message = "continue")) {
		message = "Are you really sure you want to continue?";
		color = "#007bff";
	}

	Swal.fire({
		title: title,
		html: message,
		icon: status,
		showCancelButton: true,
		focusCancel: true,
		confirmButtonColor: color,
		confirmButtonText: confirmText || "Yes, continue!",
		cancelButtonText: cancelText || "No, cancel!",
		reverseButtons: true,
		allowOutsideClick: false,
	}).then((result) => {
		if (result.isConfirmed) customFunc();
	});
}

/**
 * Swal notification with redirect to provided url
 * @param {string} title    - title of the notification - refer to 'TITLE' global variable
 * @param {string} message  - message to display
 * @param {string} status   - status of notif - refer to 'STATUS' global variable
 * @param {string} url      - url to redirect
 * @param {int} timer       - timer to run when redirect
 */
function swalNotifRedirect(title, message, status, url, timer) {
	let timerInterval;
	title = convertStatusToTitle(title);

	Swal.fire({
		title: title,
		html: message,
		icon: status,
		timer: timer || 5000,
		timerProgressBar: true,
		allowOutsideClick: false,
		didOpen: () => {
			Swal.showLoading();
			const b = Swal.getHtmlContainer().querySelector("b");
			timerInterval = setInterval(() => {
				b.textContent = Math.ceil(Swal.getTimerLeft() / 1000);
			}, 100);
		},
		willClose: () => {
			clearInterval(timerInterval);
		},
	}).then((result) => {
		if (result.dismiss === Swal.DismissReason.timer) {
			if (url === "close") window.close();
			else window.location = url;
		}
	});
}

/* Catch error message */
function catchErrMsg(err, swal = false) {
	const message =
		"Error while process data! Please contact your system administrator.";
	if ($("modal_loading").length > 0) closeLoading();

	if (swal || !isToastrLoaded())
		notifMsgSwal(TITLE.ERROR, message, STATUS.ERROR);
	else notifMsg(message, STATUS.ERROR);

	console.log(err);
}

/**
 * Show alert message in form or small elem
 * @param {string} elems    - list of name of the inputs
 * @param {string} errors   - error message pass from validation error
 * @param {string} status   - status of alert - refer to 'STATUS' global variable
 * @param {string} prefix   - prefix for small elements container for error messages
 * @param {bool} swal       - set true if using swal
 */
function showAlertInForm(elems, errors, status, prefix = "alert", swal = true) {
	/* Clear elements first */
	clearAlertInForm(elems, status, prefix);

	if (isObject(errors) && !isEmpty(errors)) {
		$.each(errors, (key, value) => {
			$("#" + key)
				.removeClass("is-valid")
				.addClass("is-invalid");
			$(`#${prefix}_${key}`).html(value);
		});
	}

	if (isString(errors) && status === STATUS.ERROR) {
		if (isToastrLoaded() && swal === false) notifMsg(errors, status);
		else notifMsgSwal(TITLE.ERROR, errors, STATUS.ERROR);
	}
}

/**
 * Clear alert message in form or small elem
 * @param {array} elems     - list of name of the inputs
 * @param {string} status   - status of alert - refer to 'STATUS' global variable
 * @param {string} prefix   - prefix for small elements container for error messages
 */
function clearAlertInForm(elems, status, prefix = "alert") {
	if (Array.isArray(elems) && !isEmpty(elems)) {
		for (let i = 0; i < elems.length; i++) {
			const elem = elems[i];
			$("#" + elem)
				.removeClass("is-invalid")
				.removeClass("is-valid");
			$(`#${prefix}_${elem}`).html("");

			if (status === STATUS.SUCCESS) $("#" + elem).addClass("is-valid");
		}
	}
}

/**
 * For submitting form using ajax - just pass the required parameters
 * @param {object} form             - JQuery selector object (e.g. $('#form'))
 * @param {string} confirmMsg       - confirmation message
 * @param {function} responseFunc   - callback response function - you can refer to accounts/form.js
 * @param {string} requestType      - either 'AJAX' (ajax for file upload) or 'POST' / POST is default
 * @param {boolean} attachment      - identifier if form has file upload
 */
function formSubmit(
	form,
	confirmMsg,
	responseFunc,
	requestType = METHOD.POST,
	attachment = false
) {
	form.on("submit", function (e) {
		e.preventDefault();
		const self = $(this);
		const route = self.attr("action");
		const data = attachment ? new FormData(this) : self.serialize();

		function sendRequest() {
			showLoading();

			if (requestType === METHOD.POST) {
				// Default request type method
				$.post(route, data)
					.then((res) => {
						responseFunc(res, self);
						closeLoading();
					})
					.catch((err) => catchErrMsg(err));
			} else {
				$.ajax({
					url: route,
					type: METHOD.POST,
					data: data,
					dataType: "json",
					contentType: false,
					cache: false,
					processData: false,
					success: function (res) {
						responseFunc(res, self);
						closeLoading();
					},
				});
			}
		}

		swalNotifConfirm(sendRequest, TITLE.CONFIRM, confirmMsg, STATUS.QUESTION);
	});
}

/**
 * Load dataTable data
 * @param {string} table    - id of the table
 * @param {string} route    - url path to get data
 * @param {string} type     - type of request method (GET, POST)
 * @param {object} options  - other options for the dataTable
 */
function loadDataTable(table, route, type = METHOD.GET, options = {}) {
	let columnDefs = [
			inObject(options, "columnDefs")
				? options.columnDefs
				: {
						targets: 0,
						orderable: false,
				  },
		],
		order = inObject(options, "order") ? [options.order] : [];

	columnDefs.push();

	dtTable = $("#" + table).DataTable({
		processing: true,
		scrollX: true,
		autoWidth: false,
		columnDefs: columnDefs,
		order: order,
		language: {
			emptyTable: "No records found...",
		},
		buttons: [
			{
				extend: "excel",
				exportOptions: {
					columns: ":visible",
				},
				text: "Export to Excel",
			},
			"colvis",
		],
		serverSide: true,
		ajax: {
			url: route,
			type: type.toUpperCase() !== METHOD.POST ? METHOD.GET : METHOD.POST,
		},
		initComplete: function (settings, json) {
			dtTable
				.buttons()
				.container()
				.appendTo(`#${table}_wrapper .col-md-6:eq(0)`);
		},
	});
}

/* Refresh dataTable data */
function refreshDataTable(table = null) {
	if (!isEmpty(table)) table.DataTable().ajax.reload();
	else dtTable.ajax.reload();
}

/*
 * Use for showing and hiding a password - dependent on input group password
 * You can see reference from Views/settings/send_mail.php for the input group
 * passId = name of the password id selector
 * showPassId = name of the show password button id selector
 */
function passwordShowHideInit(
	passId = "password",
	showPassId = "show_password"
) {
	$("#" + showPassId).on("click", function () {
		if ($(this).hasClass("show")) {
			$(this).removeClass("show").attr("title", "Click here to show password!");
			$(this).children("i").removeClass().addClass("fas fa-eye");
			$("#" + passId).attr("type", "password");
		} else {
			$(this).addClass("show").attr("title", "Click here to hide password!");
			$(this).children("i").removeClass().addClass("fas fa-eye-slash");
			$("#" + passId).attr("type", "text");
		}
	});
}

/*
 * Small functions
 */

/* Initialize select2 */
function select2Init(selector) {
	selector = selector || ".select2";
	$(`${selector}`).select2();
}

/* To set option value dynamically */
function setOptionValue(selector, val) {
	$(`${selector}`).val(val).change();
}

/* Check if value is empty - from stackoverflow */
function isEmpty(value) {
	return (
		// null or undefined
		value == null ||
		// has length and it's zero
		(value.hasOwnProperty("length") && value.length === 0) ||
		// is an Object and has no keys
		(value.constructor === Object && Object.keys(value).length === 0)
	);
}

/* Check if param is Object or not - from stackoverflow */
function isObject(obj) {
	return typeof obj === "object" && obj !== null && !Array.isArray(obj);

	/* Another approach */
	// return (Object.prototype.toString.call(obj) === '[object Object]');
}

/* Check if param/value is string */
function isString(param) {
	return Object.prototype.toString.call(param) === "[object String]";
}

/* Check if Object key exist */
function inObject(obj, key) {
	return isObject(obj) ? Object.prototype.hasOwnProperty.call(obj, key) : false;

	/* Another methods */
	// return (key in obj); // Using 'in'
	// return obj.hasOwnProperty(key); // Same as above
}

/* Check if param is Object or not - from stackoverflow */
function countObject(obj) {
	return Object.keys(obj).length;
}

/* Check if is toastr is loaded */
function isToastrLoaded() {
	return window.toastr != undefined;
}

/* Check if is swal is loaded */
function isSwalLoaded() {
	return window.Swal != undefined;
}
