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
	dtTable,
	ACTIONS = {
		VIEW: "View",
		ADD: "Add",
		EDIT: "Edit",
		DELETE: "Delete",
	};

$(document).ready(function () {
	$.ajaxSetup({
		headers: {
			"X-CSRF-TOKEN": $("meta#csrf").attr("content"),
		},
	});

	$(document).ajaxComplete(function (event, request, settings) {
		if (request.responseText === "already_logged_out") {
			setTimeout(() => {
				$(".modal").modal("hide");
				Swal.close();
				swalNotifRedirect(
					TITLE.INFO,
					"Your session has expired! You will be redirected to login page in <b></b> second/s.",
					STATUS.WARNING,
					"reload",
					6000
				);
			}, 100);
		}
	});

	$(document).on("hidden.bs.modal", function (event) {
		if ($(".modal:visible").length && !$("body").hasClass("modal-open")) {
			$("body").addClass("modal-open");
		}
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

	// Custom file input initialization
	$(".custom-file-input").on("change", function (e) {
		const fileName = e.target.files[0].name;
		$(this).next(".custom-file-label").text(fileName);
	});
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
function notifMsgSwal(title, message, status, scrollToTop) {
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
	} else if (message === "continue") {
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
			else if (url === "reload") window.location.reload();
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
			let select2 = $("#" + key).hasClass("select2") ? " select2-success" : "";
			let select2Err = $("#" + key).hasClass("select2")
				? " select2-danger"
				: "";

			$("#" + key)
				.removeClass("is-valid" + select2)
				.addClass("is-invalid" + select2Err);
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
			let select2 = $("#" + elem).hasClass("select2") ? " select2-success" : "";
			let select2Err = $("#" + elem).hasClass("select2")
				? " select2-danger"
				: "";

			$("#" + elem)
				.removeClass("is-invalid" + select2Err)
				.removeClass("is-valid" + select2);
			$(`#${prefix}_${elem}`).html("");

			if (status === STATUS.SUCCESS)
				$("#" + elem).addClass("is-valid" + select2);
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
function loadDataTable(
	table,
	route,
	type = METHOD.GET,
	options = {},
	destroy = false
) {
	let columnDefs = [
			inObject(options, "columnDefs")
				? options.columnDefs
				: {
						targets: 0,
						orderable: false,
				  },
		],
		order = inObject(options, "order") ? [options.order] : [],
		buttons = [
			{
				extend: "excel",
				titleAttr: "Export to Excel",
				exportOptions: {
					columns: ":visible",
				},
				className: "mr-1 rounded btn-outline-success",
				text: "<i class='fas fa-file-excel'></i> Excel",
			},
			{
				extend: "colvis",
				className: "mr-1 rounded btn-outline-primary",
				text: "<i class='fas fa-eye'></i> Column Visibility",
			},
		];

	if (inObject(options, "buttons") && !isEmpty(options.buttons)) {
		if (isArray(options.buttons)) {
			for (let index = 0; index < options.buttons.length; index++) {
				buttons.push(options.buttons[index]);
			}
		} else buttons.push(options.buttons);
	}

	dtTable = $("#" + table).DataTable({
		dom: `
			<'row px-3 pt-3'
				<'col-sm-12 col-md-8'<'d-flex justify-content-start'lB>>
				<'col-sm-12 col-md-4'f>
			>
			<'row'<'col-sm-12'tr>>
			<'row px-3 py-2'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>`,
		destroy: destroy,
		processing: true,
		// scrollX: true,
		autoWidth: false,
		columnDefs: columnDefs,
		order: order,
		language: {
			emptyTable: "No records found...",
			searchPlaceholder: "Search...",
			search: "",
			lengthMenu: "_MENU_",
		},
		lengthMenu: [
			[10, 25, 50, 100],
			["Show 10 rows", "Show 25 rows", "Show 50 rows", "Show 100 rows"],
		],
		buttons: {
			buttons: buttons,
			dom: {
				button: {
					className: "btn",
				},
			},
		},
		serverSide: true,
		ajax: {
			url: route,
			type: type.toUpperCase() !== METHOD.POST ? METHOD.GET : METHOD.POST,
			data: function (d) {
				if (inObject(options, "params") && !isEmpty(options.params)) {
					d.params = options.params;
				}
			},
		},
		createdRow: function (row, data, dataIndex) {
			if (data.length > 1) {
				for (var i = 0; i < data.length; i++) {
					if (isEmpty(data[i])) {
						$(`td:eq(${i})`, row).text("N/A");
					}
				}
			}
		},
		initComplete: function (settings, json) {
			$(".dataTables_wrapper").parent().addClass("p-0");
			$(".dataTables_wrapper .table").css({ width: "100%" });
			$(".dataTables_length").addClass("mr-2");
			$(".dataTables_filter input").removeClass("form-control-sm");
			$(".dataTables_length select").removeClass(
				"custom-select-sm form-control-sm"
			);
			$(".dt-buttons").removeClass("btn-group");

			// To fix not align header and body
			$("#" + table).wrap(
				"<div style='overflow: auto; width: 100%; position: relative;'></div>"
			);
		},
	});
}

/* Refresh dataTable data */
function refreshDataTable(table = null) {
	if (!isEmpty(table)) table.DataTable().ajax.reload();
	else dtTable.ajax.reload();
}

/**
 * Use for showing and hiding a password - dependent on input group password
 * You can see reference from Views/settings/send_mail.php for the input group
 *
 * @param {string} passId 		name of the password id selector
 * @param {string} showPassId 	name of the show password button id selector
 * @return {void}
 */
function passwordShowHideInit(passId, showPassId) {
	passId = passId || "password";
	showPassId = showPassId || "show_password";

	$("#" + showPassId).on("click", function () {
		if (!isEmpty($("#" + passId).val())) {
			if ($(this).hasClass("show")) {
				$(this)
					.removeClass("show")
					.attr("title", "Click here to show password!");
				$(this).children("i").removeClass().addClass("fas fa-eye");
				$("#" + passId).attr("type", "password");
			} else {
				$(this).addClass("show").attr("title", "Click here to hide password!");
				$(this).children("i").removeClass().addClass("fas fa-eye-slash");
				$("#" + passId).attr("type", "text");
			}
		}
	});
}

/*
 * Initializations and others
 */

/**
 * Initialize select2 normally
 *
 * @param {string} selector    	- id or class name of the select
 * @param {string} placeholder 	- placeholder
 * @param {object} data  		- data or the options to dispaly
 * @param {object} container  	- the container to attach to (e.g for modal)
 * @return void
 */
function select2Init(selector, placeholder, data, container) {
	selector = selector || ".select2";
	$(selector).select2({
		placeholder: placeholder || "Select an option",
		allowClear: true,
		data: data || {},
		attachContainer: container || "",
	});
}

/**
 * Initialize select2 via ajax data source
 *
 * @param {string} selector    	- id or class name of the select
 * @param {string} placeholder 	- placeholder
 * @param {string} route  		- the route or url to get data from
 * @param {string} text  		- the displayed text of selected item
 * @param {function} callaback  - the callback function after selecting an item
 * @param {object} options  	- the options (data) to be passed to the backend
 * @param {number} perPage  	- the length of options to display - default set to 10
 * @return void
 */
function select2AjaxInit(
	selector,
	placeholder,
	route,
	text,
	callaback,
	options,
	perPage
) {
	selector = selector || ".select2";

	function dataHandler(params) {
		let newOptions = {
			page: params.page || 1,
			perPage: perPage || 10,
		};

		if (isObject(options) && !isEmpty(options))
			$.each(options, (key, value) => (newOptions[key] = value));

		return {
			q: params.term || "",
			options: newOptions,
		};
	}

	$(selector).select2({
		placeholder: placeholder || "Select an option",
		allowClear: true,
		ajax: {
			url: route,
			type: "post",
			dataType: "json",
			delay: 250,
			cache: false,
			data: function (params) {
				return dataHandler(params);
			},
			processResults: function (response) {
				return {
					results: response.data,
				};
			},
		},
		templateResult: function (data) {
			return data[text] || data.text;
		},
		templateSelection: function (data) {
			if (isFunction(callaback)) callaback(data);
			return data[text] || data.text;
		},
	});
}

/**
 * Re-initialize select2 and new selection
 *
 * @param {string} selector    	- id or class name of the select
 * @param {string} placeholder 	- placeholder
 * @param {object} newData  	- new data or the new options to dispaly
 * @return void
 */
function select2Reinit(select, placeholder, newData) {
	$(select).html("");
	$(select).select2("destroy");

	select2Init(select, placeholder, newData);

	$(select).trigger("change");
}

/* Check if select2 was initialized */
function isSelect2Initialized(selector, initIfNot = false) {
	let isInitialized = $(selector).hasClass("select2-hidden-accessible");

	if (initIfNot && !isInitialized) $(selector).select2();
	return isInitialized;
}

/* Clear select2 selection */
function clearSelect2Selection(selector) {
	$(selector).val("").trigger("change");
}

/* Set select2 selection */
function setSelect2Selection(selector, val) {
	$(selector).val(val).trigger("change");
}

/**
 * Set select2 selection for ajax data source
 *
 * @param {string} selector    	- id or class name of the select
 * @param {string} text 		- text to display after selection
 * @param {string} id  			- the option value
 * @return void
 */
function setSelect2AjaxSelection(selector, text, id) {
	// Set selected option in select2
	const option = new Option(text, id, true, true);

	$(selector).append(option).trigger("change");
	$(selector).trigger({
		type: "select2:select",
	});
}

/* Get select2 selection */
function getSelect2Selection(selector, isText = false) {
	return isText ? $(selector + " :selected").text() : $(selector).val();
}

/* To set option value dynamically */
function setOptionValue(selector, val) {
	$(`${selector}`).val(val).change();
}

/* To format options for select2 */
function formatOptionsForSelect2(options, id, text) {
	if (isEmpty(options)) return [];
	return $.map(options, (val, i) => {
		if (Number.isInteger(Number(i))) return { id: val[id], text: val[text] };
		return { id: i, text: strCapitalize(val) };
	});
}

/**
 * Initialize date range picker
 * with custom callbacks for on apply and cancel events
 *
 * @param {string} selector 			- id or class name with '#' or '.' identifier
 * @param {object} options    			- options for date range picker
 * @param {function} onApplyCallback  	- on apply callback
 * @param {function} onCancelCallback 	- on cancel callback
 */
function initDateRangePicker(
	selector,
	options,
	onApplyCallback,
	onCancelCallback
) {
	options = options || {};
	$(selector).daterangepicker(options);
	$(selector).on("apply.daterangepicker", function (ev, picker) {
		if (!picker.autoUpdateInput) {
			$(this).val(
				picker.startDate.format(picker.locale.format) +
					" - " +
					picker.endDate.format(picker.locale.format)
			);
		}

		if (isFunction(onApplyCallback)) onApplyCallback(ev, picker);
	});
	$(selector).on("cancel.daterangepicker", function (ev, picker) {
		$(this).val("");
		if (isFunction(onCancelCallback)) onCancelCallback(ev, picker);
	});
}

/**
 * Initialize full calendar for schedule
 *
 * @param {string} elemName 		id or class name without identifier
 * @param {object} eventsData		calendar event data [object | json]
 * @param {object} options   		set other options for the calendar
 * @returns {object}				_calendar variable object
 */
function initFullCalendar(elemName, eventsData, options) {
	const calendarEl = document.getElementById(elemName);

	const headerToolbar = inObject(options, "headerToolbar")
		? options.headerToolbar
		: {
				left: "prev,next today",
				center: "title",
				right: "dayGridMonth,timeGridWeek,timeGridDay,listMonth",
		  };

	// Check if has eventPopHover callback otherwise call the default
	const eventPopHover =
		inObject(options, "eventPopHover") && isFunction(options.eventPopHover)
			? options.eventPopHover
			: (info) => {
					$(info.el).popover({
						container: "body",
						placement: "top",
						trigger: "hover",
						title: info.event.title,
						content: info.event.extendedProps.description,
					});
			  };

	// Check if has eventClick callback otherwise call the default
	const eventClick =
		inObject(options, "eventClick") && isFunction(options.eventClick)
			? options.eventClick
			: (info) => {
					// Please don't change the 'fcEventClick' function name
					// You need to define fcEventClick function in your module
					// to get the info from the event
					if (isFunctionExist(fcEventClick)) fcEventClick(info);
			  };

	var _calendar = new FullCalendar.Calendar(calendarEl, {
		themeSystem: "bootstrap",
		initialView: "dayGridMonth",
		displayEventTime: true,
		editable: true,
		height: "auto",
		headerToolbar: headerToolbar,
		views: {
			dayGridMonth: { buttonText: "Month" },
			timeGridWeek: { buttonText: "Week" },
			timeGridDay: { buttonText: "Day" },
			listMonth: { buttonText: "List" },
		},
		events: eventsData,
		dayMaxEvents: true, // allow "more" link when too many events
		navLinks: true,
		eventDidMount: eventPopHover,
		eventClick: eventClick,
	});

	_calendar.render();

	// You can use the return object to refresh
	// or add additional methods or functions to the calendar
	return _calendar;
}

/* Refreshing Full Calendar */
function refreshFullCalendar(elem) {
	elem.refetchEvents();
}

/*
 * More generic functions
 */
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

/* Check if param is Array or not - from stackoverflow */
function isArray(param) {
	return !isEmpty(param) && Array.isArray(param);

	/* Another approach */
	// return (Object.prototype.toString.call(obj) === '[object Object]');
}

/* Check if param/value is string */
function isString(param) {
	return Object.prototype.toString.call(param) === "[object String]";
}

/* Check if Object key exist */
function inObject(obj, key) {
	if (isEmpty(obj)) return false;
	return isObject(obj) ? Object.prototype.hasOwnProperty.call(obj, key) : false;

	/* Another methods */
	// return (key in obj); // Using 'in'
	// return obj.hasOwnProperty(key); // Same as above
}

/* Check if array or object? is associative from chatgpt */
function isArrayOrObjectAssoc(obj) {
	if (!isObject(obj) || !isArray(obj)) return false;

	// Check if the object has at least one non-numeric key
	for (const key in obj) {
		if (!Number.isInteger(Number(key))) return true;
	}

	return false;
}

/* Check if param is function */
function isFunction(param) {
	return typeof param === "function";
}

/* Check if function exist */
function isFunctionExist(param) {
	return isFunction(param);
}

/* Check if param is Object or not - from stackoverflow */
function countObject(obj) {
	return Object.keys(obj).length;
}

/* Check if param is number/float or not - from chatgpt */
function isNumber(param) {
	return typeof param === "number" && isFinite(param);
}

/* Check if is toastr is loaded */
function isToastrLoaded() {
	return window.toastr != undefined;
}

/* Check if is swal is loaded */
function isSwalLoaded() {
	return window.Swal != undefined;
}

/* Source: https://flexiple.com/javascript/javascript-capitalize-first-letter/ */
/* Capitalize first letter of string/word */
function strCapitalize(str) {
	return str.charAt(0).toUpperCase() + str.slice(1);
}

/* Transform string to upper case */
function strUpper(str) {
	return str.toUpperCase();
}

/* Transform string to lower case */
function strLower(str) {
	return str.toLowerCase();
}

/* Transform every words to upper case */
function strUpperWords(str) {
	const arr = str.split(" ");

	for (var i = 0; i < arr.length; i++) {
		arr[i] = arr[i].charAt(0).toUpperCase() + arr[i].slice(1);
	}

	return arr.join(" ");
}

/* Transform every words to lower case */
function strUpperWords(str) {
	const arr = str.split(" ");

	for (var i = 0; i < arr.length; i++) {
		arr[i] = arr[i].charAt(0).toLowerCase() + arr[i].slice(1);
	}

	return arr.join(" ");
}

/* Get the current date in "YYYY-MM-DD" format  */
function currentDate() {
	return new Date().toISOString().split("T")[0];
}
