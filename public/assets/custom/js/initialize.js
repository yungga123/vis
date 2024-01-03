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
			"X-Requested-With": "XMLHttpRequest",
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

	$(".modal").on("shown.bs.modal", function (event) {
		if (!$("body").hasClass("modal-open")) {
			$("body")
				.addClass("modal-open")
				.css({ height: "auto", "padding-right": "14px" });
		}
	});

	// Initialize toastr
	if (isToastrLoaded()) {
		initToastr();
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

	// Collapsible accordion
	if ($(".collapse").length > 0) $(".collapse").collapse();
});

/* Check if is toastr is loaded */
function isToastrLoaded() {
	return window.toastr != undefined;
}

/* Check if is swal is loaded */
function isSwalLoaded() {
	return window.Swal != undefined;
}

/* Check if is inputmask is loaded */
function isInputmaskLoaded() {
	return $.fn.inputmask != undefined;
}

/*
 * Initializations and others
 */

function initToastr(options) {
	if (isEmpty(options)) {
		options = {
			closeButton: true,
			debug: false,
			newestOnTop: true,
			progressBar: true,
			positionClass: "toast-top-right",
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
	}
	toastr.options = options;
}

/**
 * Load dataTable data
 * @param {string} table    - id of the table
 * @param {string} route    - url path to get data
 * @param {string} type     - type of request method (GET, POST)
 * @param {object} options  - other options for the dataTable
 */
function loadDataTable(table, route, type, options = {}, destroy = false) {
	/* For responsive */
	function dtResponsiveConfig() {
		return {
			details: {
				renderer: function (api, rowIdx, columns) {
					const data = dtFormatHiddenData(columns);
					return data ? $("<div/>").append(data) : false;
				},
			},
		};
	}

	function dtFormatHiddenData(columns) {
		let row = "";
		if (!isEmpty(columns)) {
			$.each(columns, (i, col) => {
				if (col.hidden) {
					const data = isEmpty(col.data) ? "N/A" : col.data;
					row += `
						<tr data-dt-row="${col.rowIndex}" data-dt-column="${col.columnIndex}">
							<td class="text-bold">${col.title}: </td>
							<td>${data}</td>
						</tr>
					`;
				}
			});

			row = `<table class="table table-sm">${row}</table>`;
		}

		return row;
	}
	/* -- end */

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
					// columns: ":visible",
				},
				className: "mr-1 rounded btn-outline-success",
				text: "<i class='fas fa-file-excel'></i> Excel",
			},
			{
				extend: "colvis",
				className: "mr-1 rounded btn-outline-primary",
				text: "<i class='fas fa-eye'></i> Column Visibility",
			},
		],
		responsive = inObject(options, "responsive")
			? options.responsive
			: dtResponsiveConfig();

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
		responsive: responsive,
		processing: true,
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
			type: type || METHOD.GET,
			data: function (d) {
				if (inObject(options, "params") && !isEmpty(options.params)) {
					d.params = options.params;
				}
			},
		},
		createdRow: function (row, data, dataIndex) {
			if (data.length > 1) {
				for (var i = 0; i < data.length; i++) {
					const condition = responsive
						? i > 0 && isEmpty(data[i])
						: isEmpty(data[i]);

					if (condition) {
						$(`td:eq(${i})`, row).text("N/A");
					}
				}
			}
		},
		initComplete: function (settings, json) {
			// if ($(".dataTables_wrapper").parent().closest(".modal").length == 0)
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
 * @param {function} callback  	- the callback function after selecting an item
 * @param {object} options  	- the options (data) to be passed to the backend
 * @param {number} perPage  	- the length of options to display - default set to 10
 * @return void
 */
function select2AjaxInit(
	selector,
	placeholder,
	route,
	text,
	callback,
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
			if (isFunction(callback)) callback(data);
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
	if (isSelect2Initialized(select)) $(select).select2("destroy");

	select2Init(select, placeholder, newData);

	$(select).val("").trigger("change");
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

/**
 * Initialize bootstrap switch
 *
 * @param {string} selector 			- id or class name with '#' or '.' identifier
 * @param {object} options    			- options for date range picker
 */
function initBootstrapSwitch(selector, options, state) {
	const elem = $(selector);

	if (elem.length > 1) {
		elem.each(function () {
			if (!isEmpty(options)) {
				$(this).bootstrapSwitch(options);
			}
			$(this).bootstrapSwitch("state", $(this).prop("checked"));
		});
	} else elem.bootstrapSwitch("state", state || false);
}
