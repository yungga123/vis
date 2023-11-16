var table, modal, form, elems;

$(document).ready(function () {
	table = "tasklead_table";
	modal = "modal_tasklead";
	form = "form_tasklead";
	elems = [
		"employee_id",
		"quarter",
		"status",
		"customer_type",
		"existing_customer",
		"customer_id",
		"branch_id",
		"project",
		"project_amount",
		"quotation_num",
		"forecast_close_date",
		"min_forecast_date",
		"max_forecast_date",
		"remark_next_step",
		"close_deal_date",
		"project_start_date",
		"project_finish_date",
		"tasklead_type",
	];

	$("#btn_add_record").on("click", function () {
		$(`#${modal}`).modal("show");
		$(`#${modal}`).removeClass("edit").addClass("add");
		$(`#${modal} .modal-title`).text("Add New Tasklead");
		$(`#${form}`)[0].reset();
		$("#tasklead_id").val("");

		hideElements();

		let elements = [
			"status",
			"customer_type",
			"existing_customer",
			"customer_id",
			"branch_id",
			"remark_next_step",
			"quarter",
		];

		$.each(elements, function (key, value) {
			$("." + value).attr("hidden", false);
		});

		$("#status").val("10.00");
		$(".status_text").val("10% -- Identified");
		$("option[value='10.00']", "#change_tasklead").attr("disabled", true);
		$("option[value='30.00']", "#change_tasklead").attr("disabled", true);
		$("option[value='50.00']", "#change_tasklead").attr("disabled", true);
		$("option[value='70.00']", "#change_tasklead").attr("disabled", true);
		$("option[value='90.00']", "#change_tasklead").attr("disabled", true);

		clearAlertInForm(elems);
	});

	/* Load dataTable */
	loadDataTable(table, router.tasklead.list, METHOD.POST);

	/* Filters */
	select2Init("#filter_status");
	select2Init("#filter_client_type");
	select2Init("#filter_quarter");

	/* Form for saving item */
	formSubmit($("#" + form), "continue", function (res, self) {
		const message = res.errors ?? res.message;

		if (res.status !== STATUS.ERROR) {
			self[0].reset();
			refreshDataTable($("#" + table));
			notifMsgSwal(res.status, message, res.status);
			$(`#${modal}`).modal("hide");
		}

		showAlertInForm(elems, message, res.status);
		resetCustomer();
	});

	// On Change in Existing Customer
	$("#existing_customer").change(function () {
		let customer_type = $("#customer_type").val();
		let existing_customer = $(this).val();

		$("#branch_id").empty();
		$("#branch_id").attr("disabled", true);

		if (existing_customer == 1 && customer_type == "Commercial") {
			appendCustomer("commercial", 0);
		} else if (existing_customer == 0 && customer_type == "Commercial") {
			appendCustomer("commercial", 1);
		} else if (existing_customer == 1 && customer_type == "Residential") {
			appendCustomer("residential", 0);
		} else if (existing_customer == 0 && customer_type == "Residential") {
			appendCustomer("residential", 1);
		}
	});

	$("#customer_id").change(function () {
		let id = $(this).val();
		let url = "get_customervtbranch_url";
		let customer_type = $("#customer_type").val();

		if (customer_type == "Commercial") {
			appendBranch(url, id);
		} else {
			$("#branch_id").empty();
			$("#branch_id").attr("disabled", true);
		}
	});

	$("#customer_type").change(function () {
		$("#branch_id").empty();
		$("#branch_id").attr("disabled", true);
		$("#existing_customer").val("");
		$("#customer_id").empty();
		$("#customer_id").attr("disabled", true);
	});

	$("#quotation_type").on("change", function () {
		$("#quotation_num").val(
			$(this).val() +
				$("#get_quotation_num").val() +
				$("#tasklead_id").val() +
				$("#quotation_color").val()
		);
		$("#tasklead_type").val($("#quotation_type option:selected").text());
	});

	$("#quotation_color").on("change", function () {
		$("#quotation_num").val(
			$("#quotation_type").val() +
				$("#get_quotation_num").val() +
				$("#tasklead_id").val() +
				$(this).val()
		);
	});

	$("#change_tasklead").on("change", function () {
		hideElements();
		$("#quotation_type").attr("required", false);
		$("#quotation_color").attr("required", false);
		if ($(this).val() == "10.00") {
			$("#status").val("10.00");
			$(".tasklead").prepend(
				"<p>You are updating this lead to IDENTIFIED(10%). Press Save to update.</p>"
			);
			$(`#${modal} .modal-title`).text("Update tasklead to IDENTIFIED(10%)");
		}

		if ($(this).val() == "30.00") {
			$(".project").attr("hidden", false);
			$(".remark_next_step").attr("hidden", false);
			$("#status").val("30.00");
			$(`#${modal} .modal-title`).text("Update tasklead to QUALIFIED(30%)");
		}

		if ($(this).val() == "50.00") {
			$("#quotation_type").attr("required", true);
			$("#quotation_color").attr("required", true);
			$(".quotation_num").attr("hidden", false);

			$(".remark_next_step").attr("hidden", false);
			$("#status").val("50.00");
			$(`#${modal} .modal-title`).text(
				"Update tasklead to DEVELOPED SOLUTION(50%)"
			);
		}

		if ($(this).val() == "70.00") {
			$(".remark_next_step").attr("hidden", false);
			$(".project_amount").attr("hidden", false);
			$(".forecast_close_date").attr("hidden", false);
			$("#status").val("70.00");
			$(`#${modal} .modal-title`).text("Update tasklead to EVALUATION(70%)");
		}

		if ($(this).val() == "90.00") {
			$(".remark_next_step").attr("hidden", false);
			$(".project_amount").attr("hidden", false);
			$("#status").val("90.00");
			$(`#${modal} .modal-title`).text("Update tasklead to NEGOTIATION(90%)");
		}
	});

	$("#customer_id").select2({
		theme: "bootstrap4",
		dropdownParent: `#${modal}`,
	});
});

