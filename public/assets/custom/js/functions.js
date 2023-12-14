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
 * @param {array} elems    - list of name of the inputs
 * @param {string|object} errors   - error message pass from validation error
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
 * Shorcut for fetching record via ajax post request
 *
 * @param {string} route            	- The backend delete route/url
 * @param {object} data     			- Object of data to pass - like {id: id}
 * @param {CallableFunction} callback 	- A callable to handle the response
 */
function fetchRecord(route, data, callback) {
	showLoading();

	$.post(route, data)
		.then((res) => {
			closeLoading();

			if (res.status === STATUS.ERROR) {
				$(`#${modal}`).modal("hide");
				notifMsgSwal(res.status, res.message, res.status);
				return;
			}

			if (callback) return callback(res);

			// If has not callback provided
			// populate record thru $.each method
			if (inObject(res, "data") && !isEmpty(res.data)) {
				$.each(res.data, (key, value) => {
					$(`input[name="${key}"]`).val(value);
				});
			}
		})
		.catch((err) => catchErrMsg(err));
}

/**
 * Shorcut for fetching record via ajax post request
 *
 * @param {string} route            	- The backend delete route/url
 * @param {object} data     			- Object of data to pass - like {id: id}
 * @param {string} title   				- The swal/message title
 * @param {string} table   				- The DataTable table name
 * @param {string} modal   				- Modal name
 * @param {CallableFunction} callback 	- An optional callable to handle the response
 */
function changeRecord(route, data, title, table, modal, callback) {
	let swalMsg = data.id ? `<div>ID #: <strong>${data.id}</strong></div>` : "";
	let change = data.status ? data.status : "change";

	change = strUpper(change);
	swalMsg = `
		${swalMsg} <div>Are you sure you want to <strong>${change}</strong> this record?</div>
	`;

	swalNotifConfirm(
		function () {
			showLoading();
			$.post(route, data)
				.then((res) => {
					closeLoading();

					if (callback) return callback(res);

					// If no callback, code below will be executed
					const message = res.errors ?? res.message;

					notifMsgSwal(res.status, message, res.status);

					if (res.status !== STATUS.ERROR) {
						if (table) refreshDataTable($("#" + table));
						if (modal) $(`#${modal}`).modal("hide");
					}
				})
				.catch((err) => catchErrMsg(err));
		},
		title,
		swalMsg,
		STATUS.WARNING
	);
}

/**
 * Shorcut for deleting record via ajax post request
 *
 * @param {string} route            	- The backend delete route/url
 * @param {object} data     			- Object of data to pass - like {id: id}
 * @param {string} table   				- The DataTable table name
 * @param {CallableFunction} callback 	- An optional callable to handle the response
 */
function deleteRecord(route, data, table, callback) {
	const swalMsg = "delete";

	swalNotifConfirm(
		function () {
			$.post(route, data)
				.then((res) => {
					if (callback) {
						return callback(res);
					}

					if (res.status === STATUS.SUCCESS) {
						if (table) refreshDataTable($("#" + table));
					}

					const message = res.errors ?? res.message;
					notifMsgSwal(res.status, message, res.status);
				})
				.catch((err) => catchErrMsg(err));
		},
		TITLE.WARNING,
		swalMsg,
		STATUS.WARNING
	);
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

/* Set select2 selection */
function destroySelect2(selector) {
	if ($(selector).hasClass("select2-hidden-accessible"))
		$(selector).select2("destroy");
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
		if (Number.isInteger(Number(i)) && (isArray(val) || isObject(val)))
			return { id: val[id], text: strCapitalize(val[text]) };
		return { id: i, text: strCapitalize(val) };
	});
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