/* For filtering and reseting */
function filterData(reset = false) {
	const status = getSelect2Selection("#filter_status");
	const client_type = getSelect2Selection("#filter_client_type");
	const quarter = getSelect2Selection("#filter_quarter");
	const params = {
		status: status,
		client_type: client_type,
		quarter: quarter,
	};
	const condition =
		!isEmpty(status) || !isEmpty(client_type) || !isEmpty(quarter);

	filterParam(
		router.tasklead.list,
		table,
		params,
		condition,
		() => {
			clearSelect2Selection("#filter_status");
			clearSelect2Selection("#filter_client_type");
			clearSelect2Selection("#filter_quarter");
		},
		reset
	);
}

/* Get item details */
function edit(id) {
	$(`#${modal}`).removeClass("add").addClass("edit");
	$(`#${modal} .modal-title`).text("Edit Item");
	$("#tasklead_id").val(id);

	clearAlertInForm(elems);
	showLoading();

	$.post(router.tasklead.edit, { id: id })
		.then((res) => {
			closeLoading();

			if (res.status === STATUS.SUCCESS) {
				if (inObject(res, "data") && !isEmpty(res.data)) {
					hideElements();
					$("#change_tasklead").val("");
					$("#quotation_type").attr("required", false);
					$("#quotation_color").attr("required", false);
					$.each(res.data, (key, value) => {
						if (value == "0000-00-00" || value == "0.00") {
							value = null;
						}

						$("#" + key).val(value);

						if (key == "customer_id") {
							$("#customer_id").attr("disabled", false);
							$("#customer_id").empty();
							$("#customer_id").append(
								$("<option>", {
									value: value,
									text: value,
								})
							);
							$(".customer_id").attr("hidden", true);
						}

						if (key == "branch_id") {
							$("#branch_id").attr("disabled", false);
							$("#branch_id").empty();
							$("#branch_id").append(
								$("<option>", {
									value: value,
									text: value,
								})
							);
							$(".branch_id").attr("hidden", true);
						}

						if (key == "status" && value == "10.00") {
							$(".project").attr("hidden", false);
							$(".remark_next_step").attr("hidden", false);
							$("#status").val("30.00");
							$(`#${modal} .modal-title`).text(
								"Update tasklead to QUALIFIED(30%)"
							);
							$("option[value='10.00']", "#change_tasklead").attr(
								"disabled",
								true
							);
							$("option[value='30.00']", "#change_tasklead").attr(
								"disabled",
								true
							);
							$("option[value='50.00']", "#change_tasklead").attr(
								"disabled",
								true
							);
							$("option[value='70.00']", "#change_tasklead").attr(
								"disabled",
								true
							);
							$("option[value='90.00']", "#change_tasklead").attr(
								"disabled",
								true
							);
						}

						if (key == "status" && value == "30.00") {
							$("#quotation_type").attr("required", true);
							$("#quotation_color").attr("required", true);
							$(".quotation_num").attr("hidden", false);

							$(".remark_next_step").attr("hidden", false);
							$("#status").val("50.00");
							$(`#${modal} .modal-title`).text(
								"Update tasklead to DEVELOPED SOLUTION(50%)"
							);
							$("option[value='10.00']", "#change_tasklead").attr(
								"disabled",
								false
							);
							$("option[value='30.00']", "#change_tasklead").attr(
								"disabled",
								true
							);
							$("option[value='50.00']", "#change_tasklead").attr(
								"disabled",
								true
							);
							$("option[value='70.00']", "#change_tasklead").attr(
								"disabled",
								true
							);
							$("option[value='90.00']", "#change_tasklead").attr(
								"disabled",
								true
							);
						}

						if (key == "status" && value == "50.00") {
							$(".remark_next_step").attr("hidden", false);
							$(".project_amount").attr("hidden", false);
							$(".forecast_close_date").attr("hidden", false);
							$("#status").val("70.00");
							$(`#${modal} .modal-title`).text(
								"Update tasklead to EVALUATION(70%)"
							);
							$("option[value='10.00']", "#change_tasklead").attr(
								"disabled",
								false
							);
							$("option[value='30.00']", "#change_tasklead").attr(
								"disabled",
								false
							);
							$("option[value='50.00']", "#change_tasklead").attr(
								"disabled",
								true
							);
							$("option[value='70.00']", "#change_tasklead").attr(
								"disabled",
								true
							);
							$("option[value='90.00']", "#change_tasklead").attr(
								"disabled",
								true
							);
						}

						if (key == "status" && value == "70.00") {
							$(".remark_next_step").attr("hidden", false);
							$(".project_amount").attr("hidden", false);
							$("#status").val("90.00");
							$(`#${modal} .modal-title`).text(
								"Update tasklead to NEGOTIATION(90%)"
							);
							$("option[value='10.00']", "#change_tasklead").attr(
								"disabled",
								false
							);
							$("option[value='30.00']", "#change_tasklead").attr(
								"disabled",
								false
							);
							$("option[value='50.00']", "#change_tasklead").attr(
								"disabled",
								false
							);
							$("option[value='70.00']", "#change_tasklead").attr(
								"disabled",
								true
							);
							$("option[value='90.00']", "#change_tasklead").attr(
								"disabled",
								true
							);
						}

						if (key == "status" && value == "90.00") {
							$(".close_deal_date").attr("hidden", false);
							$(".project_start_date").attr("hidden", false);
							$(".project_finish_date").attr("hidden", false);
							$(".remark_next_step").attr("hidden", false);
							$("#status").val("100.00");
							$(`#${modal} .modal-title`).text(
								"Update tasklead to BOOKED(100%)"
							);
							$("option[value='10.00']", "#change_tasklead").attr(
								"disabled",
								false
							);
							$("option[value='30.00']", "#change_tasklead").attr(
								"disabled",
								false
							);
							$("option[value='50.00']", "#change_tasklead").attr(
								"disabled",
								false
							);
							$("option[value='70.00']", "#change_tasklead").attr(
								"disabled",
								false
							);
							$("option[value='90.00']", "#change_tasklead").attr(
								"disabled",
								true
							);
						}
					});

					if ($("#status").val() == "50.00") {
						$("#quotation_num").val(
							$("#get_quotation_num").val() + $("#tasklead_id").val()
						);
					}
				}
			} else {
				$(`#${modal}`).modal("hide");
				notifMsgSwal(res.status, res.message, res.status);
			}
		})
		.catch((err) => catchErrMsg(err));
}