/* Check if Array key exist */
function inArray(arr, key) {
	return isArray(arr) ? arr.includes(key) : false;

	/* Another methods */
	// return isArray(arr) ? (arr.indexOf(key) !== -1) : false;
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

/* Source: https://flexiple.com/javascript/javascript-capitalize-first-letter/ */
/* Capitalize first letter of string/word */
function strCapitalize(str) {
	return str.charAt(0).toUpperCase() + str.slice(1);
}

/* Transform string to upper case */
function strUpper(str) {
	return isEmpty(str) ? "" : str.toUpperCase();
}

/* Transform string to lower case */
function strLower(str) {
	return isEmpty(str) ? "" : str.toLowerCase();
}

/* Transform every words to upper case */
function strUpperWords(str) {
	if (isEmpty(str)) return "";

	const arr = str.split(" ");

	for (var i = 0; i < arr.length; i++) {
		arr[i] = arr[i].charAt(0).toUpperCase() + arr[i].slice(1);
	}

	return arr.join(" ");
}

/* Transform every words to lower case */
function strUpperWords(str) {
	if (isEmpty(str)) return "";

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

/**
 * Add a query string in url
 *
 * @param {object} params		the params to add in the url
 * @returns {void}
 */
function addQueryStringInUrl(params) {
	// Get the current query string
	const url = new URLSearchParams(window.location.search);

	// Add or update query parameters
	for (let key in params) {
		url.set(key, params[key]);
	}

	// Reconstruct the URL with the updated query string
	const updatedUrl = url.toString();

	// Update the address bar URL without reloading the page
	const newUrl = `${window.location.pathname}?${updatedUrl}`;
	window.history.pushState({}, "", newUrl);
}

/**
 * Get the query string in url
 *
 * @param {string} param		the specific param to return
 * @returns {object|string}
 */
function getQueryStringInUrl(param) {
	// Get the query string from the current URL
	const queryString = window.location.search;

	if (!queryString) return {};

	// Remove the leading '?' character if present
	const queryStringWithoutQuestionMark = queryString.slice(1);

	// Split the query string into an array of key-value pairs
	const queryParams = queryStringWithoutQuestionMark.split("&");

	// Create an object to store the parameters
	const params = {};

	// Iterate over the key-value pairs and populate the object
	for (const param of queryParams) {
		const [key, value] = param.split("=");
		params[key] = decodeURIComponent(value);
	}

	// Now, return the params
	return param ? params[param] : params;
}

/**
 * Get the query string in url
 *
 * @param {array|string} params		the params to remove
 * @returns {void}
 */
function removeQueryStringInUrl(params) {
	// Get the current query string from the URL
	const queryString = window.location.search;

	if (!queryString) return;

	// Create a URLSearchParams object from the query string
	const searchParams = new URLSearchParams(queryString);

	// Remove a specific query parameter
	if (isArray(params)) {
		for (let index = 0; index < params.length; index++) {
			searchParams.delete(params[index]);
		}
	} else {
		searchParams.delete(params);
	}

	// Generate the new query string
	const newQueryString = searchParams.toString();

	// Create a new URL with the updated query string
	const newUrl = `${window.location.pathname}${
		newQueryString ? `?${newQueryString}` : ""
	}`;

	// Update the URL without reloading the page
	window.history.pushState({}, "", newUrl);

	// Optionally, you can also update the address bar directly
	// window.location.search = newQueryString;
}

/**
 * Format number - eg. 1000 to 1,000
 *
 * @param {integer} number 	the number to format
 * @param {integer} decimal	identifier on how many decimals - default 2
 * @returns {integer}
 */
function numberFormat(number, decimal) {
	return parseFloat(number).toLocaleString("en-US", {
		minimumFractionDigits: decimal || 2,
		maximumFractionDigits: decimal || 2,
	});
}

/**
 * Add decimal to the number
 *
 * @param {integer} number 	the number to format
 * @param {integer} decimal	identifier on how many decimals - default 2
 * @returns {integer}
 */
function numberToFixed(number, decimal = 2) {
	number = parseFloat(number);
	return number.toFixed(decimal);
}

/**
 * Check key in object if exist then return
 *
 * @param {object} obj 	the object to search from
 * @param {string} key	the key to search for
 * @returns {string|null}
 */
function inObjectReturn(obj, key) {
	return inObject(obj, key) ? obj[key] : null;
}

/**
 * Parse number or float string
 *
 * @param {string} string 	Float/number string
 * @returns {string|number}
 */
function parseNumber(string) {
	// Convert if not string to avoid error
	string = isString(string) ? string : string.toString();
	return parseFloat(string.replace(/,/g, ""));
}

/**
 * Check if page is reloaded
 *
 * @returns {bool}
 */
function isPageReloaded() {
	return performance.navigation.type === 1;
}

/**
 * Check if there's a query paramaters
 * Intended if from mail notif
 *
 * @returns {bool}
 */
function showItemsIfRedirectedFromMail() {
	const query = getQueryStringInUrl();

	if (!isEmpty(query)) {
		if (query.mail && !isPageReloaded())
			if (isFunctionExist(view)) view(query.id);
	}
}