function remove(id) {
	const swalMsg = "delete";
	swalNotifConfirm(
		function () {
			$.post(router.tasklead.delete, { id: id })
				.then((res) => {
					const message = res.errors ?? res.message;

					refreshDataTable();
					notifMsgSwal(res.status, message, res.status);
				})
				.catch((err) => catchErrMsg(err));
		},
		TITLE.WARNING,
		swalMsg,
		STATUS.WARNING
	);
}

function appendCustomer(id, forecast) {
	const route =
		id === "residential"
			? router.tasklead.customer_residential
			: router.tasklead.customer_commercial;

	$.ajax({
		url: route + "?forecast=" + forecast,
		dataType: "json",
		type: "get",
		success: function (response) {
			$("#customer_id").removeAttr("disabled");
			$("#customer_id").empty();
			$("#customer_id").append(
				$("<option>", {
					value: "",
					text: "---Please Select---",
				})
			);
			$.each(response.data, (key, value) => {
				$("#customer_id").append(
					$("<option>", {
						value: value["id"],
						text: value["id"] + " --- " + value["name"],
					})
				);
			});
		},
		error: function () {
			alert("Errors Occured");
		},
	});
}

function appendBranch(url, id) {
	$.ajax({
		url: router.tasklead.customer_branch + "?id=" + id,
		dataType: "json",
		type: "get",
		success: function (response) {
			$("#branch_id").removeAttr("disabled");
			$("#branch_id").empty();
			$("#branch_id").append(
				$("<option>", {
					value: "",
					text: "---Please Select---",
				})
			);
			$.each(response.data, (key, value) => {
				$("#branch_id").append(
					$("<option>", {
						value: value["id"],
						text: value["id"] + " --- " + value["branch_name"],
					})
				);
			});
		},
		error: function () {
			alert("Errors Occured");
		},
	});
}

function resetCustomer() {
	$("#customer_type").val("");
	$("#existing_customer").val("");
	$("#customer_id").val("");
	$("#customer_id").empty();
	$("#customer_id").attr("disabled", true);
	$("#branch_id").empty();
	$("#branch_id").attr("disabled", true);
	$("#branch_id").val("");
}

function hideElements() {
	$.each(elems, function (key, value) {
		$("." + value).attr("hidden", true);
	});
	$(".tasklead").empty();
}

function showElements() {
	$.each(elems, function (key, value) {
		$("." + value).attr("hidden", false);
	});
}
